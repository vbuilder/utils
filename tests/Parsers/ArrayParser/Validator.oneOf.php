<?php

use vBuilder\Parsers\ArrayParser,
	vBuilder\Parsers\ArrayParser\Context,
	vBuilder\Parsers\ArrayParser\KeyParser,
	Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

// ArrayParser::ONE_OF
test(function() {

	$context = new Context(array(
		'foo' => 'bar'
	));

	$rules = new KeyParser(array('foo'));
	$rules->addRule(ArrayParser::ONE_OF, array('bar', 'bar2'));
	Assert::true($rules->parse($context));

	$rules = new KeyParser(array('foo'));
	$rules->addRule(ArrayParser::ONE_OF, array('bar2', 'bar3'));
	Assert::false($rules->parse($context));

});