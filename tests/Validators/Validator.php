<?php

use vBuilder\Validators\Validator,
	vBuilder\Validators\Rules,
	Tester\Assert;

require __DIR__ . '/../bootstrap.php';

test(function() {

	$structure = array(
		'name' => 'Jane',
		'surname' => 'Doe'
	);

	$validator = new Validator;

	$validator->addKey('name')
		->addRule(Validator::REQUIRED);

	$validator->addKey('surname')
		->addRule(Validator::REQUIRED);

	$errors = array();
	$success = $validator->validate($structure, $errors);

	Assert::true($success);
	Assert::type('array', $errors);
	Assert::same(count($errors), 0);

	$structure = array(
		'name' => NULL,
		'surname' => NULL
	);

	$success = $validator->validate($structure, $errors);

	Assert::false($success);
	Assert::type('array', $errors);
	Assert::same(count($errors), 2);

});

// -----------------------------------------------------------------------------

// Validator::REQUIRED
test(function() {

	$structure = array(
		'foo' => 'bar'
	);

	$rules = new Rules(array('foo'));
	$rules->addRule(Validator::REQUIRED);
	Assert::true($rules->validate($structure));

	$rules = new Rules(array('bar'));
	$rules->addRule(Validator::REQUIRED);
	Assert::false($rules->validate($structure));

});

// Validator::IN_ARRAY
test(function() {

	$structure = array(
		'foo' => 'bar'
	);

	$rules = new Rules(array('foo'));
	$rules->addRule(Validator::IN_ARRAY, NULL, array('bar', 'bar2'));
	Assert::true($rules->validate($structure));

	$rules = new Rules(array('foo'));
	$rules->addRule(Validator::IN_ARRAY, NULL, array('bar2', 'bar3'));
	Assert::false($rules->validate($structure));

});

// Validator::SCALAR
test(function() {

	$structure = array(
		'foo' => 'bar',
		'foo2' => array(
			1, 2, 3
		)
	);

	$rules = new Rules(array('foo'));
	$rules->addRule(Validator::SCALAR);
	Assert::true($rules->validate($structure));

	$rules = new Rules(array('foo2'));
	$rules->addRule(Validator::SCALAR);
	Assert::false($rules->validate($structure));

});