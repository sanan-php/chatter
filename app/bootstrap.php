<?php
require __DIR__ . '/config/params.php';

spl_autoload_register(function ($class) {
	$file = SRC . str_replace('\\', '/', $class) . '.php';
	if (file_exists($file)) {
		require $file;
	}
});

$autoloader = require ROOT . '/../vendor/autoload.php';
\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($autoloader,'loadClass'));