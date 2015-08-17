<?php
	namespace DaybreakStudios\Salesforce\Conversion;

	final class ConversionHandler {
		private $converters;

		public function __construct() {
			$this->converters = [
				100 => new ArrayConverter(),
				150 => new BooleanConverter(),
				200 => new DateTimeConverter(),
				250 => new IntegerConverter(),
				300 => new FloatConverter(),
				350 => new StringConverter(),
			];
		}

		public function getConverters() {
			return $this->converters;
		}

		public function addConverter(Converter $converter, $priority = null) {
			if ($priority === null) {
				$keys = array_keys($this->converters);

				if (sizeof($keys) > 0)
					sort($keys);
				else
					$keys = [ 0 ];

				$priority = $keys[sizeof($keys) - 1] + 50;
			}

			$this->converters[(int)$priority] = $converter;

			ksort($this->converters);
		}

		public function removeConverter($ident) {
			if (is_integer($ident) && isset($this->converters[$ident]))
				unset($this->converters[$ident]);
			else if ($ident instanceof Converter)
				foreach ($this->converters as $k => $converter)
					if ($converter === $ident) {
						unset($this->converters[$k]);

						break;
					}
			else
				throw new InvalidArgumentException('The value you provided for $ident is not a valid identifier');
		}

		public function convert($value) {
			foreach ($this->converters as $converter)
				if ($converter->handles($value)) {
					$value = $converter->convert($value);

					break;
				}

			return $value;
		}

		public function revert($value) {
			foreach ($this->converters as $converter)
				try {
					return $converter->revert($value);
				} catch (InvalidArgumentException $e) {}

			return $value;
		}

		public function convertForUpdateOrCreate($value) {
			foreach ($this->converters as $converter)
				if ($converter instanceof Conversion\StringConverter)
					continue;
				else if ($converter->handles($value)) {
					$value = $converter->convert($value);

					break;
				}

			return $value;
		}
	}
?>