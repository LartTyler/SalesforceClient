<?php
	namespace DaybreakStudios\Salesforce\Conversion;

	use \InvalidArgumentException;

	class ArrayConverter implements Converter {
		public function handles($val) {
			return is_array($val);
		}

		public function convert($val) {
			$str = '';

			foreach ($val as $v)
				if (is_numeric($v))
					$str .= ',' . $v;
				else if (is_string($v))
					$str .= ",'" . htmlentities($v) . "'";

			if (strlen($str) > 0)
				$str = substr($str, 1);

			return $str;
		}

		public function revert($val) {
			throw new InvalidArgumentException('ArrayConverter does not support reverting values');
		}
	}
?>