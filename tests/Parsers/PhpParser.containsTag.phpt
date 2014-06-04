<?php

use vBuilder\Parsers\PhpParser,
	Tester\Assert;

require __DIR__ . '/../bootstrap.php';

test(function() {

	// No tag test
	Assert::false(PhpParser::containsTag('hello world'));

	// Basic PHP tag
	Assert::true(PhpParser::containsTag('<?php'));

	// Short tag
	Assert::true(PhpParser::containsTag('<?'));

	// Short tag 2
	Assert::true(PhpParser::containsTag('<?='));

	// ASP tag
	Assert::true(PhpParser::containsTag('<%'));

	// ASP tag 2
	Assert::true(PhpParser::containsTag('<%='));

	// Test for <script> tag in malformated HTML
	Assert::true(
		PhpParser::containsTag('a < b <script language="php"> eval();')
	);

	// Test case-insensitivity for <script> matching
	Assert::true(
		PhpParser::containsTag('<Script lanGUage="PhP"> eval();')
	);

	// Test no quotes on <script> tag
	Assert::true(
		PhpParser::containsTag('<script language=php> eval();')
	);

	// Test single quotes on <script> tag
	Assert::true(
		PhpParser::containsTag('<script language=\'php\'> eval();')
	);

	// Test malformed attributes in <script> tag
	Assert::true(
		PhpParser::containsTag('<script class="a11" language="php" disabled> eval();')
	);

	// PHP tag inside of HTML tag
	Assert::true(PhpParser::containsTag('<div<?= $a > 3 ? \' class="abcd"\'?>>hello</div>'));

});