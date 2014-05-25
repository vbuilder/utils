<?php
// This file is fake boostrap for our scripts.
// It searches for vBuilder framework boostrap

if(!isset($bootstrapSearchPath))
	$bootstrapSearchPath = array();

$bootstrapSearchPath[] =
	__DIR__
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'framework'
	. DIRECTORY_SEPARATOR . 'bin'
	. DIRECTORY_SEPARATOR . 'bootstrap.php';

foreach($bootstrapSearchPath as $path) {
	if(file_exists($path)) {
		unset($bootstrapSearchPath);
		return require $path;
	}
}

echo "\nBootstrap not found\n";
echo "Search paths:\n";
echo "\t" . implode($bootstrapSearchPath, "\n\t") . "\n";
echo "\n";

exit(1);
