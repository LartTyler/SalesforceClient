<?php
	namespace DaybreakStudios\Salesforce\DateTime\SalesforceDateTimeWrapper;

	use \DateTime;

	abstract class SalesforceDateTimeWrapper {
		protected $dt;

		public function __construct(DateTime $dt) {
			$this->dt = $dt;
		}

		public function getDateTime() {
			return $this->dt;
		}

		public abstract function format();
	}
?>