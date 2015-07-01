<?php
	namespace DaybreakStudios\Salesforce\Query;

	use \BadMethodCallException;

	use DaybreakStudios\Salesforce\Client;

	class Query {
		private $parameters = [];
		private $soql = null;

		private $client;

		public function __construct(Client $client) {
			$this->client = $client;
		}

		public function getClient() {
			return $this->client;
		}

		public function setClient($client) {
			$this->client = $client;

			return $this;
		}

		public function getSoql() {
			return $this->soql;
		}

		public function setSoql($soql) {
			$this->soql = $soql;

			return $this;
		}

		public function getParameter($key) {
			if (isset($this->parameters[$key]))
				return $this->parameters[$key];

			return null;
		}

		public function setParameter($key, $value) {
			$this->parameters[$key] = $value;

			return $this;
		}

		public function getParameters() {
			return $this->parameters;
		}

		public function setParameters(array $parameters) {
			foreach ($parameters as $k => $v)
				$this->setParameters($k, $v);

			return $this;
		}

		public function execute() {
			if (empty($this->soql))
				throw new BadMethodCallException('Cannot execute an empty statement');
			else if ($this->client === null)
				throw new BadMethodCallException('Cannot execute without a client connection');

			$result = $this->client->query($this->getSoql());

			if ($result->size === 0)
				return $result;

			foreach ($result as $record)
				if (!$record->success) {
					$error = [];

					foreach ($record->errors as $e)
						$error[] = sprintf('[%s] %s', $e->statusCode, $e->message);

					throw new QueryException(implode('; ', $error));
				}

			return $result;
		}
	}
?>