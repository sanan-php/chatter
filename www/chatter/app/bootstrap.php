<?php
require __DIR__ . '/config/params.php';

$autoloader = require ROOT . '../../vendor/autoload.php';
\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($autoloader,'loadClass'));