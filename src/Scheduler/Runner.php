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
 * Job runner
 *
 * @author Adam Staněk (velbloud)
 * @since May 25, 2014
 */
class Runner extends Nette\Object {

	/** @var vBuilder\Scheduler\JobStorage */
	private $storage;

	public function __construct(vBuilder\Scheduler\JobStorage $storage) {
		$this->storage = $storage;
	}

	/**
	 * Run all jobs
	 */
	public function runAll() {
		$allJobs = $this->storage->getJobs();
		foreach($allJobs as $name => $jobs) {
			foreach($jobs as $jobScript) {
				$this->run($jobScript);
			}
		}
	}

	/**
	 * Run specific job script
	 *
	 * @param string
	 */
	public function run($jobScript) {

		$this->jobStarted($jobScript);

		// -----

		$phpBin = 'php';
		$proc = proc_open(
			$phpBin . ' ' . escapeshellarg($jobScript),
			array(
				array('pipe', 'r'),
				array('pipe', 'w'),
				array('pipe', 'w'),
			),
			$pipes,
			dirname($jobScript),
			NULL,
			array('bypass_shell' => TRUE)
		);

		list($stdin, $stdout, $stderr) = $pipes;
		fclose($stdin);

		do {
			$status = proc_get_status($proc);

		} while($status['running']);

		// -----

		$result = $status['exitcode'] == 0;
		if($result) $this->jobFinished($jobScript, $stdout, $stderr);
		else $this->jobFailed($jobScript, $stdout, $stderr);

		fclose($stdout);
		fclose($stderr);
		proc_close($proc);

		/// @todo error handling
		if($result) {
			unlink($jobScript);
		} else {
			$dir = $this->storage->directory . '/failed';
			if(!is_dir($dir)) mkdir($dir);

			rename($jobScript, $dir . '/' . basename($jobScript));
		}

		return $result;
	}

	protected function jobStarted($jobScript) {

	}

	protected function jobFinished($jobScript, $stdoutHandle, $stderrHandle) {

	}

	protected function jobFailed($jobScript, $stdoutHandle, $stderrHandle) {

	}


}