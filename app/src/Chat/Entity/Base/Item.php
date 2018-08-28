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
	 * @return string
	 */
	public function getId() : string
	{
		return $this->id;
	}

	public function setId(string $id) : void
	{
		$this->id = $id;
	}
}