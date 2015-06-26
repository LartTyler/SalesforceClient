<?php
	namespace DaybreakStudios\Salesforce\Conversion;

	use \InvalidArgumentException;

	class IntegerConverter implements Converter {
		public function handles($val) {
			return is_integer($val);
		}

		public function convert($val) {
			return $val;
		}

		public function revert($val) {
			if (!is_numeric($val) || strpos($val, '.'))
				throw new InvalidArgumentException();

			return (int)$val;
		}
	}
?>