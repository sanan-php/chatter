<?php

namespace Chat\Entity;
use Chat\Entity\Base\Item;
use JMS\Serializer\Annotation;

class Message extends Item
{
	/**
	 * @Annotation\Type("int")
	 */
	private $from;
	/**
	 * @Annotation\Type("int")
	 */
	private $to;
	/**
	 * @Annotation\Type("string")
	 */
	private $message;
	/**
	 * @Annotation\Type("string")
	 */
	private $createdAt;
	/**
	 * @Annotation\Type("string")
	 */
	private static $entity = 'Message';

	/**
	 * @return string
	 */
	public static function getEntityName(): string
	{
		return self::$entity;
	}

	/**
	 * Message constructor.
	 * @param int $from
	 * @param int $to
	 * @param string $message
	 * @throws \Exception
	 */
	public function __construct(
		int $from,
		int $to,
		string $message
	)
	{
		$this->from = $from;
		$this->to = $to;
		$this->message = $message;
		$this->createdAt = (new \DateTimeImmutable())->format(DATE_ATOM);
	}

	/**
	 * @return int
	 */
	public function getFrom()
	{
		return $this->from;
	}

	/**
	 * @return int
	 */
	public function getTo()
	{
		return $this->to;
	}

	/**
	 * @return string
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * @return string
	 */
	public function getCreatedAt()
	{
		return $this->createdAt;
	}
}