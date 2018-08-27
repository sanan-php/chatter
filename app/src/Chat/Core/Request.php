<?php

namespace Chat\Core;

use JMS\Serializer\Annotation as Serializer;

class Request
{
	/**
	 * @Serializer\Type("array")
	 */
	private $get;
	/**
	 * @Serializer\Type("array")
	 */
	private $post;
	/**
	 * @Serializer\Type("array")
	 */
	private $request;
	/**
	 * @Serializer\Type("array")
	 */
	private $server;
	/**
	 * @Serializer\Type("array")
	 */
	private $cookie;
	/**
	 * @Serializer\Type("array")
	 */
	private $session;
	/**
	 * @Serializer\Type("array")
	 */
	private $files;

	public function __construct()
	{
		$this->get = $_GET;
		$this->post = $_POST;
		$this->request = $_REQUEST;
		$this->server = $_SERVER;
		$this->cookie = $_COOKIE;
		$this->files = $_FILES;

		if(session_status() !== PHP_SESSION_ACTIVE) {
			session_name('WCHAT');
			session_start();
		}
		$this->session = $_SESSION;
	}

	public function get(string $name)
	{
		return $this->get[$name] ?? false;
	}

	/**
	 * @param string $name
	 * @return array|bool
	 */
	public function file(string  $name)
	{
		return $this->files[$name] ?? false;
	}

	public function post(string $name)
	{
		return $this->post[$name] ?? false;
	}

	public function request(string $name)
	{
		return $this->request[$name] ?? false;
	}

	public function server(string $name)
	{
		$name = strtoupper($name);

		return $this->server[$name] ?? false;
	}

	public function session(string $name)
	{
		$name = strtoupper($name);

		return $this->session[$name] ?? false;
	}

	public function setSession($name, $value)
	{
		$this->session[$name] = $value;
	}

	public function getSession($name)
	{
		return $this->session[$name] ?? false;
	}

	public function cookie(string $name)
	{
		return $this->cookie[$name] ?? false;
	}

	public function userIp()
	{
		if (!empty($this->server['HTTP_CLIENT_IP'])) {
			$ip = $this->server['HTTP_CLIENT_IP'];
		} elseif (!empty($this->server['HTTP_X_FORWARDED_FOR'])) {
			$ip = $this->server['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $this->server['REMOTE_ADDR'];
		}

		return $ip;
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function getAll(string $name) {
		if(!method_exists($this, $name)) {
			die("Error of request: Param type \"{$name}\" is undefined.");
		}

		return $this->$name;
	}
}