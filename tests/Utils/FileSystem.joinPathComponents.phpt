<?php

use vBuilder\Utils\FileSystem,
	Tester\Assert;

require __DIR__ . '/../bootstrap.php';

test(function() {

	// Basic
	Assert::same(
		'a/b/c',
		FileSystem::joinPathComponents(array('a', 'b', 'c'), '/')
	);

	// Empty
	Assert::same(
		'',
		FileSystem::joinPathComponents(array(), '/')
	);

	// Empty components
	Assert::same(
		'a/b/c',
		FileSystem::joinPathComponents(array('a', 'b', '', 'c', ''), '/')
	);

	// Absolute path
	Assert::same(
		'/a/b/c',
		FileSystem::joinPathComponents(array('', 'a', 'b', 'c'), '/')
	);

});