<?php

namespace Chat\Managers;

use Chat\Core\ServiceBinder;
use Chat\Entity\Message;

class MessageManager extends AbstractManager
{
	/** @var UserManager */
	private $userManager;
	/** @var \JMS\Serializer\Serializer */
	private $serializer;

	public function __construct()
	{
		parent::__construct();
		$this->userManager = ServiceBinder::bind(UserManager::class);
        $this->serializer = \JMS\Serializer\SerializerBuilder::create()->build();
	}

	/**
	 * @param int $from
	 * @param int $to
	 * @param string $message
	 * @return string|bool
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

		return $this->serializer->serialize($this->db->writeData('Message', $message),'json');
	}

	/**
	 * @param int $from
	 * @param int $to
	 * @param int $limit
	 * @param int $offset
	 * @return Message[]|bool
	 */
	public function getMessages(int $from, int $to, int $limit = 30, int $offset = 0)
	{
		return $this->db->getGroup(Message::getEntityName(),md5($from.$to), $limit, $offset);
	}
}