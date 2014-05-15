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

namespace vBuilder\Validators;

use vBuilder\Utils\Strings;

/**
 * Static helpers for Validator
 *
 * @author Adam Staněk (velbloud)
 * @since May 15, 2013
 */
class Helpers {

	public static $defaultMessages = array(
		Validator::REQUIRED => 'Parameter %s is required.',
		Validator::IN_ARRAY => 'Invalid value for parameter %s.',
		Validator::SCALAR => 'Invalid value for parameter %s. Expected scalar.'
	);

	public static function validateRequired($value) {
		return $value !== NULL && $value !== array() && $value !== '';
	}

	public static function validateScalar($value) {
		return is_scalar($value);
	}

	public static function validateInArray($value, $args) {
		return in_array($value, $args);
	}

	public static function formatMessage(Rule $rule, $value) {
		$message = $rule->message;

		if($message === NULL && is_string($rule->validator) && isset(static::$defaultMessages[$rule->validator])) {
			$message = static::$defaultMessages[$rule->validator];
		}

		elseif($message == NULL) {
			trigger_error("Missing validation message for key ['" . implode($rule->key, "', '") . "'].", E_USER_WARNING);
			return ;
		}

		return Strings::sprintf($message, array(
			'key' => implode($rule->key, '.'),
			'value' => $value
		));
	}

}