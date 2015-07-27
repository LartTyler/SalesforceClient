<?php
	namespace DaybreakStudios\Salesforce\Mapping\Type;

	class DateType extends AbstractDateTimeAwareType {
		public function __construct() {
			parent::__construct('Y-m-d');
		}
	}
?>