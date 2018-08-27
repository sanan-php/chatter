<?php
/**
 * Created by PhpStorm.
 * User: Sanan
 * Date: 25.08.2018
 * Time: 0:38
 */

namespace Chat\Controllers;

use Chat\Core\Headers;
use Chat\Core\Reference;
use Chat\Core\ServiceBinder;
use Chat\Entity\Message;
use Chat\Managers\MessageManager;

class MessageController extends BaseController
{
	private $currentUserId;
	private $chatWith;
	/** @var MessageManager */
	private $messageManager;

	public function __construct()
	{
		parent::__construct();
		$this->currentUserId = (int) $this->request->cookie(Reference::UID_COOKIE);
		$this->chatWith = (int) $this->request->get('with');
		$this->messageManager = ServiceBinder::bind(MessageManager::class);
	}

	/**
	 * @throws \Exception
	 */
	public function getCreate()
	{
		if(!$this->tryAuth(false)) {
			Headers::set()->forbidden();
			$this->response->forbidden();
		}
		if(!$this->isPostQuery()) {
			Headers::set()->forbidden();
			$this->response->jsonFromArray([
				'error' => $this->l10n['messages']['forbidden']
			]);
		}
		$message = base64_decode($this->request->post('message'));
		$result = $this->messageManager->create($this->currentUserId, $this->chatWith, $message);
		if(!$result) {
			$this->response->jsonFromArray([
				'error' => $this->l10n['messages']['notCreated']
			]);
		}
	}

	public function getList()
	{
		$this->tryAuth();
		$limit = (int) $this->request->get('limit');
		$offset = (int) $this->request->get('offset');
		/** @var Message[] $result */
		$result = $this->messageManager->getMessages($this->currentUserId,$this->chatWith, $limit, $offset);
		$result[] = $this->messageManager->getMessages($this->chatWith, $this->currentUserId, $limit, $offset);
		if(!$result) {
			$this->response->jsonFromArray([
				'empty' => $this->l10n['messages']['messagesNotFound']
			]);
		}
		$messages = [];
		foreach ($result as $item) {
			$messages[] = [
				'id' => $item->getId(),
				'from' => $item->getFrom(),
				'to' => $item->getTo(),
				'text' => $item->getMessage(),
				'createdAt' => $item->getCreatedAt(),
			];
		}
		$byCreated = array();
		foreach ($messages as $key => $row)
		{
			$byCreated[$key] = $row['createdAt'];
		}
		array_multisort($byCreated, SORT_DESC, $messages);
		$this->response->jsonFromArray([
			'messages' => $messages
		]);
	}
}