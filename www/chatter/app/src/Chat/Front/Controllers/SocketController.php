<?php

namespace Chat\Front\Controllers;

use Chat\Front\Http\Headers;
use Chat\Core\Entity\User;
use Workerman\Connection\AsyncUdpConnection;
use Workerman\Worker;

class SocketController extends BaseController
{
	
	public function getWorker()
    {
    	Headers::webSocket($this->request->server('http_sec_websocket_key'));
		$webSocket = new Worker('websocket://'.WS_ADDR.'/');
		$webSocket->count = 1;
		$webSocket->user = 'www-data';
		$webSocket->name = 'chatter';
		/** @var AsyncUdpConnection[] $users */
		$users = [];
		$webSocket->onConnect = function ($connection) use (&$users) {
			$connection->onWebSocketConnect = function ($connection) use (&$users) {
				$currentConnectedUser = $_COOKIE['uid'];
				if ($this->userManager->getById($currentConnectedUser) instanceof User) {
					$users[$currentConnectedUser] = $connection;
				}
			};
		};
		$webSocket->onWorkerStart = function () use (&$users) {
			$innerTcpWorker = new Worker(APP_TCP_SOCKET);
			$innerTcpWorker->onMessage = function ($connection, $data) use (&$users) {
				$data = json_decode($data);
				if (isset($users[$data->user])) {
					$webConnection = $users[$data->user];
					$webConnection->send($data->message);
				}
			};
			$innerTcpWorker->listen();
		};
		$webSocket->onClose = function ($connection) use (&$users) {
			$user = array_search($connection, $users);
			unset($users[$user]);
		};
		Worker::runAll();
    }
}
