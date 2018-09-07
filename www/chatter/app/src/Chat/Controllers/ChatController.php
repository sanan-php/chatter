<?php

namespace Chat\Controllers;

use Chat\Helpers\Url;

class ChatController extends BaseController
{
	/**
	 * @throws \Twig_Error_Loader
	 * @throws \Twig_Error_Runtime
	 * @throws \Twig_Error_Syntax
	 * @throws \Exception
	 */
	public function getPrivate()
	{
		$this->tryAuth();
		$with = $this->request->get('with');
		if (!$with) {
			$this->response->redirect(Url::createLinkToAction('user', 'all'));
		}
		if ( $with === $this->getCurrentUser()->getId()) {
			$this->response->redirect(Url::createLinkToAction('user', 'all'));
		}
		$params = [
		    'currentUser' => $this->getCurrentUser(),
			'labels' => array_merge($this->l10n['main'],$this->l10n['chatPage']),
			'links' => array_merge($this->mainLinks(),[
				'newMessageCreate' => Url::createLinkToAction('message','create'),
			]),
			'currentContact' => $this->userManager->getById($with),
			'rand' => random_int(1111,9999),
			'with' => $with
		];
		$this->response->render('chat:private', $params);
	}
}
