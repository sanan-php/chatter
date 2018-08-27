<?php

namespace Chat\Controllers;

use Chat\Helpers\Url;

class ChatController extends BaseController
{
	/**
	 * @throws \Twig_Error_Loader
	 * @throws \Twig_Error_Runtime
	 * @throws \Twig_Error_Syntax
	 */
	public function getPrivate()
	{
		$this->tryAuth();
		$with = $this->request->get('with');
		if (!$with) {
			$this->response->redirect(Url::createLinkToAction('user', 'all'));
		}
		if($with === $this->getCurrentUser()->getId()){
			$this->response->redirect(Url::createLinkToAction('user', 'all'));
		}
		$params = [
			'labels' => $this->l10n['chatPage'],
			'links' => [
				'allUsers' => Url::createLinkToAction('user','all'),
				'profile' => Url::createLinkToAction('user','profile'),
				'logout' => Url::createLinkToAction('user','logout'),
			],
			'with' => $with
		];
		$this->response->render('chat', $params);
	}
}