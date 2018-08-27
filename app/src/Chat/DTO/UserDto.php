<?php

namespace Chat\DTO;


class UserDto
{
	private $name;
	private $email;
	private $pass;

	public function __construct(
		string $name,
		string $email,
		string $pass
	)
	{
		$this->name = $name;
		$this->email = $email;
		$this->pass = $pass;
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


}