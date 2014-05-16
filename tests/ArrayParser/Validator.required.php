<?php

use vBuilder\ArrayParser,
	vBuilder\ArrayParser\Context,
	vBuilder\ArrayParser\KeyParser,
	Tester\Assert;

require __DIR__ . '/../bootstrap.php';

// ArrayParser::REQUIRED
test(function() {

	$context = new Context(array(
		'foo' => 'bar'
	));

	$rules = new KeyParser(array('foo'));
	$rules->addRule(ArrayParser::REQUIRED);
	Assert::true($rules->parse($context));

	$rules = new KeyParser(array('bar'));
	$rules->addRule(ArrayParser::REQUIRED);
	Assert::false($rules->parse($context));

});