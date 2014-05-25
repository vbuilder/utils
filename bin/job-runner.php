<?php

use vBuilder\Utils\FileSystem;

$container = require __DIR__ . '/bootstrap.php';

// -----------------------------------------------------------------------------

class Runner extends vBuilder\Scheduler\Runner {

	protected function jobStarted($jobScript) {
		$file = FileSystem::getRelativePath(getcwd(), $jobScript);
		echo "Running \033[0;36m$file:\033[0m";

		// Call parent
		return call_user_func_array(array('parent', __FUNCTION__), func_get_args());
	}

	protected function jobFinished($jobScript, $stdoutHandle, $stderrHandle) {
		$output = stream_get_contents($stdoutHandle);
		if($output != "")
			echo "\n\n$output\n\033[0;32mJob finished\033[0m\n";
		else
			echo " \033[0;32mFinished\033[0m\n";

		// Call parent
		return call_user_func_array(array('parent', __FUNCTION__), func_get_args());
	}

	protected function jobFailed($jobScript, $stdoutHandle, $stderrHandle) {
		$output = stream_get_contents($stdoutHandle);
		if($output != "")
			echo "\n\n$output\n\033[0;31m!!! Job FAILED\033[0m\n";
		else
			echo " \033[0;31mFailed\033[0m\n";

		// Log error (and notify by e-mail if set)
		Nette\Diagnostics\Debugger::log('Job ' . $jobScript . ' failed', 'error');

		// Call parent
		return call_user_func_array(array('parent', __FUNCTION__), func_get_args());
	}

}

// -----------------------------------------------------------------------------

$runner = new Runner($container->getService('scheduler.jobStorage'));

echo "\n";
$runner->runAll();
echo "\n";
