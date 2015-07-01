<?php
	namespace DaybreakStudios\Salesforce\Query;

	use \Exception;

	class QueryException extends Exception {
		const MSG_FORMAT = 'Query failed with the following message%s: %s';

		public function __construct($msg) {
			parent::__construct(sprintf(self::MSG_FORMAT, strpos($msg, ';') !== false ? 's' : '', $msg));
		}
	}
?>