<?php
	namespace DaybreakStudios\Salesforce\Conversion;

	use \InvalidArgumentException;

	class FloatConverter implements Converter {
		public function handles($val) {
			return is_float($val);
		}

		public function convert($val) {
			return $val;
		}

		public function revert($val) {
			if (!is_numeric($val))
				throw new InvalidArgumentException();

			return (float)$val;
		}
	}
?>