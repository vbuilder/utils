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

namespace vBuilder\Parsers\ArrayParser;

use vBuilder\Utils\Strings;

/**
 * Static helpers for value processing
 *
 * @package vBuilder.Utils
 *
 * @author Adam Staněk (velbloud)
 * @since May 15, 2013
 */
class Filter {

	public static function filterDefaultValue(Context $context, $arg) {
		if($context->value === NULL)
			$context->value = $arg;

		return TRUE;
	}

	public static function filterSimplify(Context $context) {
		if($context->value !== NULL)
			$context->value = Strings::simplify($context->value);

		return TRUE;
	}

	public static function filterTrim(Context $context, $characterMask = " \t\n\r\0\x0B") {
		if($context->value !== NULL)
			$context->value = trim($context->value, $characterMask);

		return TRUE;
	}

	public static function filterSerialize(Context $context) {
		if($context->value !== NULL)
			$context->value = serialize($context->value);

		return TRUE;
	}


}