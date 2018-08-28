<?php

namespace Chat\Controllers;

use Chat\Core\Headers;
use Chat\Core\Reference;
use Chat\Core\ServiceBinder;
use Chat\Entity\Message;
use Chat\Managers\MessageManager;

class MessageController extends BaseController
{
	/** @var MessageManager */
	private $messageManager;

	public function __construct()
	{
		parent::__construct();
		$this->messageManager = ServiceBinder::bind(MessageManager::class);
	}

	/**
     * Написать сообщение
	 * @throws \Exception
	 */
	public function getCreate()
	{
        $to = $this->request->post('to');
		if(!$this->tryAuth(false)) {
			Headers::set()->forbidden();
			$this->response->forbidden();
		}
		if(!$this->isPostQuery()) {
			Headers::set()->conflict();
			$this->response->jsonFromArray([
				'errorMess' => $this->l10n['messages']['conflict']
			]);
		}
		$message = base64_decode($this->request->post('message'));
		$result = $this->messageManager->create($this->getCurrentUser()->getId(), $to, $message);
		if(!$result) {
            Headers::set()->conflict();
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
        $to = $this->request->post('to');
		$endPosition = (int) $this->request->get('endPosition');
		$startPosition = (int) $this->request->get('startPosition');
		/** @var Message[] $result */
		$result = $this->messageManager->getMessages($this->getCurrentUser()->getId(), $to, $endPosition, $startPosition);
        $result2 = $this->messageManager->getMessages($to, $this->getCurrentUser()->getId(), $endPosition, $startPosition);
		if(!$result && !$result2) {
            $this->response->jsonFromArray([
                'errorMess' => $this->l10n['messages']['messagesNotFound'] . ';' . $to
            ]);
        }
		$messages = $this->convertToArray($result);
		$temp = $this->convertToArray($result2);
		if(\count($temp) && \count($messages)) {
		    $messages = array_merge($messages,$temp);
        } elseif(\count($temp)) {
		    $messages = $temp;
        }
		$messages = $this->sortMessages($messages);
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
                'from' => (string) $item->getFrom(),
                'to' => (string) $item->getTo(),
                'text' => $item->getMessage(),
                'createdAt' => date('d.m.Y H:i:s', strtotime($item->getCreatedAt())),
                'timestamp' => strtotime($item->getCreatedAt())
            ];
        }

        return $messages;
    }

    private function sortMessages($messages)
    {
        $createdAt = array_column($messages,'timestamp');
        $byCreated = [];
        foreach ($createdAt as $item) {
            $byCreated[] = $item;
        }
        array_multisort($byCreated,SORT_NUMERIC,SORT_ASC, $messages);

        return $messages;
    }
}