<?php
namespace Pandora3\Widgets\ValidationForm\Rules;

use Pandora3\Libs\Time\Date;

/**
 * Class RuleDate
 * @package Pandora3\Widgets\ValidationForm\Rules
 */
class RuleDate {

	/** @var string $message */
	public static $message = 'Неверный формат даты "{:label}"'; // 'Field "{:label}" wrong date format'

	/**
	 * @param mixed $value
	 * @param array $arguments
	 * @return bool
	 */
	public static function validate($value, array $arguments = []): bool {
		if (!$value) {
			return true;
		}
		$format = $arguments['format'] ?? 'd.m.Y';
		return !is_null(Date::createFromFormat($format, $value));
	}

}