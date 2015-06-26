<?php
	namespace DaybreakStudios\Salesforce\Conversion;

	use \InvalidArgumentException;

	class NullConverter implements Converter {
		public function handles($val) {
			return $val === null;
		}

		public function convert($val) {
			return 'null';
		}

		public function revert($val) {
			if ($val !== 'null')
				throw new InvalidArgumentException();

			return null;
		}
	}
?>