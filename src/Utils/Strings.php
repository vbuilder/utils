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

use vBuilder\Parsers\ScalarParser,
	Nette;

/**
 * String manipulation library
 *
 * @package vBuilder.Utils
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
		$str = preg_replace_callback('/(.?)%([a-zA-Z0-9_-]+|{[^}]+})(\$)?/', function ($m) use ($map) {

			// Escaping
			if($m[1] == '%') return $m[0];

			// Named placeholders in brackets
			if(strncmp($m[2], '{', 1) === 0)
				$m[2] = substr($m[2], 1, -1);

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

	/**
	 * Does $haystack contain $needle?
	 *
	 * @param string hastack
	 * @param string needle
	 * @param bool case sensitive?
	 *
	 * @return bool
	 */
	public static function contains($haystack, $needle, $caseSensitive = true) {
		if($caseSensitive)
			return parent::contains($haystack, $needle);
		else
			return parent::contains(self::lower($haystack), self::lower($needle));
	}

	/**
	 * Replaces multiple spaces with one and removes spaces
	 * from the begining and end of string.
	 *
	 * @param string input
	 * @param bool trim?
	 * @return string
	 */
	public static function simplify($input, $trim = true) {
		$simplified = preg_replace("/[[:blank:]]+/", " ", $input);

		return $trim ? trim($simplified) : $simplified;
	}

	/**
	 * Creates parametrized string
	 * Ex. something:123,abcd,a b aa \, d,something=true
	 *
	 * @see Strings::parseParametrizedString()
	 *
	 * @param string base string
	 * @param array of parameters
	 * @return string
	 */
	public static function intoParameterizedString($key, $params = array()) {
		$key = str_replace(':', '\\:', $key);
		if(count($params) == 0) return $key;

		$p = array();
		foreach($params as $k => $v) {
			if(is_bool($v)) {
				$v = $v ? 'true' : 'false';
			}

			if(!is_int($k)) {
				$p[] = str_replace(array('\\', ',', '='), array('\\\\', '\\,', '\\='), $k)
					   . '=' .
					   str_replace(array('\\', ',', '='), array('\\\\', '\\,', '\\='), $v);
			} else {
				$p[] = str_replace(array('\\', ',', '='), array('\\\\', '\\,', '\\='), $v);
			}
		}

		return $key . ':' . implode($p, ',');
	}

	/**
	 * Parses string coded by Strings::intoParameterizedString
	 *
	 * <code>
	 * list($key, $parameters) = Strings::parseParametrizedString($str);
	 * </code>
	 *
	 * @see Strings::intoParameterizedString()
	 *
	 * @param string
	 * @return array ($key, array($parameters))
	 */
	public static function parseParametrizedString($str) {
		$escaped = false;
		$parsed = array('');
		$associative = false;
		$key = 0;

		for($i = 0; $i < strlen($str); $i++) {
			if(!$escaped) {
				if($str[$i] == '\\') {
					$escaped = true;
				} elseif($str[$i] == ':') {
					$key++;
					$parsed[$key] = '';

				} elseif($str[$i] == '=' && count($parsed) > 1) {
					$tmp = $parsed[$key];
					unset($parsed[$key]);
					$key = $tmp;
					$parsed[$key] = '';

				} elseif($str[$i] == ',' && count($parsed) > 1) {
					$parsed[$key] = ScalarParser::parseBool($parsed[$key], $parsed[$key]);

					$key = count($parsed);
					$parsed[$key] = '';

				} else {
					$parsed[$key] .= $str[$i];
				}
			} else {
				$escaped = false;
				$parsed[$key] .= $str[$i];
			}
		}

		$parsed[$key] = ScalarParser::parseBool($parsed[$key], $parsed[$key]);

		return count($parsed) > 1
				? array($parsed[0], array_slice($parsed, 1))
				: array($parsed[0], array());
	}


	/**
	 * Generates random human readable token.
	 * Ambiguous chars like 0 and O are ommitted.
	 * Combines vowels with consonants and numbers.
	 *
	 * @author Pavel Maca
	 *
	 * @param  int length
	 * @return string
	 */
	public static function randomHumanToken($length = 8) {
		$numbers = '123456789';
		$vowels = 'aeiuy';
		$consonants = 'bcdfghjkmnpqrstvwxz';
		$s = '';
		for ($i = 0; $i < $length; $i++) {
			if(mt_rand(0, 10) % 3 === 0){
				$group = $numbers;
				$s .= $group{mt_rand(0, strlen($group) - 1)};
				continue;
			}
			$group = $i % 2 === 0 ? $consonants : $vowels;
			$s .= $group{mt_rand(0, strlen($group) - 1)};
		}
		return $s;
	}


	/**
	 * Splits string into tokens using specified separator with maintaining
	 * posibility to use escaped separator within string.
	 *
	 * @param string string to split
	 * @param string separator
	 * @param string escape sequence
	 * @param bool ignore empty tokens?
	 *
	 * @return string
	 * @throws Nette\InvalidArgumentException
	 */
	public static function splitWithEscape($str, $separator = '/', $escape = '\\', $ignoreEmpty = true) {

		if(!is_string($str))
			throw new Nette\InvalidArgumentException("First argument has to be string");

		if(!is_string($separator))
			throw new Nette\InvalidArgumentException("Second argument has to be string");

		if(!is_string($escape))
			throw new Nette\InvalidArgumentException("Second argument has to be string");

		if($separator == $escape)
			throw new Nette\InvalidArgumentException("Separator cannot be equal to escape");

		// ----

		$tokens = array();
		$index = 0;
		$escaped = false;
		for($i = 0; $i < mb_strlen($str); ) {

			if($escaped) {
				$escaped = false;
				$i++;
				continue;
			}

			// ...ESCAPE
			//    ^
			if(mb_substr($str, $i, mb_strlen($escape)) == $escape) {
				$escaped = true;
				$str = mb_substr($str, 0, $i) . mb_substr($str, $i + mb_strlen($escape));
				continue;
			}

			// ...SEPARATOR
			//    ^
			if(mb_substr($str, $i, mb_strlen($separator)) == $separator) {
				if($i - $index > 0 || !$ignoreEmpty)
					$tokens[] = mb_substr($str, $index, $i - $index);

				$i += mb_strlen($separator);
				$index = $i;
				continue;
			}

			$i++;
		}

		if($i - $index > 0 || !$ignoreEmpty)
			$tokens[] = mb_substr($str, $index, $i - $index);

		return $tokens;
	}

}
