<?php

use vBuilder\IO\PhpFileStorage,
	Tester\Assert;

require __DIR__ . '/../bootstrap.php';

test(function() {

	$file = TEMP_DIR . '/test.php';
	$metadata = array('foo' => 123);
	$data = 'Hello';

	$storage = new PhpFileStorage;
	$storage->write($file, $metadata, $data);

	Assert::true(file_exists($file));

	$read = $storage->read($file);
	Assert::same($data, $read[PhpFileStorage::DATA]);
	Assert::equal($metadata, $read[PhpFileStorage::METADATA]);

	Assert::same($data, $storage->read($file, PhpFileStorage::DATA));
	Assert::equal($metadata, $storage->read($file, PhpFileStorage::METADATA));
});