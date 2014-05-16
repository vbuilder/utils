<?php

use vBuilder\ArrayParser\Rule,
	vBuilder\ArrayParser\Context,
	Tester\Assert;

require __DIR__ . '/../bootstrap.php';

// Get / Set value
test(function() {

	$context = new Context;
	$context->data = array('a' => 1, 'b' => 2);

	$context->rule = new Rule;
	$context->rule->key = array('a');

	Assert::same($context->data['a'], $context->value);

	$context->value = 3;
	Assert::same(3, $context->data['a']);

});

// Get reference
test(function() {

	$context = new Context(array(
		'a' => array(
			0 => 'changeme'
		)
	));

	$context->rule = new Rule;
	$context->rule->key = array('a');

	Assert::same($context->data['a'], $context->value);

	$context->value[0] = 'changed';
	Assert::same(array(0 => 'changed'), $context->value);

});