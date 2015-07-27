<?php
	namespace DaybreakStudios\Salesforce\Mapping\Type;

	interface Type {
		/**
		 * Returns the string that the type should be indexed by.
		 *
		 * @return string the key to index the type by; for custom types, it is recommended to namespace your type
		 *                    (i.e. 'dbstudios.string')
		 */
		public function getName();

		/**
		 * Returns true if the given value is valid for this type. This is primarily used for type sanity checks before
		 * sending data to Salesforce.
		 *
		 * @param  mixed $value the value to check
		 * @return boolean      true if the value is valid, false otherwise
		 */
		public function isValid($value);

		/**
		 * Returns true if the value can be parsed from the given value. The value passed to this function will always
		 * be a value coming from Salesforce.
		 *
		 * @return boolean true if the value can be parsed, false otherwise
		 */
		public function isParseable($value);

		/**
		 * Formats the given value in a way that Salesforce can understand. The value returned from this function will
		 * be exactly what is sent to Salesforce.
		 *
		 * @throws InvalidArgumentException if the given value cannot be formatted
		 *
		 * @param  mixed $value the value to format
		 * @return mixed        the value as it should be sent to Salesforce
		 */
		public function format($value);

		/**
		 * Parses a value from Salesforce into the type represented by this object.
		 *
		 * @throws InvalidArgumentException if the value cannot be parsed
		 *
		 * @param  mixed $value the value to parse
		 * @return mixed        the parsed value
		 */
		public function parse($value);
	}
?>