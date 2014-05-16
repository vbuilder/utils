<?php

use vBuilder\ArrayParser\Validator,
	vBuilder\ArrayParser,
	Tester\Assert;

require __DIR__ . '/../bootstrap.php';

test(function() {

	$structure = array(
		'name' => 'Jane',
		'surname' => 'Doe'
	);

	$validator = new ArrayParser;

	$validator->addKey('name')
		->addRule(Validator::REQUIRED);

	$validator->addKey('surname')
		->addRule(Validator::REQUIRED);

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

