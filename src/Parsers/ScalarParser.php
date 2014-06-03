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

namespace vBuilder\Parsers;

/**
 * Parser library for scalar types
 *
 * @author Adam Staněk (velbloud)
 * @since Jun 3, 2014
 */
class ScalarParser {

	/**
	 * Parses TRUE or FALSE strings into their boolean representation
	 *
	 * @param string
	 * @param mixed value, which will be returned if given string can't be parsed into boolean
	 * @return bool
	 */
	public static function parseBool($str, $onFailValue = NULL) {
		if(strcasecmp($str, 'true') == 0) return true;
		elseif(strcasecmp($str, 'false') == 0) return false;

		return $onFailValue;
	}

	/**
	 * Parses numeric string into FLOAT variable.
	 * Automatically converts decimal comma into dot and removes any spaces
	 * and other non-numeric chars.
	 *
	 * If given string does not contain any number $onFailValue is returned.
	 *
	 * @param string
	 * @param mixed value, which will be returned if given string can't be parsed into float
	 *
	 * @return float|NULL
	 */
	public static function parseFloat($str, $onFailValue = NULL) {

		if(preg_match_all("/([^0-9]+)?([0-9]+)/", $str, $matches)) {

			$sgn = strpos($matches[1][0], '-') !== FALSE ? -1 : 1;
			$point = -1;
			$commaCount = 0;
			$dotCount = 0;
			foreach($matches[1] as $key=>$sep) {
				if(strpos($sep, '.') !== FALSE)
					$point = ++$dotCount < 2 ? $key : -1;

				elseif(strpos($sep, ',') !== FALSE)
					$point = ++$commaCount < 2 ? $key : -1;
			}

			$nStr = '';
			foreach($matches[2] as $key=>$n) {
				if($key == $point) $nStr .= '.';
				$nStr .= $n;
			}

			return $sgn * floatval($nStr);
		}

		return $onFailValue;
	}

}