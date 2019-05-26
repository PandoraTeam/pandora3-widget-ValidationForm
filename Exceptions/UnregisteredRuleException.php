<?php
namespace Pandora3\Widgets\ValidationForm\Exceptions;

use Throwable;
use RuntimeException;
use Pandora3\Core\Interfaces\Exceptions\CoreException;

/**
 * Class UnregisteredRuleException
 * @package Pandora3\Widgets\ValidationForm\Exceptions
 */
class UnregisteredRuleException extends RuntimeException implements CoreException {

	/**
	 * @param string $rule
	 * @param Throwable|null $previous
	 */
	public function __construct(string $rule, ?Throwable $previous = null) {
		$message = "Unregistered validation rule '$rule'";
		parent::__construct($message, E_WARNING, $previous);
	}

}