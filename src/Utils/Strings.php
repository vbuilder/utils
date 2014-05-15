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

namespace vBuilder\Utils;

use Nette;

/**
 * String manipulation library
 *
 * @author Adam Staněk (velbloud)
 * @since Feb 7, 2011
 */
class Strings extends Nette\Utils\Strings {

	/**
	 * Improved sprintf function with support for named placeholders
	 *
	 * @param string
	 * @param array
	 * @return string
	 */
	public static function sprintf($str, array $args = array()) {
		$map = array_flip(array_keys($args));
		$str = preg_replace_callback('/(^|[^%])%([a-zA-Z0-9_-]+)(\$)?/', function ($m) use ($map) {

			// Standard mapping
			// @see http://www.php.net/manual/en/function.sprintf.php
			if(in_array($m[2], array('b', 'c', 'd', 'e', 'E', 'f', 'F', 'g', 'G', 'o', 's', 'u', 'x', 'X'))) return $m[0];

			// No registered mapping
			if(!isset($map[$m[2]]))
				return $m[1].'%%'.$m[2]. (isset($m[3]) ? $m[3] : '');

			// Registered mapping
			return $m[1].'%'.($map[$m[2]] + 1) . (isset($m[3]) ? '$' : '$s');

		}, $str);

		return vsprintf($str, $args);
	}

}
