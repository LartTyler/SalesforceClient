<?php
	namespace DaybreakStudios\Salesforce;

	use \Exception;
	use \InvalidArgumentException;

	use \SforcePartnerClient;
	use \SObject;

	use DaybreakStudios\Salesforce\DateTime\SalesforceDateTime;
	use DaybreakStudios\Salesforce\DateTime\SalesforceDateTimeWrapper;
	use DaybreakStudios\Salesforce\Conversion\ArrayConverter;
	use DaybreakStudios\Salesforce\Conversion\BooleanConverter;
	use DaybreakStudios\Salesforce\Conversion\DateTimeConverter;
	use DaybreakStudios\Salesforce\Conversion\NullConverter;
	use DaybreakStudios\Salesforce\Conversion\StringConverter;
	use DaybreakStudios\Salesforce\Conversion\IntegerConverter;
	use DaybreakStudios\Salesforce\Conversion\FloatConverter;

	class Client {
		const BATCH_LIMIT = 200;

		const DATE_FORMAT = 'Y-m-d';
		const DATETIME_FORMAT = 'Y-m-d\\TH:i:s.000\\Z';

		const SESSION_ENDPOINT = 'dbstudios.salesforce.client.endpoint';
		const SESSION_ID = 'dbstudios.salesforce.client.sfid';

		private $client;
		private $converters;

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

			$this->converters = [
				100 => new ArrayConverter(),
				150 => new BooleanConverter(),
				200 => new DateTimeConverter(),
				250 => new NullConverter(),
				300 => new StringConverter(),
				350 => new IntegerConverter(),
				400 => new FloatConverter(),
			];
		}

		public function getConverters() {
			return $this->converters;
		}

		public function addConverter(Converter $converter, $priority = null) {
			if ($priority === null) {
				$keys = array_keys($this->converters);

				if (sizeof($keys) > 0)
					sort($keys);
				else
					$keys = [ 0 ];

				$priority = $keys[sizeof($keys) - 1] + 50;
			}

			$this->converters[(int)$priority] = $converter;

			ksort($this->converters);
		}

		public function removeConverter($ident) {
			if (is_integer($ident) && isset($this->converters[$ident]))
				unset($this->converters[$ident]);
			else if ($ident instanceof Converter)
				foreach ($this->converters as $k => $converter)
					if ($converter === $ident) {
						unset($this->converters[$k]);

						break;
					}
			else
				throw new InvalidArgumentException('The value you provided for $ident is not a valid identifier');
		}

		public function getSalesforceClient() {
			return $this->client;
		}

		public function query($query, array $args = array()) {
			foreach ($args as $k => $arg)
				foreach ($this->converters as $converter)
					if ($converter->handles($arg))
						$args[$k] = $converter->convert($arg);

			$res = $this->client->query(str_replace(array_keys($args), $args, $query));

			if ($res->size)
				foreach ($res as $i => $record)
					$res->records[$i] = $this->transmute($record);

			return $res;
		}

		public function create($objects, AssignmentRuleHeader $aheader = null, MruHeader $mheader = null) {
			$results = [];

			foreach (array_chunk($this->clean($objects), self::BATCH_LIMIT) as $chunk)
				$results = array_merge($results, $this->client->create($chunk, $aheader, $mheader));

			return $results;
		}

		public function update($objects, AssignmentRuleHeader $aheader = null, MruHeader $mheader = null) {
			$results = [];

			foreach (array_chunk($this->clean($objects), self::BATCH_LIMIT) as $chunk)
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

		public function clean($objects) {
			if (!is_array($objects))
				$objects = [ $objects ];

			foreach ($objects as $obj) {
				if (!isset($obj->fields))
					continue;

				$fields = $obj->fields;

				if (is_object($fields))
					$fields = get_object_vars($fields);

				foreach ($fields as $k => $v) {
					foreach ($this->converters as $converter)
						if ($converter->handles($v)) {
							$v = $converter->convert($v);

							break;
						}

					$fields[$k] = $v;
				}

				if (is_object($obj->fields))
					$fields = (object)$fields;

				$obj->fields = $fields;
			}

			return $objects;
		}

		private function transmute(SObject $record) {
			if (isset($record->Id))
				$record->Id = substr($record->Id, 0, 15);

			if (!isset($record->fields))
				return $record;

			$fields = $record->fields;

			if (is_object($fields))
				$fields = get_object_vars($fields);

			foreach ($fields as $k => $v) {
				if (strpos($k, 'Id') === strlen($k) - 2) {
					$fields[$k] = substr($v, 0, 15);

					continue;
				}

				foreach ($this->converters as $converter)
					try {
						$fields[$k] = $converter->revert($v);
					} catch (InvalidArgumentException $e) {}
			}

			if (is_object($record->fields))
				$fields = (object)$fields;

			$record->fields = $fields;

			return $record;
		}

		public function __call($name, $arguments) {
			return call_user_func_array([
				$this->client,
				$name,
			], $arguments);
		}
	}
?>