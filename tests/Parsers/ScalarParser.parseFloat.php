<?php

use vBuilder\Parsers\ScalarParser,
	Tester\Assert;

require __DIR__ . '/../bootstrap.php';

test(function() {

	// Negative sign
	Assert::same(
		-13.0,
		ScalarParser::parseFloat('-13')
	);

	// Positive sign
	Assert::same(
		13.22,
		ScalarParser::parseFloat('+13.22')
	);

	// Comma as decimal separator
	Assert::same(
		1.22,
		ScalarParser::parseFloat('1,22')
	);

	// Zero
	Assert::same(
		0.0,
		ScalarParser::parseFloat('0')
	);

	// Empty string
	Assert::same(
		NULL,
		ScalarParser::parseFloat('')
	);

	// Thousand separator + decimal point
	Assert::same(
		1231.123,
		ScalarParser::parseFloat('1,231.123')
	);

	// Multiple thousand separators
	Assert::same(
		123122123.0,
		ScalarParser::parseFloat('123,122,123')
	);

	// Thousand separator + decimal comma
	Assert::same(
		123122.123,
		ScalarParser::parseFloat('123 122,123')
	);

	// Garbage
	Assert::same(
		11.3,
		ScalarParser::parseFloat('some garbage 11.3 some other garbage')
	);

	// Missing leading zero
	Assert::same(
		0.12,
		ScalarParser::parseFloat('.12')
	);

	// Missing leading zero 2
	Assert::same(
		0.12,
		ScalarParser::parseFloat(',12')
	);

	// Dot as thousand separator
	Assert::same(
		123123123.0,
		ScalarParser::parseFloat('123.123.123')
	);

	// Dot as thousand separator + comma as decimal
	Assert::same(
		123123.12,
		ScalarParser::parseFloat('123.123,12')
	);

	// Practical test #1
	Assert::same(
		1124.5,
		ScalarParser::parseFloat('Cena: 1 124,50 Kč')
	);

});