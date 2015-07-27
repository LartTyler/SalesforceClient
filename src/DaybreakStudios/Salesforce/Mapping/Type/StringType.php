<?php
	namespace DaybreakStudios\Salesforce\Mapping\Type;

	class StringType extends AbstractType {
		private $length;

		public function __construct($length) {
			$this->length = $length;
		}

		public function isValid($value) {
			return is_string($value) && strlen($value) <= $length;
		}

		public function isParseable($value) {
			return $this->isValid($value);
		}

		public function format($value) {
			return (string)$value;
		}

		public function parse($value) {
			return (string)$value;
		}
	}
?>