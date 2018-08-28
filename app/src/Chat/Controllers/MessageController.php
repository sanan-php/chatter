<?php

namespace Chat\Controllers;

use Chat\Core\Headers;
use Chat\Core\Reference;
use Chat\Core\ServiceBinder;
use Chat\Entity\Message;
use Chat\Managers\MessageManager;

class MessageController extends BaseController
{
	private $currentUserId;
	/** @var MessageManager */
	private $messageManager;

	public function __construct()
	{
		parent::__construct();
		$this->currentUserId = (int) $this->request->cookie(Reference::UID_COOKIE);
		$this->messageManager = ServiceBinder::bind(MessageManager::class);
	}

	/**
     * Написать сообщение
	 * @throws \Exception
	 */
	public function getCreate()
	{
        $to = (int) $this->request->post('to');
		if(!$this->tryAuth(false)) {
			Headers::set()->forbidden();
			$this->response->forbidden();
		}
		if(!$this->isPostQuery()) {
			Headers::set()->forbidden();
			$this->response->jsonFromArray([
				'errorMess' => $this->l10n['messages']['forbidden']
			]);
		}
		$message = base64_decode($this->request->post('message'));
		$result = $this->messageManager->create($this->currentUserId, $to, $message);
		if(!$result) {
			$this->response->jsonFromArray([
				'errorMess' => $this->l10n['messages']['notCreated']
			]);
		}
		$this->response->jsonFromArray([
		    'success' => true,
            'content' => json_decode($result, true)
        ]);
	}

    /**
     * Список пользователей, с которыми есть переписка
     */
	public function getList()
    {

    }

    /**
     * Переписка с пользователем
     */
	public function getAll()
	{
		$this->tryAuth();
        $to = (int) $this->request->post('to');
		$limit = (int) $this->request->get('limit');
		$offset = (int) $this->request->get('offset');
		/** @var Message[] $result */
		$result = $this->messageManager->getMessages($this->currentUserId, $to, $limit, $offset);
        $result2 = $this->messageManager->getMessages($to, $this->currentUserId, $limit, $offset);
		if(!$result && !$result2) {
            $this->response->jsonFromArray([
                'errorMess' => $this->l10n['messages']['messagesNotFound'] . ';' . $to
            ]);
        }
		$messages = $this->convertToArray($result);
		$temp = $this->convertToArray($result2);
		if(\count($temp) && \count($messages)) {
		    $messages[] = $temp;
        } elseif(\count($temp)) {
		    $messages = $temp;
        }
		$byCreated = [];
		foreach ($messages as $key => $row) {
		    if($row === '') {
		        continue;
            }
			$byCreated[$key] = $row['createdAt'];
		}
		$this->response->jsonFromArray([
			'content' => $messages
		]);
	}

	private function convertToArray($result)
    {
        $messages = [];
        foreach ($result as $item) {
            if(!($item instanceof Message)) {
                continue;
            }
            $messages[] = [
                'id' => $item->getId(),
                'from' => $item->getFrom(),
                'to' => $item->getTo(),
                'text' => $item->getMessage(),
                'createdAt' => $item->getCreatedAt(),
            ];
        }

        return $messages;
    }
}