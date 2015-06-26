<?php
	namespace DaybreakStudios\Salesforce\DateTime;

	use \DateTime;

	class SalesforceDateTime extends SalesforceDateTimeWrapper {
		public function format() {
			return $this->getDateTime()->format(DateTime::ISO8601);
		}
	}
?>