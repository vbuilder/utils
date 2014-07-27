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

namespace vBuilder\Expressions;

use Iterator;

/**
 * Traverses through IExpression in infix order
 *
 * @package vBuilder.Utils
 *
 * @author Adam Staněk (velbloud)
 * @since Jul 27, 2014
 */
class InfixIterator implements Iterator {

	/** @var IExpression */
	private $expression;

	/** @var array context stack */
	private $context;

	/** @var int */
	private $key;

	/** @var mixed */
	private $leftBracket = '(';

	/** @var mixed */
	private $rightBracket = ')';

	function __construct(IExpression $expression) {
		$this->expression = $expression;
		$this->rewind();
	}

	function rewind() {
		$this->key = 0;
		$this->context = array(
			NULL,
			array($this->expression, 0)
		);
	}

	function current() {

		// Expand possible nested expressions
		$this->expandCurrentContext();

		// Current context
		$current = &$this->context[count($this->context) - 1];

		// Current context is IExpression => return operand at current position
		if(is_array($current)) {
			return $current[0]->getOperand($current[1]);

		// Current context is a prepared statement => return it
		} else {
			return $current;
		}
	}

	function key() {
		return $this->key;
	}

	function valid() {
		return count($this->context) > 1;
	}

	function next() {
		$this->key++;

		// Current context
		$current = &$this->context[count($this->context) - 1];

		// Current context is a prepared statement => drop it
		if(!is_array($current)) {
			array_pop($this->context);
		}

		// Current context is IExpression => lets move up the position or
		// drop it if there is no more operands in it
		else {
			if($current[1] + 1 < $current[0]->getOperandCount()) {
				$current[1]++;
				array_push($this->context, $current[0]->getOperator());

			} else {
				array_pop($this->context);
			}
		}
	}

	/**
	 * Resolves nested IExpressions.
	 */
	private function expandCurrentContext() {
		$current = &$this->context[count($this->context) - 1];

		if(is_array($current)) {
			$operand = $current[0]->getOperand($current[1]);
			while($operand instanceof IExpression) {

				// Move to next operand
				if($current[1] + 1 < $current[0]->getOperandCount()) {
					$current[1]++;
					array_push($this->context, $current[0]->getOperator());

				} else
					array_pop($this->context);

				// Right bracket
				if($operand->getOperandCount() > 1)
					array_push($this->context, $this->rightBracket);

				// Expand expression
				array_push($this->context, array($operand, 0));

				if($operand->getOperandCount() > 1) {
					array_push($this->context, $this->leftBracket);
					break;

				} else {
					$current = &$this->context[count($this->context) - 1];
					$operand = $current[0]->getOperand($current[1]);
				}
			}
		}
	}
}