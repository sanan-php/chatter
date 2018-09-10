<?php

namespace Chat\Core\Managers;

use Chat\Core\Db;

class BaseManager
{
	/** @var Db */
	protected $db;
	/** @var \JMS\Serializer\Serializer */
	protected $serializer;
	
	public function __construct()
	{
		$this->db = Db::init();
		$this->serializer = \JMS\Serializer\SerializerBuilder::create()->build();
	}

	/**
	 * @param string $entity
	 * @param string $forUserId
	 * @return string
	 * @throws \Exception
	 */
	final public function generateItemId(string $entity, string $forUserId)
	{
		return md5(time(). $entity . $forUserId . random_int(1111,9999));
	}
}