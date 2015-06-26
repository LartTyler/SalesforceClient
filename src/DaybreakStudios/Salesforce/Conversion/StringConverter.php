<?php
	namespace DaybreakStudios\Salesforce\Conversion;

	use \InvalidArgumentException;

	class StringConverter implements Converter {
		public function handles($val) {
			return is_string($val);
		}

		public function convert($val) {
			return sprintf("'%s'", htmlentities($val));
		}

		public function revert($val) {
			throw new InvalidArgumentException('StringConverter does not support reverting values');
		}
	}
?>