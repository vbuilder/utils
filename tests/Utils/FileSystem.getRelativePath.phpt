<?php

use vBuilder\Utils\FileSystem,
	Tester\Assert;

require __DIR__ . '/../bootstrap.php';

test(function() {

	// Basic
	Assert::same(
		'../..',
		FileSystem::getRelativePath('a/b/c', 'a', '/')
	);

	// Basic: component mode
	Assert::same(
		array('..', '..'),
		FileSystem::getRelativePath(array('a', 'b', 'c'), array('a'), FALSE)
	);

	// Up and down
	Assert::same(
		'../../d/e',
		FileSystem::getRelativePath('a/b/c', 'a/d/e', '/')
	);

	// Down
	Assert::same(
		'b/c',
		FileSystem::getRelativePath('a', 'a/b/c', '/')
	);

	// Empty target
	Assert::same(
		'../../..',
		FileSystem::getRelativePath('a/b/c', '', '/')
	);

	// Empty from
	Assert::same(
		'a/b/c',
		FileSystem::getRelativePath('', 'a/b/c', '/')
	);

	// Same
	Assert::same(
		'',
		FileSystem::getRelativePath('a/b/c', 'a/b/c', '/')
	);

	// Full path
	Assert::same(
		'd',
		FileSystem::getRelativePath('a/b/c', 'a/b/c/d', '/')
	);

	// Absolute path
	Assert::same(
		'../../d',
		FileSystem::getRelativePath('/a/b/c', '/a/d', '/')
	);

	// Absolute from and relative target
	Assert::same(
		'a/d',
		FileSystem::getRelativePath('/a/b/c', 'a/d', '/')
	);

});