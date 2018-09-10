<?php

namespace Chat\Front\Controllers;

use Chat\Common\Content\L10n;
use Chat\Common\Enums\Reference;
use Chat\Front\Http\Request;
use Chat\Front\Http\Response;
use Chat\Core\ServiceBinder;
use Chat\Entity\User;
use Chat\Common\Helpers\Url;
use Chat\Core\Managers\UserManager;

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
		if ($this->request->get('from')) {
		    $this->response->redirect($this->request->get('from'));
        }
	}

	protected function isPostQuery()
	{
		return (\count($this->request->getRequest()['post']) !== 0);
	}

	protected function tryAuth($redirect = true)
	{
		$id = $this->request->cookie(Reference::UID_COOKIE);
		$hash = $this->request->cookie(Reference::HASH_COOKIE);
		if (!$id && !$hash) {
			if ($redirect) {
				$this->response->redirect(Url::createLinkToAction('user','login'));
			}
			return false;
		}
		if (!$this->userManager->checkAuth($id, $hash)) {
			$this->userManager->clearAuth();
			if ($redirect) {

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
		if (!$this->tryAuth(false)) {
			return false;
		}
		$id = $this->request->cookie(Reference::UID_COOKIE);

		return $this->userManager->getById($id);
	}

	protected function sendToSocket(string $forUser, string $message)
    {
        $instance = stream_socket_client(APP_TCP_SOCKET);
        fwrite($instance, json_encode([
        	'user' => $forUser,
        	'message' => $message
		])."\n");
    }

    protected function mainLinks()
    {
        return [
            'allUsers' => Url::createLinkToAction('user','all'),
            'profile' => Url::createLinkToAction('user','profile'),
            'logout' => Url::createLinkToAction('user','logout'),
            'favorites' => Url::createLinkToAction('favorite','all'),
            'chat' => Url::createLinkToAction('chat','private'),
            'searchAction' => Url::createLinkToAction('user','search')
        ];
    }
}
