<?php

use vBuilder\Scheduler\Scheduler,
	vBuilder\Scheduler\Runner,
	vBuilder\Scheduler\JobStorage,
	Tester\Assert;

require __DIR__ . '/../bootstrap.php';

if(!isset($autoloadPath))
	Assert::fail('Autoloader path not known. Cannot continue.');

$jobBootstrapPath = TEMP_DIR . '/bootstrap.php';
$jobBootstrapContent = "<?php\n"
	. "define('TEMP_DIR', __DIR__);\n"
	. 'require ' . var_export($autoloadPath, TRUE) . ";\n"
	. 'return require ' . var_export(__DIR__ . '/resources/job-bootstrap.php', TRUE) . ";\n";

if(file_put_contents($jobBootstrapPath, $jobBootstrapContent) === FALSE)
	Assert::fail('Unable to create fake bootstrap. Cannot continue.');

// -----------------------------------------------------------------------------

test(function() use ($jobBootstrapPath) {

	$storage = new JobStorage(TEMP_DIR);
	$scheduler = new Scheduler($jobBootstrapPath, $storage);
	$runner = new Runner($storage);

	// Service method
	$jobScript1 = $scheduler->schedule('test1', '@myService::method1', 1, 2, 3);
	Assert::true($jobScript1 !== FALSE);
	Assert::true(file_exists($jobScript1));

	// Static method
	$jobScript2 = $scheduler->schedule('test2', 'MyService::method2', 3, 2, 1);
	Assert::true($jobScript2 !== FALSE);
	Assert::true(file_exists($jobScript2));

	// Throwing exception
	$jobScript3 = $scheduler->schedule('test3', 'MyService::method3', 3, 2, 1);
	Assert::true($jobScript3 !== FALSE);
	Assert::true(file_exists($jobScript3));

	$runner->runAll();

	// Check it scripts were called with correct arguments
	// (see: resources/job-bootstrap.php)
	Assert::true(file_exists(TEMP_DIR . '/method1.called.txt'));
	Assert::true(file_exists(TEMP_DIR . '/method2.called.txt'));
	Assert::equal(array(1, 2, 3), unserialize(file_get_contents(TEMP_DIR . '/method1.called.txt')));
	Assert::equal(array(3, 2, 1), unserialize(file_get_contents(TEMP_DIR . '/method2.called.txt')));

	// Finished scripts should be deleted
	Assert::false(file_exists($jobScript1));
	Assert::false(file_exists($jobScript2));

	// Failed script should be moved
	Assert::false(file_exists($jobScript3));
	Assert::true(file_exists(TEMP_DIR . '/failed/' . basename($jobScript3)));

});