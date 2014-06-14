<?php

use vBuilder\Utils\FileSystem,
	Tester\Assert;

require __DIR__ . '/../bootstrap.php';

test(function() {

	// Basic
	Assert::same(
		array('a', 'b', 'c'),
		FileSystem::getPathComponents('a/b/c', '/')
	);

	// Empty
	Assert::same(
		array(),
		FileSystem::getPathComponents('', '/')
	);

	// Not escaped spaces
	Assert::same(
		array('a b c', 'd'),
		FileSystem::getPathComponents('a b c/d', '/')
	);

	// Escaped spaces
	Assert::same(
		array('a b c', 'd'),
		FileSystem::getPathComponents('a\ b\ c/d', '/')
	);

	// Trailing separator
	Assert::same(
		array('a', 'b'),
		FileSystem::getPathComponents('a/b/', '/')
	);

	// Absolute path
	Assert::same(
		array('', 'a', 'b'),
		FileSystem::getPathComponents('/a/b', '/')
	);

	// Multiple separators
	Assert::same(
		array('a', 'b'),
		FileSystem::getPathComponents('a//b', '/')
	);

});