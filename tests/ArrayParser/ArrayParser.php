<?php

use vBuilder\ArrayParser,
	Tester\Assert;

require __DIR__ . '/../bootstrap.php';

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

test(function() {

	$validator = new ArrayParser;
	$validator->addKey('value')
		->addFilter(ArrayParser::DEFAULT_VALUE, 0);

	$structure = array();
	$parsed = $validator->parse($structure, $errors);
	Assert::equal(array('value' => 0), $parsed);

});

test(function() {

	$validator = new ArrayParser;
	$validator->addKey('data')
		->addCondition(ArrayParser::SCALAR)
			->AddFilter(ArrayParser::SIMPLIFY)
			->AddRule(ArrayParser::NOT_EMPTY)
		->elseCondition()
			->AddFilter(ArrayParser::SERIALIZE);

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

