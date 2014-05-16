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

use vBuilder,
	vBuilder\Utils\Strings;

/**
 * Static helpers for value validation
 *
 * @author Adam Staněk (velbloud)
 * @since May 15, 2013
 */
class Validator {

	public static function validateRequired(Context $context, $message = NULL) {
		if($context->value !== NULL)
			return TRUE;

		return Strings::sprintf(
			$message ?: 'Parameter %key is required.',
			array('key' => $context->printableKey)
		);
	}

	/**
	 * @note Spaces are considered not empty. Might want to addFilter(ArrayParser::TRIM) first
	 */
	public static function validateNotEmpty(Context $context, $message = NULL) {
		if($context->value != '')
			return TRUE;

		return Strings::sprintf(
			$message ?: 'Parameter %key can\'t be empty',
			array('key' => $context->printableKey)
		);
	}

	public static function validateScalar(Context $context, $message = NULL) {
		if(is_scalar($context->value))
			return TRUE;

		return Strings::sprintf(
			$message ?: 'Invalid value for parameter %key. Expected scalar.',
			array('key' => $context->printableKey)
		);
	}

	public static function validateInArray(Context $context) {
		$args = count($context->rule->arguments) == 1 && is_array($context->rule->arguments[0])
			? $context->rule->arguments[0] : $context->rule->arguments;

		if(in_array($context->value, $args))
			return TRUE;

		$enum = '';
		for($i = 0; $i < count($args); $i++) {
			if($i > 0) {
				$enum .= $i + 1 < count($args)
					? ', '
					: ' or ';
			}

			$enum .= is_scalar($args[$i]) ? var_export($args[$i], TRUE) : gettype($args[$i]);
		}

		$value = is_scalar($context->value)
			? 'value ' . var_export($context->value, TRUE)
			: 'value';

		$message = 'Invalid %value for parameter %key.';
		if(count($args) == 1) $message .= ' Expected one of %enum.';
		elseif(count($args) == 2) $message .= ' Expected either %enum.';
		elseif(count($args) > 2) $message .= ' Expected one of %enum.';

		return Strings::sprintf(
			$message,
			array(
				'value' => $value,
				'key' => $context->printableKey,
				'enum' => $enum
			)
		);
	}

	public static function validateStructure(Context $context, vBuilder\ArrayParser $nestedParser) {
		return $nestedParser->parseContext($context, $context->absoluteKey);
	}

}