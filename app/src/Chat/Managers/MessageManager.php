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
	 * @param string $from
	 * @param string $to
	 * @param string $message
	 * @return string|bool
	 * @throws \Exception
	 */
	public function create(string $from, string $to, string $message)
	{
		if(!$this->userManager->getById($from)) {
			return false;
		}
		if(!$this->userManager->getById($to)) {
			return false;
		}
		$text = htmlspecialchars($message);
		$message = new Message($from, $to, $text);
		$message->setId(md5(time()));
		$result = $this->db->setList('Message', md5($from.$to), $message);
		if(!$result) {
		    return false;
        }

		return $this->serializer->serialize($result,'json');
	}

	/**
	 * @param string $from
	 * @param string $to
	 * @param int $endPosition
	 * @param int $startPosition
	 * @return Message[]|bool
	 */
	public function getMessages(string $from, string $to, int $endPosition = 30, int $startPosition = 0)
	{
		return $this->db->getList(Message::getEntityName(), md5($from.$to), $endPosition, $startPosition);
	}
}