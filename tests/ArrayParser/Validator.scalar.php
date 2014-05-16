<?php

use vBuilder\ArrayParser,
	vBuilder\ArrayParser\Context,
	vBuilder\ArrayParser\KeyParser,
	Tester\Assert;

require __DIR__ . '/../bootstrap.php';

// ArrayParser::SCALAR
test(function() {

	$context = new Context(array(
		'foo' => 'bar',
		'foo2' => array(
			1, 2, 3
		)
	));

	$rules = new KeyParser(array('foo'));
	$rules->addRule(ArrayParser::SCALAR);
	Assert::true($rules->parse($context));

	$rules = new KeyParser(array('foo2'));
	$rules->addRule(ArrayParser::SCALAR);
	Assert::false($rules->parse($context));

});