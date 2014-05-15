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

use vBuilder\Utils\Strings,
	Nette;

/**
 * Validator
 *
 * @author Adam Staněk (velbloud)
 * @since May 15, 2013
 */
class Validator extends Nette\Utils\ArrayHash {

	/** Helpers **/
	const REQUIRED = ':required';
	const IN_ARRAY = ':inArray';
	const SCALAR = ':scalar';
	/**/

	public function validate(&$structure, &$errors = array()) {
		$success = TRUE;

		foreach($this as $rules) {
			if(!$rules->validate($structure, $errors))
				$success = FALSE;
		}

		return $success;
	}

	public function addKey($key) {
		if(!isset($this[$key]))
			$this[$key] = new Rules(array($key));

		return $this[$key];
	}

	/**
	 * Adds new rule.
	 * @param  mixed
	 * @param  vBuilder\Validators\Rules
	 * @return void
	 */
	public function offsetSet($index, $rule) {
		if(!$rule instanceof Rules) {
			throw new Nette\InvalidArgumentException('Argument must be vBuilder\Validators\Rules descendant.');
		}

		parent::offsetSet($index, $rule);
	}

}