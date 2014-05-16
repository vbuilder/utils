<?php

use vBuilder\ArrayParser\Validator,
	vBuilder\ArrayParser\KeyParser,
	Tester\Assert;

require __DIR__ . '/../bootstrap.php';

// Validator::REQUIRED
test(function() {

	$structure = array(
		'foo' => 'bar'
	);

	$rules = new KeyParser(array('foo'));
	$rules->addRule(Validator::REQUIRED);
	Assert::true($rules->parse($structure));

	$rules = new KeyParser(array('bar'));
	$rules->addRule(Validator::REQUIRED);
	Assert::false($rules->parse($structure));

});

// Validator::IN_ARRAY
test(function() {

	$structure = array(
		'foo' => 'bar'
	);

	$rules = new KeyParser(array('foo'));
	$rules->addRule(Validator::IN_ARRAY, NULL, array('bar', 'bar2'));
	Assert::true($rules->parse($structure));

	$rules = new KeyParser(array('foo'));
	$rules->addRule(Validator::IN_ARRAY, NULL, array('bar2', 'bar3'));
	Assert::false($rules->parse($structure));

});

// Validator::SCALAR
test(function() {

	$structure = array(
		'foo' => 'bar',
		'foo2' => array(
			1, 2, 3
		)
	);

	$rules = new KeyParser(array('foo'));
	$rules->addRule(Validator::SCALAR);
	Assert::true($rules->parse($structure));

	$rules = new KeyParser(array('foo2'));
	$rules->addRule(Validator::SCALAR);
	Assert::false($rules->parse($structure));

});