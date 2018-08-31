<?php

namespace Chat\Entity;

use Chat\Entity\Base\Item;
use JMS\Serializer\Annotation;

/**
 * Class User
 * @package Chat\Entity
 */
class User extends Item
{
	/**
	 * @Annotation\Type("string")
	 */
	private $name;
	/**
	 * @Annotation\Type("string")
	 */
	private $email;
	/**
	 * @Annotation\Type("string")
	 */
	private $pass;
	/**
	 * @Annotation\Type("string")
	 */
	private $pic;
	
	/**
	 * @Annotation\Type("int")
	 */
	private $sex;
	
	/**
	 * @Annotation\Type("int")
	 */
	private $birthDate;
	/**
	 * @Annotation\Type("string")
	 */
	private $location;

	/**
	 * @Annotation\Type("string")
	 */
	private static $entity = 'User';

	public static function getEntityName(): string
	{
		return self::$entity;
	}

	/**
	 * @return string
	 */
	public function getName() :string
	{
		return $this->name;
	}
	
	/**
	 * @param string $name
	 */
	public function setName(string $name) : void
	{
		$this->name = $name;
	}
	
	/**
	 * @return string
	 */
	public function getEmail() : string
	{
		return $this->email;
	}
	
	/**
	 * @param string $email
	 */
	public function setEmail(string $email) : void
	{
		$this->email = $email;
	}
	
	/**
	 * @return string
	 */
	public function getPass() : string
	{
		return $this->pass;
	}
	
	/**
	 * @param string $pass
	 */
	public function setPass(string $pass) : void
	{
		$this->pass = $pass;
	}
	
	/**
	 * @return mixed
	 */
	public function getPic() : ?string
	{
		return $this->pic;
	}
	
	/**
	 * @param mixed $pic
	 */
	public function setPic($pic): void
	{
		$this->pic = $pic;
	}
	
	/**
	 * @return mixed
	 */
	public function getSex()
	{
		return $this->sex;
	}
	
	/**
	 * @param mixed $sex
	 */
	public function setSex(int $sex): void
	{
		$this->sex = $sex;
	}
	
	
	/**
	 * @param mixed $id
	 */
	public function setId($id): void
	{
		$this->id = $id;
	}
	
	/**
	 * @return mixed
	 */
	public function getBirthDate()
	{
		return $this->birthDate;
	}
	
	/**
	 * @param mixed $birthDate
	 */
	public function setBirthDate($birthDate): void
	{
		$this->birthDate = $birthDate;
	}
	
	/**
	 * @return mixed
	 */
	public function getLocation()
	{
		return $this->location;
	}
	
	/**
	 * @param mixed $location
	 */
	public function setLocation($location): void
	{
		$this->location = $location;
	}
}