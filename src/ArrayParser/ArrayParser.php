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

namespace vBuilder;

use vBuilder\Utils\Strings,
	vBuilder\ArrayParser\Context,
	vBuilder\ArrayParser\KeyParser,
	Nette;

/**
 * Validator
 *
 * Usage:
 * <code>
 * $parser = new ArrayParser;
 * $parser->addKey('name')
 * 	->addRule(ArrayParser::SCALAR)
 * 	->addFilter(ArrayParser::SIMPLIFY);
 * $parser->addKey('role')
 *  ->addFilter(ArrayParser::DEFAULT_VALUE, 'user')
 * 	->addRule(ArrayParser::ONE_OF, array('admin', 'user'));
 *
 * $parsed = $parser->parse($data, $errors);
 * </code>
 *
 * @author Adam Staněk (velbloud)
 * @since May 15, 2013
 */
class ArrayParser extends Nette\Utils\ArrayHash {

	/** Filters **/
	const DEFAULT_VALUE = ':filterDefaultValue';
	const TRIM = ':filterTrim';
	const SIMPLIFY = ':filterSimplify';
	const SERIALIZE = ':filterSerialize';
	/**/

	/** Validators **/
	const REQUIRED = ':validateRequired';
	const NOT_EMPTY = ':validateNotEmpty';

	const SCALAR = ':validateScalar';
	const ONE_OF = ':validateInArray';

	const STRUCTURE = ':validateStructure';
	const ARRAY_OF_STRUCTURE = ':validateArrayOfStructure';
	/**/

	public function parse(array $data, &$errors = array()) {

		/// @todo add translator
		$context = new Context($data);
		$success = $this->parseContext($context);

		$errors = $context->errors;
		return $success === FALSE ? FALSE : $context->data;
	}

	public function parseContext(Context $context, $baseKey = array()) {
		$success = TRUE;

		$oldBaseKey = $context->baseKey;
		$oldRule = $context->rule;

		$context->baseKey = $baseKey;
		$context->setRule();

		foreach($this as $rules) {
			if(!$rules->parse($context))
				$success = FALSE;
		}

		$context->baseKey = $oldBaseKey;
		$context->rule = $oldRule;

		return $success;
	}

	public function addKey($key) {
		if(!isset($this[$key]))
			$this[$key] = new KeyParser(array($key));

		return $this[$key];
	}

	/**
	 * Adds new rule.
	 * @param  mixed
	 * @param  KeyParser
	 * @return void
	 */
	public function offsetSet($index, $rule) {
		if(!$rule instanceof KeyParser) {
			throw new Nette\InvalidArgumentException('Argument must be ' . __NAMESPACE__ . 'ArrayParser\\KeyParser descendant.');
		}

		parent::offsetSet($index, $rule);
	}

}