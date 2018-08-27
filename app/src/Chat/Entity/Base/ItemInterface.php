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
	public function getGroupId() : ?string;

	/**
	 * @param string $id
	 */
	public function setGroupId(string $id) : void;

	/**
	 * @return string
	 */
	public static function getEntityName() : string;
}