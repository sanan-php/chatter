<?php
/**
 * Created by PhpStorm.
 * User: Sanan
 * Date: 25.08.2018
 * Time: 1:34
 */

namespace Chat\Entity;
use Chat\Entity\Base\Item;
use JMS\Serializer\Annotation as Serializer;

class Message extends Item
{
	/**
	 * @Serializer\Type("int")
	 */
	private $from;
	/**
	 * @Serializer\Type("int")
	 */
	private $to;
	/**
	 * @Serializer\Type("string")
	 */
	private $message;
	/**
	 * @Serializer\Type("string")
	 */
	private $createdAt;
	/**
	 * @Serializer\Type("string")
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