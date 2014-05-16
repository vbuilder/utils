<?php

/**
 * This file is part of vBuilder Framework (vBuilder FW).
 *
 * Copyright (c) 2011 Adam Staněk <adam.stanek@v3net.cz>
 *
 * For more information visit http://www.vbuilder.cz
 *
 * vBuilder FW is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * vBuilder FW is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with vBuilder FW. If not, see <http://www.gnu.org/licenses/>.
 */

namespace vBuilder\ArrayParser;

use Nette;

/**
 * Key parser
 *
 * Inspired by Nette\Forms\Rules by David Grudl
 *
 * @author Adam Staněk (velbloud)
 * @since May 15, 2013
 */
class KeyParser extends Nette\Object implements \IteratorAggregate {

	/** @var Rule */
	private $required;

	/** @var Rule[] */
	private $rules = array();

	/** @var Rules */
	private $parent;

	/** @var array */
	private $key;

	public function __construct(array $key) {
		$this->key = $key;
	}

	/**
	 * Makes key mandatory.
	 * @param  mixed  state or error message
	 * @return self
	 */
	public function setRequired($value = TRUE)
	{
		if ($value) {
			$this->addRule(Validator::REQUIRED, is_string($value) ? $value : NULL);
		} else {
			$this->required = NULL;
		}
		return $this;
	}


	/**
	 * Is key mandatory?
	 * @return bool
	 */
	public function isRequired()
	{
		return $this->required instanceof Rule ? !$this->required->isNegative : FALSE;
	}


	/**
	 * Adds a validation rule for current key.
	 * @param  mixed      rule type
	 * @param  string     message to display for invalid data
	 * @param  mixed      optional rule arguments
	 * @return self
	 */
	public function addRule($validator, $message = NULL, $arg = NULL)
	{
		$rule = new Rule;
		$rule->key = $this->key;
		$rule->validator = $validator;
		$this->adjustOperation($rule);
		$rule->arg = $arg;
		$rule->message = $message;
		if ($rule->validator === Validator::REQUIRED) {
			$this->required = $rule;
		} else {
			$this->rules[] = $rule;
		}
		return $this;
	}


	/**
	 * Adds a validation condition a returns new branch.
	 * @param  mixed      condition type
	 * @param  mixed      optional condition arguments
	 * @return Rules      new branch
	 */
	public function addCondition($validator, $arg = NULL)
	{
		return $this->addConditionOn($this->key, $validator, $arg);
	}


	/**
	 * Adds a validation condition on specified key a returns new branch.
	 * @param  array      key
	 * @param  mixed      condition type
	 * @param  mixed      optional condition arguments
	 * @return Rules      new branch
	 */
	public function addConditionOn($key, $validator, $arg = NULL)
	{
		$rule = new Rule;
		$rule->key = $key;
		$rule->validator = $validator;
		$this->adjustOperation($rule);
		$rule->arg = $arg;
		$rule->branch = new static($this->key);
		$rule->branch->parent = $this;

		$this->rules[] = $rule;
		return $rule->branch;
	}


	/**
	 * Adds a else statement.
	 * @return Rules      else branch
	 */
	public function elseCondition()
	{
		$rule = clone end($this->parent->rules);
		$rule->isNegative = !$rule->isNegative;
		$rule->branch = new static($this->parent->key);
		$rule->branch->parent = $this->parent;
		$this->parent->rules[] = $rule;
		return $rule->branch;
	}


	/**
	 * Ends current validation condition.
	 * @return Rules      parent branch
	 */
	public function endCondition()
	{
		return $this->parent;
	}

	/**
	 * Performs processing and validation.
	 *
	 * @param array|object data structure as reference
	 * @param array output array of error messages
	 * @return bool
	 */
	public function parse(&$structure, array &$errors = array())
	{
		foreach ($this as $rule) {

			$found = TRUE;
			$ref = &$structure;
			foreach($rule->key as $k) {
				if($ref === NULL) $ref = array();
				if(!array_key_exists($k, $ref)) {
					$found = FALSE;
					$ref[$k] = NULL;
				}

				$ref = &$ref[$k];
			}

			$success = (bool) call_user_func(self::getCallback($rule), &$ref, $rule->arg);
			if($rule->isNegative) $success = !$success;

			if ($success && $rule->branch && !$rule->branch->parse($structure, $errors)) {
				return FALSE;

			} elseif (!$success && !$rule->branch) {
				$errors[] = array(
					$rule->key,
					Validator::formatMessage($rule, $ref)
				);

				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * Iterates over complete ruleset.
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		$rules = $this->rules;
		if ($this->required) {
			array_unshift($rules, $this->required);
		}
		return new \ArrayIterator($rules);
	}


	/**
	 * Process 'operation' string.
	 * @param  Rule
	 * @return void
	 */
	private function adjustOperation($rule)
	{
		if (is_string($rule->validator) && ord($rule->validator[0]) > 127) {
			$rule->isNegative = TRUE;
			$rule->validator = ~$rule->validator;
		}

		if (!is_callable($this->getCallback($rule))) {
			$validator = is_scalar($rule->validator) ? " '$rule->validator'" : '';
			throw new Nette\InvalidArgumentException("Unknown validator$validator for key ['" . implode($rule->key, "', '") . "'].");
		}
	}


	private static function getCallback($rule)
	{
		$op = $rule->validator;
		if (is_string($op) && strncmp($op, ':', 1) === 0) {
			return __NAMESPACE__ . '\\Validator::validate' . ltrim($op, ':');
		} else {
			return $op;
		}
	}

}