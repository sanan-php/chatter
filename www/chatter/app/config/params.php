<?php
define('ROOT', __DIR__ . DIRECTORY_SEPARATOR);
define('SRC', ROOT . '../src' . DIRECTORY_SEPARATOR);
define('SALT', '1fDr3W$q^TGiCDb');
define('HOST', 'chatter.local');
define('WS_ADDR','0.0.0.0:8081');
define('AUTH_KEY','AWlDr$3sPdeC');
define('LANG','ru');
define('API_PUBLIC_KEY','AdpErBkl3dsFqiPoC');
define('APP_TCP_SOCKET','tcp://chatter.local:1234');
define('REDIS_TCP_SOCKET','redis:6379');

ini_set('max_execution_time',15);

error_reporting(E_ERROR);
