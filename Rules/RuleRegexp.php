<?php
namespace Pandora3\Widgets\ValidationForm\Rules;

/**
 * Class RuleRegexp
 * @package Pandora3\Widgets\ValidationForm\Rules
 */
class RuleRegexp {

	/** @var string $message */
	public static $message = 'Неверный формат поля "{:label}"'; // 'Field "{:label}" wrong format'

	/**
	 * @param mixed $value
	 * @param array $arguments
	 * @return bool
	 */
	public static function validate($value, array $arguments = []): bool {
		if (!$value) {
			return true;
		}
		$pattern = $arguments['pattern'] ?? null;
		if (!$pattern) {
			// todo: custom exception
			throw new \LogicException("Rule regexp argument 'pattern' is required");
		}
		return (bool) preg_match($pattern, $value);
	}

}