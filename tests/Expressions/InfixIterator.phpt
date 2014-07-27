<?php

use vBuilder\Expressions\SimpleExpression,
	vBuilder\Expressions\InfixIterator,
	Tester\Assert;

require __DIR__ . '/../bootstrap.php';

test(function() {

	// Basic
	$e = new SimpleExpression(
		'OR',
		new SimpleExpression('=', 'a', 10),
		new SimpleExpression('=', 'b', 100)
	);

	Assert::same(
		array('(', 'a', '=', 10, ')', 'OR', '(', 'b', '=', 100, ')'),
		iterator_to_array(new InfixIterator($e))
	);

	// More complex
	$e = new SimpleExpression(
		'OR',
		new SimpleExpression(
			'AND',
			new SimpleExpression('=', 'a', 10),
			new SimpleExpression('=', 'b', 100)
		),
		new SimpleExpression('=', 'state', FALSE),
		new SimpleExpression('=', 'author', NULL)
	);

	Assert::same(
		array('(', '(', 'a', '=', 10, ')', 'AND', '(', 'b', '=', 100, ')', ')', 'OR', '(', 'state', '=', FALSE, ')', 'OR', '(', 'author', '=', NULL, ')'),
		iterator_to_array(new InfixIterator($e))
	);

	// Nested
	$e = new SimpleExpression(
		'AND',
		new SimpleExpression('=', 'a', 100),
		new SimpleExpression('AND',
			new SimpleExpression('=', 'b', 200),
			new SimpleExpression('AND',
				new SimpleExpression('=', 'c', 300)
			)
		)
	);

	Assert::same(
		array('(', 'a', '=', 100, ')', 'AND', '(', 'b', '=', 200, ')', 'AND', '(', 'c', '=', 300, ')'),
		iterator_to_array(new InfixIterator($e))
	);

});