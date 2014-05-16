<?php

use vBuilder\ArrayParser\KeyParser,
	Tester\Assert;

require __DIR__ . '/../bootstrap.php';

// Positive test
test(function() {

	$structure = array(
		'name' => 'Jane',
		'surname' => 'Doe'
	);

	$rules = new KeyParser(array('name'));

	$rules->addRule(function ($value) use ($structure) {
		 return $value == $structure['name'];
	}, 'msg');

	$errors = array();
	$success = $rules->parse($structure, $errors);

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

	$rules = new KeyParser(array('name'));

	$rules->addRule(function ($value) use ($structure) {
		 return $value != $structure['name'];
	}, 'msg');

	$errors = array();
	$success = $rules->parse($structure, $errors);

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

	$rules = new KeyParser(array('address', 'street'));

	$rules->addRule(function ($value) use ($structure) {
		 return $value == $structure['address']['street'];
	}, 'msg');

	$errors = array();
	$success = $rules->parse($structure, $errors);

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

	$rules = new KeyParser(array('data'));
	$rules
		->addConditionOn(array('type'), function ($value) { return $value == 1; })
			->addRule(function ($value) { return $value == 'A'; }, 'msg')
		->endCondition()
			->addRule(function ($value) { return is_string($value); }, 'msg');

	Assert::true($rules->parse($structure));

	$structure['data'] = 'B';
	Assert::false($rules->parse($structure));

	$structure['type'] = '2';
	Assert::true($rules->parse($structure));

	$rules = new KeyParser(array('data'));
	$rules
		->addConditionOn(array('type'), function ($value) { return $value == 1; })
			->addRule(function ($value) { return $value == 'A'; }, 'ifTrueRule')
		->elseCondition()
			->addRule(function ($value) { return $value == 'B'; }, 'ifFalseRule');

	Assert::true($rules->parse($structure));

	$structure['type'] = '1';
	Assert::false($rules->parse($structure));

	$structure['data'] = 'A';
	Assert::true($rules->parse($structure));

});

// Test auto key creation
test(function() {

	$structure = array();

	$rules = new KeyParser(array('address', 'street'));

	$rules->addRule(function (&$value) {
		$value = 'Main Street';
		return TRUE;

	}, 'msg');

	$parsed = $rules->parse($structure);

	Assert::equal(array('address' => array('street' => 'Main Street')), $structure);

});


