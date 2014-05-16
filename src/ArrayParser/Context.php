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

use Nette;

/**
 * Object for holding parser context
 *
 * @author Adam Staněk (velbloud)
 * @since May 16, 2013
 */
class Context extends Nette\Object {

	/** @var array */
	private $data;

	/** @var Rule|NULL */
	private $rule;

	/** @var reference */
	private $value;

	/** @var bool should I refresh value reference? */
	private $dirtyReference = FALSE;

	/** @var array */
	public $baseKey = array();

	/** @var array */
	public $errors = array();

	public function __construct(array $data = array()) {
		$this->setData($data);
	}

	public function setData(array $data = array()) {
		$this->dirtyReference = TRUE;
		$this->data = $data;
	}

	public function & getData() {
		return $this->data;
	}

	public function & getRule() {
		return $this->rule;
	}

	public function setRule(Rule $rule = NULL) {
		$this->dirtyReference = TRUE;
		$this->rule = $rule;
	}

	public function & getValue() {
		if($this->dirtyReference) {
			$this->dirtyReference = FALSE;

			$found = TRUE;
			$ref = &$this->data;
			foreach($this->absoluteKey as $k) {
				if($ref === NULL) $ref = array();
				if(!array_key_exists($k, $ref)) {
					$found = FALSE;
					$ref[$k] = NULL;
				}

				$ref = &$ref[$k];
			}

			$this->value = &$ref;
		}

		return $this->value;
	}

	public function setValue($value) {
		if($this->dirtyReference) $this->getValue();
		$this->value = $value;
	}

	public function getAbsoluteKey() {
		return array_merge($this->baseKey, $this->rule->key);
	}

	public function getPrintableKey() {
		$key = $this->absoluteKey;
		return count($key) == 1 ? "'" . $key[0] . "'" : "['" . implode($key, "', '") . "']";
	}

}