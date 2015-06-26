<?php
	namespace DaybreakStudios\Salesforce\DateTime;

	class SalesforceDate extends SalesforceDateTimeWrapper {
		public function format() {
			return $this->getDateTime()->format('Y-m-d');
		}
	}
?>