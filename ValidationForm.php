<?php
namespace Pandora3\Widgets\ValidationForm;

use Pandora3\Core\Interfaces\RequestInterface;
use Pandora3\Widgets\Form\Form;
use Pandora3\Widgets\ValidationForm\Exceptions\UnregisteredRuleException;

/**
 * Class ValidationForm
 * @package Pandora3\Widgets\ValidationForm
 *
 * @property-read array $values
 */
abstract class ValidationForm extends Form {

	/** @var string $loadMethod */
	protected $loadMethod = 'post';

	/** @var bool $autoLoad */
	protected $autoLoad = false;
	
	/** @var bool $validateSameReferer */
	protected $validateSameReferer = true;

	/** @var array $ruleTypes */
	protected static $ruleTypes = [
		'required' => '\Pandora3\Widgets\ValidationForm\Rules\RuleRequired',
		'date' => '\Pandora3\Widgets\ValidationForm\Rules\RuleDate',
		'regexp' => '\Pandora3\Widgets\ValidationForm\Rules\RuleRegexp',
	];

	/**
	 * @return array
	 */
	protected function messages(): array {
		return [];
	}

	/**
	 * @return array
	 */
	abstract protected function rules(): array;

	/**
	 * @param array $values
	 * @return array
	 */
	protected function afterValidate(array $values): array {
		return $values;
	}

	/**
	 * @param RequestInterface $request
	 * @return array
	 */
	protected function loadFromRequest(RequestInterface $request): array {
		return $request->all($this->loadMethod);
	}

	/**
	 * @return bool
	 */
	public function validate(): bool {
		if (
			!$this->request->isPost ||
			($this->validateSameReferer && $this->request->refererUri !== $this->request->uri)
		) {
			return false;
		}
		$isLoaded = $this->isLoaded;
		if (!$isLoaded) {
			$this->load();
		}
		$isValid = true;
		$rules = $this->rules();
		$messages = $this->messages();
		foreach ($rules as $field => $fieldRules) {
			if (!$this->hasField($field)) {
				continue;
			}
			if (is_string($fieldRules)) {
				$fieldRules = [$fieldRules];
			}
			$value = $this->values[$field] ?? null;
			$requestValue = $this->requestValues[$field] ?? null;
			foreach ($fieldRules as $key => $rule) {
				$arguments = [];
				$unSanitized = $arguments['unSanitized'] ?? false;
				if (!is_numeric($key)) {
					$arguments = $rule;
					$rule = $key;
					if (substr($rule, -12) === ':unSanitized') {
						$rule = substr($rule, 0, strlen($rule) - 12);
						$unSanitized = true;
					}
				}
				if (!array_key_exists($rule, self::$ruleTypes)) {
					throw new UnregisteredRuleException($rule);
				}
				$ruleClass = self::$ruleTypes[$rule];
				/* if (!class_exists($sanitizerClass)) {
					throw new SanitizerClassNotFoundException($sanitizerClass); // todo: custom exception
				} */
				if (!$ruleClass::validate($unSanitized ? $requestValue : $value, $arguments)) {
					$message = $messages[$field][$rule] ?? $ruleClass::$message;
					$label = $this->getField($field)->label;
					$this->fieldMessages[$field][] = str_replace('{:label}', $label ?: $field, $message); // todo: message string params
					$isValid = false;
					break;
				}
			}
		}
		if ($isValid) {
			$this->values = $this->afterValidate($this->values);
		} else if (!$isLoaded) {
			// restore original non-sanitized values
			$this->values = $this->requestValues;
		}
		return $isValid;
	}

}