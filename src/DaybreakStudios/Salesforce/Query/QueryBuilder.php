<?php
	namespace DaybreakStudios\Salesforce\Query;

	use \BadMethodCallException;
	use \InvalidArgumentException;

	use DaybreakStudios\Common\Collection\EnumMap;

	use DaybreakStudios\Salesforce\Client;
	use DaybreakStudios\Salesforce\Query\Expr\AndPart;
	use DaybreakStudios\Salesforce\Query\Expr\Expr;
	use DaybreakStudios\Salesforce\Query\Expr\OrPart;
	use DaybreakStudios\Salesforce\Query\Expr\Part;

	class QueryBuilder {
		private $query;
		private $parts;
		private $expr;

		public function __construct(Client $client = null) {
			$this->query = new Query($client);
			$this->parts = new EnumMap('DaybreakStudios\\Salesforce\\Query\\PartType');
			$this->expr = new Expr();
		}

		public function getQuery() {
			if (!$this->getPart(PartType::SELECT()) || !$this->getPart(PartType::FROM()) || !$this->getPart(PartType::WHERE()))
				throw new BadMethodCallException('At miniumum, you must have a SELECT part, FROM part, and WHERE part');

			$soql = '';

			foreach (PartType::values() as $type) {
				$parts = $this->parts->get($type);

				if ($parts === null)
					continue;

				$soql .= sprintf(' %s ', $type->getSoql());

				if (!is_array($parts))
					$parts = [ $parts ];

				if ($type === PartType::SELECT()) {
					$soql .= implode(', ', $parts);

					continue;
				}

				$assembled = '';

				foreach ($parts as $p)
					if ($p instanceof Part)
						$assembled .= strlen($assembled) > 0 ? $p->assemble() : $p->getValue();
					else
						$assembled .= $p;

				$soql .= $assembled;
			}

			return $this->query->setSoql(trim($soql));
		}

		public function expr() {
			return $this->expr;
		}

		public function add(PartType $type, $part, $append = false) {
			if ($append && !$type->isAppendSupported())
				throw new InvalidArgumentException('PartType::' . $type . ' does not support appending parts');

			if ($append) {
				$parts = [];

				if ($this->parts->containsKey($type))
					$parts = $this->parts->get($type);

				if (!is_array($parts))
					$parts = [ $parts ];

				$parts[] = $part;

				$this->parts->put($type, $parts);
			} else
				$this->parts->put($type, $part);

			return $this;
		}

		public function getPart(PartType $type) {
			return $this->parts->get($type);
		}

		public function select(/* ... $select */) {
			$select = func_get_args();

			foreach ($select as $s)
				$this->add(PartType::SELECT(), $s, true);

			return $this;
		}

		public function from($from) {
			return $this->add(PartType::FROM(), $from, false);
		}

		public function where($where) {
			return $this->add(PartType::WHERE(), new Part($where), false);
		}

		public function andWhere($where) {
			return $this->add(PartType::WHERE(), new AndPart($where), true);
		}

		public function orWhere($where) {
			return $this->add(PartType::WHERE(), new OrPart($where), true);
		}

		public function groupBy($groupBy) {
			return $this->add(PartType::GROUPBY(), $groupBy, false);
		}

		public function addGroupBy($groupBy) {
			return $this->add(PartType::GROUPBY(), $groupBy, true);
		}

		public function having($having) {
			return $this->add(PartType::HAVING(), new Part($having), false);
		}

		public function andHaving($having) {
			return $this->add(PartType::HAVING(), new AndPart($having), true);
		}

		public function orHaving($having) {
			return $this->add(PartType::HAVING(), new OrPart($having), true);
		}

		public function orderBy($orderBy) {
			return $this->add(PartType::ORDERBY(), $orderBy, false);
		}

		public function addOrderBy($orderBy) {
			return $this->add(PartType::ORDERBY(), $orderBy, true);
		}

		public function getMaxResults() {
			return $this->getPart(PartType::LIMIT());
		}

		public function setMaxResults($limit) {
			return $this->add(PartType::LIMIT(), $limit, false);
		}

		public function getFirstResult() {
			return $this->getPart(PartType::OFFSET());
		}

		public function setFirstResult($offset) {
			return $this->add(PartType::OFFSET(), $offset, false);
		}

		public function setParameter($key, $value) {
			$this->query->setParameter($key, $value);

			return $this;
		}

		public function setParameters(array $parameters) {
			$this->query->setParameters($parameters);

			return $this;
		}

		public function reset($type = null) {
			if ($type === null)
				$this->parts->clear();
			else if ($type instanceof PartType)
				$this->parts->remove($type);
			else if (is_array($type)) {
				foreach ($type as $type)
					if ($type instanceof PartType)
						$this->reset($type);
			} else
				throw new InvalidArgumentException('$type must be a PartType, null, or an array of PartType objects');

			return $this;
		}
	}
?>