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

namespace vBuilder\Scheduler;

use vBuilder,
	Nette;

/**
 * Job scheduler
 *
 * Allows schedule job for asynchronous execution.
 * Each job is handled by it's own PHP script which is created in specified
 * folder which is meant to be monitored for filesystem changes. This way job
 * can be executed immediately when worker is available.
 * Although it is possible to execute scripts by Cron it's recommended to use
 * tools like inotifywait.
 *
 * Usage:
 * <code>
 * $scheduler->schedule('myJob1', '@myService::method', 1, 2, 3)
 * $scheduler->schedule('myJob2', 'MyClass::staticMethod', 1, 2, 3)
 * </code>
 *
 * @author Adam Staněk (velbloud)
 * @since May 25, 2014
 */
class Scheduler extends Nette\Object {

	/** @var vBuilder\Scheduler\JobStorage */
	private $storage;

	/** @var string path to boostrap */
	private $boostrapPath;

	public function __construct($boostrapPath, vBuilder\Scheduler\JobStorage $storage) {
		$this->boostrapPath = $boostrapPath;
		$this->storage = $storage;
	}

	/**
	 * Schedules job for execution
	 *
	 * @param string
	 * @param string callable
	 * @return string path to job script
	 */
	public function schedule($name, $callable, $arg1 = NULL) {

		if(!is_scalar($name) || $name == '')
			throw new Nette\InvalidArgumentException("Invalid job name");

		if(!is_scalar($callable) || $callable == '')
			throw new Nette\InvalidArgumentException("Invalid callable");

		list($class, $method) = explode('::', $callable);

		$metadata = array();
		$phpCode = ""
			. "\$container = require " . var_export($this->boostrapPath, TRUE) . ";\n"
			. "\$arguments = unserialize(<<<XXXXXZZZZ\n"
			. serialize(array_slice(func_get_args(), 2)) . "\n"
			. "XXXXXZZZZ\n"
			. ");\n";

		// Service
		if(strncmp($class, '@', 1) == 0) {
			$phpCode = $phpCode
				. 'return call_user_func_array(array($container->getService('
				. var_export(substr($class, 1), TRUE)  . '), '
				. var_export($method, TRUE)
				. '), $arguments);';
		}

		// Static
		else {
			$phpCode = $phpCode
				. 'return call_user_func_array('
				. var_export($callable, TRUE)
				. ', $arguments);';
		}

		$jobFile = $this->storage->createJob($name, $metadata, "$phpCode\n");
		if(!$jobFile)
			throw new Nette\InvalidStateException("Cannot schedule job $name");

		return $jobFile;
	}

}