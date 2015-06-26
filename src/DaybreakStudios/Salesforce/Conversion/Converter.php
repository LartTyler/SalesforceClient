<?php
	namespace DaybreakStudios\Salesforce\Conversion;

	interface Converter {
		/**
		 * Returns true if this converter handles the given value.
		 *
		 * @param  mixed   $val the value to check
		 * @return boolean      true if the converter can handle the value, false otherwise
		 */
		public function handles($val);

		/**
		 * Converts a value to a format acceptable by Salesforce.
		 *
		 * @param  mixed $val the value to convert
		 * @return mixed      the value in a format that Salesforce will understand
		 */
		public function convert($val);

		/**
		 * Functionally opposite of Converter#convert(). When given a value from Salesforce, this method should
		 * convert it to the form desired by the application.
		 *
		 * @throws InvalidArgumentException if the given value cannot be converted by this converter.
		 *
		 * @param  mixed $val the value from Salesforce to revert
		 * @return mixed      the reverted value
		 */
		public function revert($val);
	}
?>