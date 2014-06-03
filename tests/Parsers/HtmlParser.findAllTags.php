<?php

use vBuilder\Parsers\HtmlParser,
	Tester\Assert;

require __DIR__ . '/../bootstrap.php';

test(function() {

	// Tags with arguments
	Assert::same(
		array(array(4, 24, 'a', array('b' => 'c', 'qq' => '', 'foo' => ' a b '))),
		HtmlParser::findAllTags("aaaa<a b=c qq\nfoo=\" a b \" />bbbb", 'a', TRUE, TRUE)
	);

	// All tags
	Assert::same(
		array(array(0, 3, 'a'), array(3, 3, 'b')),
		HtmlParser::findAllTags('<a><b>')
	);

	// Case sensitivity
	Assert::same(
		array(array(0, 3, 'a')),
		HtmlParser::findAllTags('<a><A>', 'a', TRUE)
	);

	// Case insensitivity
	Assert::same(
		array(array(0, 3, 'a'), array(3, 3, 'A')),
		HtmlParser::findAllTags('<a><A>', 'a', FALSE)
	);

	// Correctly anchored names
	Assert::same(
		array(array(0, 3, 'a'), array(9, 5, 'a'), array(14, 4, 'a')),
		HtmlParser::findAllTags("<a><abcd><a /><a/>", 'a')
	);

	// Multiple names
	Assert::same(
		array(array(0, 3, 'a'), array(3, 3, 'b')),
		HtmlParser::findAllTags("<a><b><c>", array('a', 'b'))
	);

	// Badly written arguments
	Assert::same(
		array(array(0, 44, 'div', array('data-something' => '>', 'style' => 'color: red;')), array(48, 6, '/div', array())),
		HtmlParser::findAllTags('<div data-something=">" style="color: red;">Test</div>', 'div', TRUE, TRUE)
	);

});