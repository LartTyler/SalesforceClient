<?php
	namespace DaybreakStudios\Salesforce\Mapping\Type;

	use \InvalidArgumentException;

	class PhoneType extends AbstractType {
		public function isValid($value) {
			if (!is_string($value))
				return false;

			try {
				$this->format($value);
			} catch (InvalidArgumentException $e) {
				return false;
			}

			return true;
		}

		public function isParseable($value) {
			if (!is_string($value))
				return false;

			try {
				$this->parse($value);
			} catch (InvalidArgumentException $e) {
				return false;
			}

			return true;
		}

		public function format($value) {
			$phone = $this->parse($value);

			if (substr_count($phone, 'x') === 0)
				$phone .= 'x';

			list($number, $ext) = explode('x', $phone);

			if (strlen($number) < 10)
				throw new InvalidArgumentException('The number portion of $value must be at least 10 characters long');

			$formatted = '';

			if (strlen($number) > 10) {
				for ($i = 0, $ii = strlen($number) - 10; $i < $ii; $i++)
					$formatted .= $number[$i];

				$formatted .= ' ';
			}

			$phone = substr($number, strlen($number) - 10);

			for ($i = 0; $i < 10; $i++) {
				if ($i === 0)
					$formatted .= '(';
				else if ($i === 3)
					$formatted .= ') ';
				else if ($i === 6)
					$formatted .= '-';

				$formatted .= $phone[$i];
			}

			if (strlen($ext) > 0)
				$formatted = sprintf('%s x%s', $formatted, $ext);

			return $formatted;
		}

		public function parse($value) {
			$phone = '';

			for ($i = 0, $ii = strlen($value); $i < $ii; $i++) {
				$c = strtolower($value[$i]);

				if (is_numeric($c) || $c === 'x')
					$phone .= $c;
			}

			if (substr_count($phone, 'x') > 1)
				throw new InvalidArgumentException('$value contains more than one extension');

			return $phone;
		}
	}
?>