<?php
require __DIR__.'/../../app/bootstrap.php';
set_time_limit(0);
$controller = new \Chat\Controllers\SocketController();
$controller->getWorker();