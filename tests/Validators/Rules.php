<?php

use vBuilder\Validators\Validator,
	vBuilder\Validators\Rules,
	Tester\Assert;

require __DIR__ . '/../bootstrap.php';

// Positive test
test(function() {

	$structure = array(
		'name' => 'Jane',
		'surname' => 'Doe'
	);

	$rules = new Rules(array('name'));

	$rules->addRule(function ($value) use ($structure) {
		 return $value == $structure['name'];
	}, 'msg');

	$errors = array();
	$success = $rules->validate($structure, $errors);

	Assert::true($success);
	Assert::type('array', $errors);
	Assert::same(count($errors), 0);

});

// Negative test
test(function() {

	$structure = array(
		'name' => 'Jane',
		'surname' => 'Doe'
	);

	$rules = new Rules(array('name'));

	$rules->addRule(function ($value) use ($structure) {
		 return $value != $structure['name'];
	}, 'msg');

	$errors = array();
	$success = $rules->validate($structure, $errors);

	Assert::false($success);
	Assert::type('array', $errors);
	Assert::same(count($errors), 1);

});

// Test nested keys
test(function() {

	$structure = array(
		'address' => array(
			'street' => 'Main Street'
		)
	);

	$rules = new Rules(array('address', 'street'));

	$rules->addRule(function ($value) use ($structure) {
		 return $value == $structure['address']['street'];
	}, 'msg');

	$errors = array();
	$success = $rules->validate($structure, $errors);

	Assert::true($success);
	Assert::type('array', $errors);
	Assert::same(count($errors), 0);

});

// Test condition
test(function() {

	$structure = array(
		'type' => 1,
		'data' => 'A',
	);

	$rules = new Rules(array('data'));
	$rules
		->addConditionOn(array('type'), function ($value) { return $value == 1; })
			->addRule(function ($value) { return $value == 'A'; }, 'msg')
		->endCondition()
			->addRule(function ($value) { return is_string($value); }, 'msg');

	Assert::true($rules->validate($structure));

	$structure['data'] = 'B';
	Assert::false($rules->validate($structure));

	$structure['type'] = '2';
	Assert::true($rules->validate($structure));

	$rules = new Rules(array('data'));
	$rules
		->addConditionOn(array('type'), function ($value) { return $value == 1; })
			->addRule(function ($value) { return $value == 'A'; }, 'ifTrueRule')
		->elseCondition()
			->addRule(function ($value) { return $value == 'B'; }, 'ifFalseRule');

	Assert::true($rules->validate($structure));

	$structure['type'] = '1';
	Assert::false($rules->validate($structure));

	$structure['data'] = 'A';
	Assert::true($rules->validate($structure));

});


