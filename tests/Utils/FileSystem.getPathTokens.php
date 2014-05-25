<?php

use vBuilder\Utils\FileSystem,
	Tester\Assert;

require __DIR__ . '/../bootstrap.php';

test(function() {

	Assert::same(
		array('a', 'b', 'c'),
		FileSystem::getPathTokens('a/b/c', '/')
	);

	Assert::same(
		array('a b c', 'd'),
		FileSystem::getPathTokens('a b c/d', '/')
	);

	Assert::same(
		array('a b c', 'd'),
		FileSystem::getPathTokens('a\ b\ c/d', '/')
	);

	Assert::same(
		array('a/b c', 'd'),
		FileSystem::getPathTokens('a\/b\ c/d', '/')
	);

});