<?php

namespace Chat\Entity\Base;

use JMS\Serializer\Annotation;

abstract class Item implements ItemInterface
{
	/**
	 * @Annotation\Type("string")
	 */
	protected $id;
	/**
	 * @Annotation\Type("string")
	 */
	protected $groupId;

	/**
	 * @return string
	 */
	public function getId() : string
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getGroupId() : ?string
	{
		return $this->groupId;
	}

	public function setId(string $id) : void
	{
		$this->id = $id;
	}

	public function setGroupId(string $id) : void
	{
		$this->groupId = $id;
	}
}