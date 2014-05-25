<?php

use vBuilder\Utils\FileSystem,
	Tester\Assert;

require __DIR__ . '/../bootstrap.php';

test(function() {

	Assert::same(
		'a/b/c',
		FileSystem::normalizePath('a/b/c', '/')
	);

	Assert::same(
		'a/b/c',
		FileSystem::normalizePath('a/b/c/.', '/')
	);

	Assert::same(
		'a/b/c/d',
		FileSystem::normalizePath('a/b/c/./d', '/')
	);

	Assert::same(
		'a/b/d',
		FileSystem::normalizePath('./a/b/c/../d', '/')
	);

	Assert::same(
		'../a/b',
		FileSystem::normalizePath('../a/b', '/')
	);

	Assert::same(
		'a/b',
		FileSystem::normalizePath('a/b/', '/')
	);

	Assert::same(
		'../../c',
		FileSystem::normalizePath('../a/b/../../../c', '/')
	);

});