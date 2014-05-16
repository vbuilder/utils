<?php

// The Nette Tester command-line runner can be
// invoked through the command: ../vendor/bin/tester .

$autoloadSearchPath = array(
	// Only vBuilder Utils and Composer
	__DIR__ . '/../vendor/autoload.php',

	// In Composer project
	__DIR__ . '/../../../autoload.php'
);

foreach($autoloadSearchPath as $curr) {
	if(file_exists($curr)) {
		require $curr;
		unset($autoloadSearchPath);
		break;
	}
}

if(isset($autoloadSearchPath)) {
	echo 'Install Nette Tester using `composer update --dev`';
	exit(1);
}

// configure environment
Tester\Environment::setup();
date_default_timezone_set('Europe/Prague');


// create temporary directory
define('TEMP_DIR', __DIR__ . '/../temp/' . getmypid());
@mkdir(dirname(TEMP_DIR)); // @ - directory may already exist
Tester\Helpers::purge(TEMP_DIR);


function before(\Closure $function = NULL)
{
	static $val;
	if (!func_num_args()) {
		return ($val ? $val() : NULL);
	}
	$val = $function;
}


function test(\Closure $function)
{
	before();
	$function();
}
