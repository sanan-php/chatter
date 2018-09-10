<?php
require __DIR__ . '/config/params.php';

$autoloader = require __DIR__ . '/vendor/autoload.php';
\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($autoloader,'loadClass'));