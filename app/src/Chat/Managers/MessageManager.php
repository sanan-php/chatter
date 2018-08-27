<?php

namespace Chat\Managers;

use Chat\Core\ServiceBinder;
use Chat\Entity\Message;

class MessageManager extends AbstractManager
{
	/** @var UserManager */
	private $userManager;

	public function __construct()
	{
		parent::__construct();
		$this->userManager = ServiceBinder::bind(UserManager::class);
	}

	/**
	 * @param int $from
	 * @param int $to
	 * @param string $message
	 * @return object|bool
	 * @throws \Exception
	 */
	public function create(int $from, int $to, string $message)
	{
		if(!$this->userManager->getById($from)) {
			return false;
		}
		if(!$this->userManager->getById($to)) {
			return false;
		}
		$text = htmlspecialchars($message);
		$message = new Message($from, $to, $text);
		$message->setId($this->generateItemId($message::getEntityName(),$from));
		$message->setGroupId(md5($from.$to));

		return $this->db->writeData('Message', $message);
	}

	/**
	 * @param int $from
	 * @param int $to
	 * @param int $limit
	 * @param int $offset
	 * @return Message[]
	 */
	public function getMessages(int $from, int $to, int $limit = 30, int $offset = 0)
	{
		return $this->db->getGroup(Message::getEntityName(),md5($from.$to), $limit, $offset);
	}
}