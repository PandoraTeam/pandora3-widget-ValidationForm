<?php
namespace Pandora3\Widgets\ValidationForm\Rules;

/**
 * Class RuleRequired
 * @package Pandora3\Widgets\ValidationForm\Rules
 */
class RuleRequired {

	/** @var string $message */
	public static $message = 'Заполните поле "{:label}"'; // 'Field "{:label}" is required'

	/**
	 * @param mixed $value
	 * @return bool
	 */
	public static function validate($value): bool {
		return $value != '';
	}

}