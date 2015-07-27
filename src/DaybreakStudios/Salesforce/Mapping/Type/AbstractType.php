<?php
	namespace DaybreakStudios\Salesforce\Mapping\Type;

	abstract class AbstractType implements Type {
		public function getName() {
			$name = strtolower(substr(__CLASS__, strrpos(__CLASS__, '\\')));

			if (strrpos($name, 'type') === strlen($name) - 4)
				return substr($name, 0, strlen($name) - 4);

			throw new BadMethodCallException(__CLASS__ . ' does not have an explicitly defined getName method, and ' .
				'it\'s class name could not be cleanly converted to a key');
		}
	}
?>