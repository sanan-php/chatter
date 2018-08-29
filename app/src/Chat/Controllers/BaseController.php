<?php

namespace Chat\Controllers;

use Chat\Content\L10n;
use Chat\Core\Reference;
use Chat\Core\Request;
use Chat\Core\Response;
use Chat\Core\ServiceBinder;
use Chat\Entity\User;
use Chat\Helpers\Logger;
use Chat\Helpers\Url;
use Chat\Managers\UserManager;

abstract class BaseController
{
	/** @var Request */
	protected $request;
	/** @var Response */
	protected $response;
	/** @var array */
	protected $l10n;
	/** @var UserManager */
	protected $userManager;

	public function __construct()
	{
		$this->request = ServiceBinder::bind(Request::class);
		$this->response = ServiceBinder::bind(Response::class);
		/** @var L10n $l10n */
		$l10n = ServiceBinder::bind(L10n::class);
		$this->l10n = $l10n->getContent();
		$this->userManager = ServiceBinder::bind(UserManager::class);
		if($this->request->get('from')) {
		    $this->response->redirect($this->request->get('from'));
        }
	}

	protected function isPostQuery()
	{
		return (\count($this->request->getAll('post')) !== 0);
	}

	protected function tryAuth($redirect = true)
	{
		$id = $this->request->cookie(Reference::UID_COOKIE);
		$hash = $this->request->cookie(Reference::HASH_COOKIE);
		if(!$id && !$hash) {
			if($redirect) {
			    Logger::write('Куки не установлены');
				$this->response->redirect(Url::createLinkToAction('user','login'));
			}
			return false;
		}
		if(!$this->userManager->checkAuth($id, $hash)) {
			$this->userManager->clearAuth();
			if($redirect) {

				$this->response->redirect(Url::createLinkToAction('user','login'));
			}
			return false;
		}

		return true;
	}

	/**
	 * @return User|bool
	 */
	protected function getCurrentUser()
	{
		if(!$this->tryAuth(false)) {
			return false;
		}
		$id = $this->request->cookie(Reference::UID_COOKIE);

		return $this->userManager->getById($id);
	}

	protected function sendToSocket(string $entity, string $message)
    {
        $instance = stream_socket_client(APP_TCP_SOCKET);
        fwrite($instance, "{'$entity':$message}\n");

    }
}