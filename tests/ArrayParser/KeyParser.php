<?php

use vBuilder\ArrayParser\Context,
	vBuilder\ArrayParser\KeyParser,
	Tester\Assert;

require __DIR__ . '/../bootstrap.php';

// Positive test
test(function() {

	$context = new Context($structure = array(
		'name' => 'Jane',
		'surname' => 'Doe'
	));

	$rules = new KeyParser(array('name'));

	$rules->addRule(function (Context $context) use ($structure) {
		 return $context->value == $structure['name'];
	});

	$success = $rules->parse($context);

	Assert::true($success);
	Assert::type('array', $context->errors);
	Assert::same(count($context->errors), 0);

});

// Negative test
test(function() {

	$context = new Context($structure = array(
		'name' => 'Jane',
		'surname' => 'Doe'
	));

	$rules = new KeyParser(array('name'));

	$rules->addRule(function (Context $context) use ($structure) {
		 return $context->value != $structure['name'];
	});

	$success = $rules->parse($context);

	Assert::false($success);
	Assert::type('array', $context->errors);
	Assert::same(count($context->errors), 1);

});

// Test nested keys
test(function() {

	$context = new Context($structure = array(
		'address' => array(
			'street' => 'Main Street'
		)
	));

	$rules = new KeyParser(array('address', 'street'));

	$rules->addRule(function (Context $context) use ($structure) {
		 return $context->value == $structure['address']['street'];
	});

	$success = $rules->parse($context);

	Assert::true($success);
	Assert::type('array', $context->errors);
	Assert::same(count($context->errors), 0);

});

// Test condition
test(function() {

	$context = new Context($structure = array(
		'type' => 1,
		'data' => 'A'
	));

	$rules = new KeyParser(array('data'));
	$rules
		->addConditionOn(array('type'), function (Context $context) { return $context->value == 1; })
			->addRule(function (Context $context) { return $context->value == 'A'; })
		->endCondition()
			->addRule(function (Context $context) { return is_string($context->value); });

	Assert::true($rules->parse($context));

	$context->data['data'] = 'B';
	Assert::false($rules->parse($context));

	$context->data['type'] = '2';
	Assert::true($rules->parse($context));

	$rules = new KeyParser(array('data'));
	$rules
		->addConditionOn(array('type'), function (Context $context) { return $context->value == 1; })
			->addRule(function (Context $context) { return $context->value == 'A'; })
		->elseCondition()
			->addRule(function (Context $context) { return $context->value == 'B'; });

	Assert::true($rules->parse($context));

	$context->data['type'] = '1';
	Assert::false($rules->parse($context));

	$context->data['data'] = 'A';
	Assert::true($rules->parse($context));

});

