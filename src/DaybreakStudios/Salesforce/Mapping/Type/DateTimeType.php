<?php
	namespace DateTime\Salesforce\Mapping\Type;

	class DateTimeType extends AbstractDateTimeAwareType {
		public function __construct() {
			parent::__construct('Y-m-d\\TH:i:s.000\\Z');
		}
	}
?>