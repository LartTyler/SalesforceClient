<?php
	namespace DaybreakStudios\Salesforce\Conversion;

	use \DateTime;
	use \InvalidArgumentException;

	use DaybreakStudios\Salesforce\DateTime\SalesforceDateTime;
	use DaybreakStudios\Salesforce\DateTime\SalesforceDateTimeWrapper;

	class DateTimeConverter implements Converter {
		protected static $FORMATS = [
			'Y-m-d',
			'Y-m-d\\TH:i:s.000\\Z',
		];

		public function handles($val) {
			return $val instanceof DateTime || $val instanceof SalesforceDateTimeWrapper;
		}

		public function convert($val) {
			if ($val instanceof DateTime)
				$val = new SalesforceDateTime($val);

			return $val->format();
		}

		public function revert($val) {
			foreach (self::$FORMATS as $format) {
				$v = DateTime::createFromFormat($format, $val);

				if ($v !== false)
					return $v;
			}

			throw new InvalidArgumentException();
		}
	}
?>