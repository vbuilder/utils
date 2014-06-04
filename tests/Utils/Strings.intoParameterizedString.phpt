<?php

use vBuilder\Utils\Strings,
	Tester\Assert;

require __DIR__ . '/../bootstrap.php';

test(function() {

	// Empty
	Assert::same(
		'',
		Strings::intoParameterizedString('')
	);

	// No parameters
	Assert::same(
		'abcd',
		Strings::intoParameterizedString('abcd')
	);

	// Complex parameters
	Assert::same(
		'something:123,abcd,a b aa \\, d,something=true',
		Strings::intoParameterizedString(
			'something',
			array(123, 'abcd', 'a b aa , d', 'something' => true)
		)
	);

});