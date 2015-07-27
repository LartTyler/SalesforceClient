<?php
	namespace DaybreakStudios\Salesforce\Mapping\Type;

	class IntegerType extends Type {
		private $unsigned;

		public function __construct($unsigned = false) {
			$this->unsigned = $unsigned;
		}

		public function isValid($value) {
			if (!is_integer($value))
				return false;
			else if ($this->unsigned)
				return $value >= 0;

			return true;
		}

		public function isParseable($value) {
			if (!is_numeric($value) || strpos($value, '.') !== false)
				return false;
			else if ($this->unsigned)
				return (int)$value >= 0;

			return true;
		}

		public function format($value) {
			return (int)$value;
		}

		public function parse($value) {
			return $this->format($value);
		}
	}
?>