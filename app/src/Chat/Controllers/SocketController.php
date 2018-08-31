<?php

namespace Chat\Controllers;

use Chat\Helpers\Logger;
use Workerman\Connection\AsyncUdpConnection;
use Workerman\Worker;

class SocketController extends BaseController
{
    private $webSocket;

    public function __construct()
    {
        parent::__construct();
        $this->webSocket = new Worker('websocket://127.0.0.1:8000');
    }

    public function getWorker()
    {
        /** @var AsyncUdpConnection[] $users */
        $users = [];
        $this->webSocket->onConnect = function($connection) use (&$users)
        {
            $connection->onWebSocketConnect = function($connection) use (&$users)
            {
                $currentConnectedUser = $this->request->get('uid');
                if($this->userManager->getById($currentConnectedUser)) {
                    $users[$currentConnectedUser] = $connection;
                } else {
                    Logger::write("Worker try connect: UID as $currentConnectedUser");
                }
            };
        };
        $this->webSocket->onClose = function($connection) use(&$users)
        {
            $user = array_search($connection, $users);
            unset($users[$user]);
        };
        $this->webSocket->onWorkerStart = function() use (&$users)
        {
            $innerTcpWorker = new Worker(APP_TCP_SOCKET);
            // create a handler that will be called when a local tcp-socket receives a message (for example from send.php)
            $innerTcpWorker->onMessage = function($data) use (&$users) {
                $data = json_decode($data);
                if (isset($users[$data->user])) {
                    $connection = $users[$data->user];
                    $connection->send($data->message);
                }
            };
            $innerTcpWorker->listen();
            Worker::runAll();
        };
    }
}
