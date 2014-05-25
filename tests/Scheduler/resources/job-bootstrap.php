<?php

$container = new Nette\DI\Container;

class MyService {

	function method1($a, $b, $c) {
		file_put_contents(
			TEMP_DIR . '/method1.called.txt',
			serialize(func_get_args())
		);
	}

	static function method2($a, $b, $c) {
		file_put_contents(
			TEMP_DIR . '/method2.called.txt',
			serialize(func_get_args())
		);
	}

	static function method3($a, $b, $c) {
		throw new Exception('FAIL!');
	}

}

$myService = new MyService;
$container->addService('myService', $myService);

return $container;