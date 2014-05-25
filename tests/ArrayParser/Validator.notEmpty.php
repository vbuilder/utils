<?php

use vBuilder\ArrayParser\ArrayParser,
	vBuilder\ArrayParser\Context,
	vBuilder\ArrayParser\KeyParser,
	Tester\Assert;

require __DIR__ . '/../bootstrap.php';

// ArrayParser::NOT_EMPTY
test(function() {

	$context = new Context(array(
		'a' => '    ',
		'b' => ''
	));

	$rules = new KeyParser(array('a'));
	$rules->addRule(ArrayParser::NOT_EMPTY);
	Assert::true($rules->parse($context));

	$rules = new KeyParser(array('b'));
	$rules->addRule(ArrayParser::NOT_EMPTY);
	Assert::false($rules->parse($context));

	$rules = new KeyParser(array('a'));
	$rules->addFilter(ArrayParser::TRIM);
	$rules->addRule(ArrayParser::NOT_EMPTY);
	Assert::false($rules->parse($context));

});