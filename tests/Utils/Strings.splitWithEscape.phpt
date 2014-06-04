<?php

use vBuilder\Utils\Strings,
	Tester\Assert;

require __DIR__ . '/../bootstrap.php';

test(function() {

	// Empty
	Assert::same(
		array(),
		Strings::splitWithEscape('')
	);

	// Separator
	Assert::same(
		array('a', 'b'),
		Strings::splitWithEscape('a/b', '/')
	);

	// Escaped
	Assert::same(
		array('a/b'),
		Strings::splitWithEscape('a\\/b', '/', '\\')
	);

	// Skip empty
	Assert::same(
		array('a', 'b'),
		Strings::splitWithEscape('a//b', '/', '\\', TRUE)
	);

	// Do not skip empty
	Assert::same(
		array('a', '',  'b'),
		Strings::splitWithEscape('a//b', '/', '\\', FALSE)
	);

});