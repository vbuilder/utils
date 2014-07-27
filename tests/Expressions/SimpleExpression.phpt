<?php

use vBuilder\Expressions\SimpleExpression,
	Tester\Assert;

require __DIR__ . '/../bootstrap.php';

test(function() {

	// Flattening
	$e = new SimpleExpression('AND', 1, 1, array(2, array(3)), 4);
	Assert::same(array(1, 2, 3, 4), $e->getOperands());
	Assert::same('AND', $e->getOperator());

	// Nesting
	$e = new SimpleExpression(
		'AND',
		100,
		new SimpleExpression('AND',
			200,
			new SimpleExpression('AND',
				300
			)
		)
	);
	Assert::same(array(100, 200, 300), $e->getOperands());

});