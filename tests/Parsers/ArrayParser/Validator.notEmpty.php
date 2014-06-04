<?php

use vBuilder\Parsers\ArrayParser,
	vBuilder\Parsers\ArrayParser\Context,
	vBuilder\Parsers\ArrayParser\KeyParser,
	Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

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