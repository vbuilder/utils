<?php

use vBuilder\ArrayParser,
	vBuilder\ArrayParser\Context,
	vBuilder\ArrayParser\KeyParser,
	Tester\Assert;

require __DIR__ . '/../bootstrap.php';

// ArrayParser::DEFAULT_VALUE
test(function() {

	$context = new Context(array(
		'a' => '',
		'b' => 0,
		'c' => FALSE,
		'd' => NULL
	));

	// Non existing key
	$rules = new KeyParser(array('foo', 'bar'));
	$rules->addFilter(ArrayParser::DEFAULT_VALUE, 11);
	Assert::true($rules->parse($context));
	Assert::equal(
		array('a' => '', 'b' => 0, 'c' => FALSE, 'd' => NULL, 'foo' => array('bar' => 11)),
		$context->data
	);

	// NULL value
	$rules = new KeyParser(array('d'));
	$rules->addFilter(ArrayParser::DEFAULT_VALUE, 11);
	Assert::true($rules->parse($context));
	Assert::equal(
		array('a' => '', 'b' => 0, 'c' => FALSE, 'd' => 11, 'foo' => array('bar' => 11)),
		$context->data
	);

	// Empty string
	$rules = new KeyParser(array('a'));
	$rules->addFilter(ArrayParser::DEFAULT_VALUE, 11);
	Assert::true($rules->parse($context));
	Assert::equal(
		array('a' => '', 'b' => 0, 'c' => FALSE, 'd' => 11, 'foo' => array('bar' => 11)),
		$context->data
	);

	// Zero
	$rules = new KeyParser(array('b'));
	$rules->addFilter(ArrayParser::DEFAULT_VALUE, 11);
	Assert::true($rules->parse($context));
	Assert::equal(
		array('a' => '', 'b' => 0, 'c' => FALSE, 'd' => 11, 'foo' => array('bar' => 11)),
		$context->data
	);

	// FALSE
	$rules = new KeyParser(array('c'));
	$rules->addFilter(ArrayParser::DEFAULT_VALUE, 11);
	Assert::true($rules->parse($context));
	Assert::equal(
		array('a' => '', 'b' => 0, 'c' => FALSE, 'd' => 11, 'foo' => array('bar' => 11)),
		$context->data
	);

});

