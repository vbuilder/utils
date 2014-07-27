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

/**
 * Simple expression container.
 *
 * @package vBuilder.Utils
 *
 * @author Adam Staněk (velbloud)
 * @since Jul 27, 2014
 */
class SimpleExpression implements IExpression {

	private $operator;
	private $operands = array();

	function __construct($operator, $operand1 = NULL, $operand2 = NULL) {
		$args = func_get_args();
		$this->operator = array_shift($args);
		array_walk_recursive($args, array($this, 'addOperand'));
	}

	/**
	 * @param mixed|FilterExpression
	 * @return self
	 */
	function addOperand($operand) {

		// Nexted expressions
		if($operand instanceof IExpression) {
			if($operand->getOperator() == $this->operator) {
				for($i = 0; $i < $operand->getOperandCount(); $i++)
					$this->addOperand($operand->getOperand($i));

				return $this;
			}
		}

		if(!in_array($operand, $this->operands))
			$this->operands[] = $operand;

		return $this;
	}

	/**
	 * @return mixed
	 */
	function getOperator() {
		return $this->operator;
	}

	/**
	 * @return mixed[]
	 */
	function getOperands() {
		return $this->operands;
	}

	/**
	 * @return mixed|NULL
	 */
	function getOperand($index) {
		return isset($this->operands[$index]) ? $this->operands[$index] : NULL;
	}

	/**
	 * @return int
	 */
	function getOperandCount() {
		return count($this->operands);
	}

}