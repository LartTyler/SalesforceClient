<?php
	namespace DaybreakStudios\Salesforce\Query\Expr;

	use DaybreakStudios\Salesforce\Query\QueryBuilder;

	class Expr {
		public function andX(/* ... $wheres */) {
			$wheres = func_get_args();
			$where = null;

			for ($i = 0, $ii = sizeof($wheres); $i < $ii; $part = new AndPart($wheres[$i++]))
				if ($where === null)
					$where = $part->getValue();
				else
					$where .= $part->assemble();

			if ($where === null)
				throw new InvalidArgumentException('Expr#andX requires at least one argument');

			return sprintf(' (%s) ', $where);
		}

		public function orX(/* ... $wheres */) {
			$wheres = func_get_args();
			$where = null;

			for ($i = 0, $ii = sizeof($wheres); $i < $ii; $part = new OrPart($wheres[$i++]))
				if ($where === null)
					$where = $part->getValue();
				else
					$where .= $part->assemble();

			if ($where === null)
				throw new InvalidArgumentException('Expr#orX requires at least one argument');

			return sprintf(' (%s) ', $where);
		}
	}
?>