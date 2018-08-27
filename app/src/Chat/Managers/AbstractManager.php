<?php

namespace Chat\Managers;

use Chat\Core\Db;

abstract class AbstractManager
{
	/** @var Db */
	protected $db;

	public function __construct()
	{
		$this->db = Db::init();
	}

	/**
	 * @param string $entity
	 * @param int $forUserId
	 * @return string
	 * @throws \Exception
	 */
	final public function generateItemId(string $entity, int $forUserId)
	{
		return md5(time(). $entity . $forUserId . random_int(1111,9999));
	}
}