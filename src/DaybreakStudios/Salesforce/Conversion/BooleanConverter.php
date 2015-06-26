<?php
	namespace DaybreakStudios\Salesforce\Conversion;

	use \InvalidArgumentException;

	class BooleanConverter implements Converter {
		public function handles($val) {
			return is_bool($val);
		}

		public function convert($val) {
			return $val ? 'true' : 'false';
		}

		public function revert($val) {
			if ($val !== 'true' && $val !== 'false')
				throw new InvalidArgumentException();

			return !!str_replace('false', '', $val);
		}
	}
?>