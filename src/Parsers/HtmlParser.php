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

use vBuilder\Utils\Strings,
	Nette;

/**
 * HTML parsing helpers
 *
 * @note Please note that this class is not meant to be a replacement for
 *   complex XML / HTML parsers. It is rather library of simple helpers
 *   for gathering HTML tags from possibly invalid code.
 *
 * @author Adam Staněk (velbloud)
 * @since Jun 3, 2014
 */
class HtmlParser {

	/**
	 * Finds all HTML tags
	 *
	 * @note Don't forget to call htmlspecialchars_decode() on attributes if necessary.
	 *
	 * @param string
	 * @param string|string[]|NULL tag name, if NULL all tags will be matched
	 * @param bool case sensitive?
	 * @param bool capture attributes?
	 *
	 * @return array(array( start, length, tagName ))
	 * @throws Nette\InvalidArgumentException if invalid tag name given
	 */
	static function findAllTags($str, $tag = NULL, $caseSensitive = TRUE, $captureAttributes = FALSE) {

		if($tag === NULL)
			$re_name = '(/?[a-z0-9]+)';

		elseif(is_array($tag) || $tag instanceof \Traversable) {
			$re_name = '';
			foreach($tag as $name)
				$re_name .= '|/?' . preg_quote($name, '#');

			if(strlen($re_name) > 1)
				$re_name = '(' . substr($re_name, 1) . ')';

		} elseif(is_scalar($tag))
			$re_name = '(/?' . preg_quote($tag, '#') . ')';

		else
			throw new Nette\InvalidArgumentException("Invalid tag name");

		if($re_name == '') return array();

		$re_flags = '';
		if(!$caseSensitive) $re_flags .= 'i';

		$re_start = '<' . $re_name . '([^a-zA-Z0-9])';
		$re_end = '>';

		$re_s_dq = '"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"';	// Double quotes string with escapes
		$re_s_sq = "'[^'\\\\]*(?:\\\\.[^'\\\\]*)*'";	// Single quotes string with escapes

		$matches = Strings::matchAll($str, "#$re_start|$re_s_dq|$re_s_sq|$re_end#sx$re_flags", PREG_OFFSET_CAPTURE);

		$info = NULL;
		$matched = array();
		foreach($matches as $m) {

			// Start tag
			if(strncmp($m[0][0], '<', 1) === 0) {

				// Tag without arguments
				if($m[2][0] == '>') {
					$info = array(
						$m[0][1], // start offset
						$m[2][1] - $m[0][1] + 1, // length
						$m[1][0] // tag name
					);

					if($captureAttributes)
						$info[] = array();

					$matched[] = $info;
					$info = NULL;
				}

				// Tag with arguments
				else
					$info = array(
						$m[0][1],
						NULL,
						$m[1][0]
					);
			}

			// End tag
			if(strncmp($m[0][0], '>', 1) === 0 && $info !== NULL) {
				$info[1] = $m[0][1] - $info[0] + 1; // length

				if($captureAttributes) {
					$attributes = array();
					$attrStr = substr($str, $info[0] + 1, $info[1] - 2);
					$attrStr = rtrim(substr($attrStr, strlen($info[2])), '/');

					$attrMatches = Strings::matchAll($attrStr, "#\\s+([^=\\s]+)(=($re_s_dq|$re_s_sq|\\S+))?#sx");
					foreach($attrMatches as $curr) {

						if(count($curr) == 4) {
							if(strncmp($curr[3], '"', 1) === 0 || strncmp($curr[3], "'", 1) === 0)
								$value = substr($curr[3], 1, -1);
							else
								$value = $curr[3];
						} else
							$value = "";

						$attributes[$curr[1]] = $value;
					}

					$info[] = $attributes;
				}

				$matched[] = $info;
				$info = NULL;
			}
		}

		return $matched;
	}

}
