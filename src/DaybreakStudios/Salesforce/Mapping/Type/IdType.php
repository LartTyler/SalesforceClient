<?php
	namespace DaybreakStudios\Salesforce\Mapping\Type;

	class IdType extends AbstractType {
		public function isValid($value) {
			return strlen($value) === 15 || strlen($value) === 18;
		}

		public function isParseable($value) {
			return $this->isValid($value);
		}

		public function format($value) {
			return substr($value, 0, 15);
		}

		public function parse($value) {
			return $this->format($value);
		}
	}
?>