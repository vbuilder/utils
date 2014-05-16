<?php

use vBuilder\ArrayParser,
	Tester\Assert;

require __DIR__ . '/../bootstrap.php';

// Simple validator
test(function() {

	$structure = array(
		'name' => 'Jane',
		'surname' => 'Doe'
	);

	$validator = new ArrayParser;

	$validator->addKey('name')
		->addRule(ArrayParser::REQUIRED);

	$validator->addKey('surname')
		->addRule(ArrayParser::REQUIRED);

	$errors = array();
	$parsed = $validator->parse($structure, $errors);

	Assert::equal($structure, $parsed);
	Assert::type('array', $errors);
	Assert::same(count($errors), 0);

	$structure = array(
		'name' => NULL,
		'surname' => NULL
	);

	$success = $validator->parse($structure, $errors);

	Assert::false($success);
	Assert::type('array', $errors);
	Assert::same(count($errors), 2);

});


// Simple filter
test(function() {

	$validator = new ArrayParser;
	$validator->addKey('value')
		->addFilter(ArrayParser::DEFAULT_VALUE, 0);

	$structure = array();
	$parsed = $validator->parse($structure, $errors);
	Assert::equal(array('value' => 0), $parsed);

});

// Conditional
test(function() {

	$validator = new ArrayParser;
	$validator->addKey('data')
		->addCondition(ArrayParser::SCALAR)
			->addFilter(ArrayParser::SIMPLIFY)
			->addRule(ArrayParser::NOT_EMPTY)
		->elseCondition()
			->addFilter(ArrayParser::SERIALIZE);

	$structure = array('data' => ' hello   world  ');
	$parsed = $validator->parse($structure, $errors);
	Assert::equal(array('data' => 'hello world'), $parsed);

	$structure = array('data' => '     ');
	$parsed = $validator->parse($structure, $errors);
	Assert::false($parsed);

	$structure = array('data' => array('a' => 1));
	$parsed = $validator->parse($structure, $errors);
	Assert::equal(array('data' => 'a:1:{s:1:"a";i:1;}'), $parsed);

});

// Nested
test(function() {

	$addressParser = new ArrayParser;

	$addressParser->addKey('street')
		->addRule(ArrayParser::SCALAR)
		->addFilter(ArrayParser::SIMPLIFY)
		->addRule(ArrayParser::NOT_EMPTY);

	$addressParser->addKey('city')
		->addRule(ArrayParser::SCALAR)
		->addFilter(ArrayParser::SIMPLIFY)
		->addRule(ArrayParser::NOT_EMPTY);

	$personParser  = new ArrayParser;

	$personParser->addKey('name')
		->addRule(ArrayParser::SCALAR)
		->addFilter(ArrayParser::SIMPLIFY)
		->addRule(ArrayParser::NOT_EMPTY);

	$personParser->addKey('address')
		->addRule(ArrayParser::STRUCTURE, $addressParser);

	$data = array(
		'name' => 'Jane Doe',
		'address' => array(
			'street' => 'Blue Street 42',
			'city' => 'Boston'
		)
	);

	$parsed = $personParser->parse($data);
	Assert::equal($data, $parsed);

	$data = array(
		'name' => 'Jane Doe',
		'address' => array(
			'street' => 'Blue Street 42',
			'city' => ''
		)
	);

	$parsed = $personParser->parse($data, $errors);
	Assert::false($parsed);
});




