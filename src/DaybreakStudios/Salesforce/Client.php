<?php
	namespace DaybreakStudios\Salesforce;

	use \Exception;
	use \InvalidArgumentException;

	use \SforcePartnerClient;
	use \SObject;

	use DaybreakStudios\Salesforce\DateTime\SalesforceDateTime;
	use DaybreakStudios\Salesforce\DateTime\SalesforceDateTimeWrapper;
	use DaybreakStudios\Salesforce\Conversion\ConversionHandler;
	use DaybreakStudios\Salesforce\Query\QueryBuilder;

	use Symfony\Component\HttpFoundation\Session\Session;

	class Client {
		const BATCH_LIMIT = 200;

		const SESSION_ENDPOINT = 'dbstudios.salesforce.client.endpoint';
		const SESSION_ID = 'dbstudios.salesforce.client.sfid';

		private $client;
		private $converter;

		public function __construct($username, $token, $wsdl, Session $session = null) {
			$this->client = new SforcePartnerClient();
			$this->client->createConnection($wsdl);

			$refresh = true;

			if ($session !== null && $session->has(self::SESSION_ENDPOINT) && $session->has(self::SESSION_ID)) {
				$refresh = false;

				$this->client->setEndpoint($session->get(self::SESSION_ENDPOINT));
				$this->client->setSessionHeader($session->get(self::SESSION_ID));

				try {
					$this->client->getUserInfo();
				} catch (Exception $e) {
					$refresh = true;
				}
			}

			if ($refresh) {
				$this->client->login($username, $token);

				if ($session !== null) {
					$session->set(self::SESSION_ENDPOINT, $this->client->getLocation());
					$session->set(self::SESSION_ID, $this->client->getSessionId());
				}
			}

			$this->converter = new ConversionHandler();
		}

		public function createQueryBuilder() {
			return new QueryBuilder($this);
		}

		public function getConversionHandler() {
			return $this->converter;
		}

		public function getSalesforceClient() {
			return $this->client;
		}

		public function processParameters($query, array $args) {
			foreach ($args as $k => $arg) {
				if (strpos($k, ':') !== 0)
					$k = ':' . $k;

				$query = str_replace($k, $this->converter->convert($arg), $query);
			}

			return $query;
		}

		public function query($query, array $args = array()) {
			$query = $this->processParameters($query, $args);
			$res = $this->client->query($query);

			if ($res->size)
				foreach ($res as $i => $record)
					$res->records[$i] = ClientUtil::transmute($record, $this->getConversionHandler());

			return $res;
		}

		public function create($objects, AssignmentRuleHeader $aheader = null, MruHeader $mheader = null) {
			if (!is_array($objects))
				$objects = [ $objects ];

			$results = [];

			foreach (array_chunk(ClientUtil::clean($objects), self::BATCH_LIMIT) as $chunk)
				$results = array_merge($results, $this->client->create($chunk, $aheader, $mheader));

			return $results;
		}

		public function update($objects, AssignmentRuleHeader $aheader = null, MruHeader $mheader = null) {
			if (!is_array($objects))
				$objects = [ $objects ];

			$results = [];

			foreach (array_chunk(ClientUtil::clean($objects), self::BATCH_LIMIT) as $chunk)
				$results = array_merge($results, $this->client->update($chunk, $aheader, $mheader));

			return $results;
		}

		public function delete($ids, AssignmentRuleHeader $aheader = null, MruHeader $mheader = null) {
			if (!is_array($ids))
				$ids = [ $ids ];

			$results = [];

			foreach (array_chunk($ids, self::BATCH_LIMIT) as $chunk)
				$results = array_merge($results, $this->client->delete($chunk, $aheader, $mheader));

			return $results;
		}

		public function __call($name, $arguments) {
			return call_user_func_array([
				$this->client,
				$name,
			], $arguments);
		}
	}
?>