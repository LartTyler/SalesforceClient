<?php
	namespace DaybreakStudios\Salesforce;

	final class ClientUtil {
		/**
		 * Gets the Salesforce fields member out of an SObject, and guarentees conversion to an associative array.
		 *
		 * @param  SObject $sob the SObject to retrieve the fields member from
		 * @return array|null      the fields member as an associative array, or null if fields is not defined
		 */
		public static function getSObjectFields(SObject $sob) {
			if (!isset($sob->fields))
				return null;

			$fields = $sob->fields;

			if (is_object($fields))
				$fields = get_object_vars($fields);

			return $fields;
		}

		/**
		 * Updates an SObject's fields member using either an object or an associative array.
		 *
		 * @throws InvalidArgumentException if $fields is not an array or an object
		 *
		 * @param  SObject $sob    the SObject to update
		 * @param  array|object  $fields the array or object to update the fields member from
		 * @return SObject         the updated SObject for chaining
		 */
		public static function updateSObjectFields(SObject $sob, $fields) {
			if (!is_array($fields) && !is_object($fields))
				throw new InvalidArgumentException('$fields must be an array or object');

			if (is_object($sob->fields))
				$fields = (object)$fields;

			$sob->fields = $fields;

			return $sob;
		}

		/**
		 * Normalizes an SObject from a Salesforce query. When normalizing, the following steps are taken:
		 *
		 * - Remove padding from the Id field (if set)
		 * - Calls the revert method of the ConversionHandler on every value of the fields member
		 *
		 * @param  SObject           $record    [description]
		 * @param  ConversionHandler $converter [description]
		 * @return [type]                       [description]
		 */
		public static function transmute(SObject $record, ConversionHandler $converter) {
			if (isset($record->Id))
				$record->Id = substr($record->Id, 0, 15);

			$fields = self::getSObjectFields($record);

			if ($fields === null)
				return $record;

			foreach ($fields as $k => $v)
				$fields[$k] = $converter->revert($v);

			return self::updateSObjectFields($record, $fields);
		}

		/**
		 * Cleans an array of objects in preparation to send them to Salesforce (usually for an update or create call).
		 *
		 * @param  array  $objects an array of objects to clean
		 * @return array           an array containing the cleaned objects
		 */
		public static function clean(array $objects) {
			foreach ($objects as $k => $obj) {
				$fields = self::getSObjectFields($obj);

				if ($fields === null) {
					unset($objects[$k]);

					continue;
				}

				foreach ($fields as $k => $v) {
					if ($v === null || is_string($v) && strlen($v) === 0) {
						if (!isset($obj->fieldsToNull))
							$obj->fieldsToNull = [];

						$obj->fieldsToNull[] = $k;

						unset($fields[$k]);

						continue;
					}

					$fields[$k] = $this->converter->convertForUpdateOrCreate($v);
				}

				$obj = self::updateSObjectFields($obj, $fields);
			}

			return $objects;
		}
	}
?>