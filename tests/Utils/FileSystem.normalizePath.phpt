<?php

use vBuilder\Utils\FileSystem,
	Tester\Assert;

require __DIR__ . '/../bootstrap.php';

test(function() {

	// Basic
	Assert::same(
		'a/b/c',
		FileSystem::normalizePath('a/b/c', '/')
	);

	// Basic 2
	Assert::same(
		'a/c',
		FileSystem::normalizePath('a/b/../c', '/')
	);

	// Trailing dot
	Assert::same(
		'a/b/c',
		FileSystem::normalizePath('a/b/c/.', '/')
	);

	// Current directory dots
	Assert::same(
		array('a', 'b', 'd'),
		FileSystem::normalizePath(array('.', 'a', '.', '.', 'b', 'c', '..', 'd'), FALSE)
	);

	// Starting ..
	Assert::same(
		'../a/b',
		FileSystem::normalizePath('../a/b', '/')
	);

	// Trailing ..
	Assert::same(
		'a',
		FileSystem::normalizePath('a/b/..', '/')
	);

	// Complex 1
	Assert::same(
		'../../c',
		FileSystem::normalizePath('../a/b/../../../c', '/')
	);

	// Trailing separator
	Assert::same(
		'a/b',
		FileSystem::normalizePath('a/b/', '/')
	);

	// Absolute path
	Assert::same(
		'/c',
		FileSystem::normalizePath('/a/b/../../c', '/')
	);

	// Absolute path in component mode
	Assert::same(
		array('', 'c'),
		FileSystem::normalizePath(array('', 'a', 'b', '..', '..', 'c'), FALSE)
	);

});