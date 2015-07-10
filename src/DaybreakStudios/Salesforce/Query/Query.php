<?php
	namespace DaybreakStudios\Salesforce\Query;

	use \BadMethodCallException;

	use DaybreakStudios\Salesforce\Client;

	class Query {
		private $parameters = [];
		private $soql = null;

		private $client;

		public function __construct(Client $client = null) {
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

			$params = [];

			foreach ($this->parameters as $k => $v)
				if (strpos($k, ':') !== 0)
					$params[sprintf(':%s', $k)] = $v;
				else
					$params[$k] = $v;

			return $this->client->query($this->getSoql(), $params);
		}

		public function getResult() {
			return $this->execute();
		}

		public function getSingleScalarResult() {
			$result = $this->execute();

			if ($result->size > 1)
				throw new BadMethodCallException('There was more than one row in the result set');

			$record = $result->records[0];

			if (isset($record->fields)) {
				$fields = get_object_vars($recrd->fields);

				if (sizeof($fields) > 1)
					throw new BadMethodCallException('There was more than one column in the result set');
				else
					return current($fields);
			} else if (isset($record->Id))
				return $record->Id;

			throw new BadMethodCallException('There were no columns in the result set');
		}

		public function getOneOrNullResult() {
			$result = $this->execute();

			if ($result->size > 1)
				throw new BadMethodCallException('There was more than one row in the result set');
			else if ($result->size === 0)
				return null;

			return $result->records[0];
		}
	}
?>