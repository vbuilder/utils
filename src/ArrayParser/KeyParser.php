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

use Nette,
	vBuilder\Utils\Strings;

/**
 * Key parser
 *
 * Inspired by Nette\Forms\Rules by David Grudl
 *
 * @author Adam Staněk (velbloud)
 * @since May 15, 2013
 */
class KeyParser extends Nette\Object implements \IteratorAggregate {

	/** @var Rule[] */
	private $rules = array();

	/** @var Rules */
	private $parent;

	/** @var array */
	private $key;

	public static $helperClasses = array(
		Rule::FILTER => 'vBuilder\ArrayParser\Filter',
		Rule::VALIDATOR => 'vBuilder\ArrayParser\Validator',
		Rule::CONDITION => 'vBuilder\ArrayParser\Validator'
	);

	public function __construct(array $key) {
		$this->key = $key;
	}

	/**
	 * Adds a validation rule for current key.
	 * @param  mixed      rule type
	 * @param  mixed      optional rule arguments
	 * @return self
	 */
	public function addRule($validator, $arg = NULL) {
		$rule = new Rule;
		$rule->type = Rule::VALIDATOR;
		$rule->key = $this->key;
		$rule->validator = $validator;
		$this->adjustOperation($rule);
		$rule->arguments = array_slice(func_get_args(), 1);
		$this->rules[] = $rule;
		return $this;
	}

	/**
	 * Adds a filter rule for current key.
	 * @param  mixed      rule type
	 * @param  mixed      optional rule arguments
	 * @return self
	 */
	public function addFilter($filter, $arg = NULL) {
		$rule = new Rule;
		$rule->type = Rule::FILTER;
		$rule->key = $this->key;
		$rule->validator = $filter;
		$this->adjustOperation($rule);
		$rule->arguments = array_slice(func_get_args(), 1);
		$this->rules[] = $rule;
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
		$args = func_get_args();
		array_unshift($args, $this->key);

		return call_user_func_array(array($this, 'addConditionOn'), $args);
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
		$rule->type = Rule::CONDITION;
		$rule->key = $key;
		$rule->validator = $validator;
		$this->adjustOperation($rule);
		$rule->arguments = array_slice(func_get_args(), 1);
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
	 * @param Context
	 * @return bool
	 */
	public function parse(Context $context)
	{
		foreach ($this as $rule) {

			$context->rule = $rule;

			$args = $rule->arguments;
			array_unshift($args, $context);;
			$result = call_user_func_array(self::getCallback($rule), $args);

			$success = $result === TRUE;
			if($rule->isNegative) $success = !$success;

			if ($success && $rule->branch && !$rule->branch->parse($context)) {
				return FALSE;

			} elseif (!$success && !$rule->branch) {

				if($result) {
					$context->errors[] = array(
						$context->absoluteKey,
						$result
					);
				}

				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * Iterates over complete ruleset.
	 * @return \ArrayIterator
	 */
	public function getIterator() {
		return new \ArrayIterator($this->rules);
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
			$validator = is_scalar($rule->validator) ? "'$rule->validator'" : '';
			throw new Nette\InvalidArgumentException("Unknown callback $validator for key ['" . implode($rule->key, "', '") . "'].");
		}
	}


	private static function getCallback($rule) {
		$op = $rule->validator;
		if (is_string($op) && strncmp($op, ':', 1) === 0) {
			return static::$helperClasses[$rule->type] . '::' . ltrim($op, ':');
		} else {
			return $op;
		}
		return $rule->validator;
	}

}