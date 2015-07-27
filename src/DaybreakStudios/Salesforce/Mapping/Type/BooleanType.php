<?php
	namespace DaybreakStudios\Salesforce\Mapping\Type;

	use \InvalidArgumentException;

	class BooleanType implements Type {
		public function isValid($value) {
			return is_bool($value) || is_integer($value) && $value >= 0 && $value <= 1;
		}

		public function isParseable($value) {
			return $this->intCheck($value) || $this->boolStringCheck($value);
		}

		public function format($value) {
			if (!is_bool($value) && !is_integer($value) && $value >= 0 && $value <= 1)
				throw new InvalidArgumentException('$value must be a boolean, or the integers 1 or 0');

			return $value ? 'true' : 'false';
		}

		public function parse($value) {
			if (is_numeric($value))
				return !!(int)$value;

			return strtolower($value) === 'true' ? true : false;
		}

		private function intCheck($v) {
			return is_numeric($v) && strpos($v, '.') && (int)$v >= 0 && (int)$v <= 1;
		}

		private function boolStringCheck($v) {
			$v = strtolower($v);

			return $v === 'true' || $v === 'false';
		}
	}
?>