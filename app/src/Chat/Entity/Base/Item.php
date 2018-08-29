<?php

namespace Chat\Entity\Base;

use JMS\Serializer\Annotation;

abstract class Item
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

    /**
     * @return string
     */
    abstract public static function getEntityName() : string;
}