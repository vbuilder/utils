<?php

use vBuilder\Utils\Strings,
	Tester\Assert;

require __DIR__ . '/../bootstrap.php';

test(function() {

	Assert::same(
		'Hello Jan, my number is 88.',
		Strings::sprintf('Hello %s, my number is %d.', array('Jan', 88))
	);

	Assert::same(
		'Hello Jan, my number is 88.',
		Strings::sprintf('Hello %name, my number is %number.', array(
			'number' => 88,
			'name' => 'Jan'
		))
	);

	Assert::same(
		'5.261 + 4.739 = 10.00',
		Strings::sprintf('%A$.3f + %B$.3f = %C$.2f', array(
			'C' => 10,
			'A' => 5.2611,
			'B' => 4.739
		))
	);

	Assert::same(
		'AB',
		Strings::sprintf('%foo$s%bar$s', array(
			'foo' => 'A',
			'bar' => 'B'
		))
	);

	Assert::same(
		'AB',
		Strings::sprintf('%{foo}%{bar}', array(
			'foo' => 'A',
			'bar' => 'B'
		))
	);

});