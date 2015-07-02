<?php
	namespace DaybreakStudios\Salesforce\Query;

	use DaybreakStudios\Common\Enum\Enum;

	class PartType extends Enum {
		private $appendable;
		private $soql;

		protected function __construct($appendable = true, $soql = null) {
			$this->appendable = $appendable;
			$this->soql = $soql;
		}

		public function isAppendSupported() {
			return $this->appendable;
		}

		public function getSoql() {
			return $this->soql ?: $this->name();
		}

		protected static function init() {
			parent::register('SELECT');
			parent::register('FROM', false);
			parent::register('WHERE');
			parent::register('GROUPBY', true, 'GROUP BY');
			parent::register('HAVING');
			parent::register('ORDERBY', true, 'ORDER BY');
			parent::register('LIMIT', false);
		}
	}
?>