<?php
namespace Chat\Entity\Base;

interface ItemInterface
{
	/**
	 * @return string
	 */
	public function getId() : string;

	/**
	 * @param string $id
	 * @return mixed
	 */
	public function setId(string $id) : void;

	/**
	 * @return string
	 */
	public static function getEntityName() : string;
}