<?php

use vBuilder\Utils\Strings,
	Tester\Assert;

require __DIR__ . '/../bootstrap.php';

test(function() {

	// Complex parameters
	Assert::same(
		array('something', array('123', 'abcd', 'a b aa , d', 'something' => true)),
		Strings::parseParametrizedString('something:123,abcd,a b aa \\, d,something=true')
	);

});