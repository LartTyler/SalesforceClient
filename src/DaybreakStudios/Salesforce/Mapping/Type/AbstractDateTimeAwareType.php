<?php
	namespace DaybreakStudios\Salesforce\Mapping\Type;

	use \DateTime;
	use \InvalidArgumentException;

	abstract class AbstractDateTimeAwareType extends AbstractType {
		private $dtFormat;

		public function __construct($dtFormat) {
			$this->dtFormat = $dtFormat;
		}

		public function isValid($value) {
			return $value instanceof DateTime;
		}

		public function isParseable($value) {
			return DateTime::createFromFormat($this->dtFormat, $value) !== false;
		}

		public function format($value) {
			return $value->format($this->dtFormat);
		}

		public function parse($value) {
			return DateTime::createFromFormat($this->dtFormat, $value);
		}
	}
?>