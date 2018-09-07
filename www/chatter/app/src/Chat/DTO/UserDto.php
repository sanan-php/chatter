<?php

namespace Chat\DTO;


class UserDto
{
	private $name;
	private $email;
	private $pass;
	private $pic;
	private $sex;
	private $birthDate;
	private $location;

	public function __construct(
		string $name,
		string $email,
		string $pass,
		string $location = '',
		array $pic = [],
		int $sex = 0,
		int $birthDate = 0
	)
	{
		$this->name = $name;
		$this->email = $email;
		$this->pass = $pass;
		$this->location = $location;
		$this->pic = $pic;
		$this->sex = $sex;
		$this->birthDate = $birthDate;
	}

	/**
	* @return string
	*/
	public function getName()
	{
		return $this->name;
	}
	
	/**
	* @return string
	*/
	public function getEmail()
	{
		return $this->email;
	}
	
	/**
	* @return string
	*/
	public function getPass()
	{
	return $this->pass;
	}
	
	/**
	* @return array
	*/
	public function getPic(): array
	{
		return $this->pic;
	}
	
	/**
	* @return int
	*/
	public function getSex(): int
	{
		return $this->sex;
	}
	
	/**
	* @return int
	*/
	public function getBirthDate(): int
	{
		return $this->birthDate;
	}
	
	/**
	* @return string
	*/
	public function getLocation(): string
	{
		return $this->location;
	}

}