<?php

namespace Chat\Content;

use Chat\Core\Request;
use Chat\Core\ServiceBinder;

class L10n
{
	/** @var Request */
	private $req;

	public function __construct()
	{
		$this->req = ServiceBinder::bind(Request::class);
	}

	final public function getContent() : array
	{
		$service = new self();
		$langFile =  __DIR__ . '/Lang/'.$service->checkLang().'.php';
		if(!file_exists($langFile)) {
			return require __DIR__ . '/Lang/Ru.php';
		}
		return require $langFile;
	}

	private function checkLang()
	{
		if(!$this->req->cookie('lang')) {
			return 'ru';
		}

		return $this->req->cookie('lang');
	}
}