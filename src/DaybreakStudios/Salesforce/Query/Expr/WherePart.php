<?php
	namespace DaybreakStudios\Salesforce\Query\Expr;

	class Part {
		protected $separator = '';
		protected $value;

		public function __construct($value) {
			$this->value = $value;
		}

		public function getSeparator() {
			return $this->separator;
		}

		public function getValue() {
			return $this->value;
		}

		public function assemble() {
			return sprintf('%s%s', $this->getSeparator(), $this->getValue());
		}
	}
?>