<?php

namespace Chat\Core;

class Response
{
	/** @var \Twig_Environment */
	protected $twig;

	public function __construct()
	{
		$loader = new \Twig_Loader_Filesystem(Paths::TPL_PATH);
		$this->twig = new \Twig_Environment($loader, array(
			'cache' => false, // ROOT.'/cache/twig/',

		));
	}

	public function jsonFromArray(array $data)
	{
		Headers::set()->contentType(ContentTypes::JSON);
		exit(json_encode($data, JSON_PRETTY_PRINT));
	}

	/**
	 * @param string $template
	 * @param array $params
	 * @throws \Twig_Error_Loader
	 * @throws \Twig_Error_Runtime
	 * @throws \Twig_Error_Syntax
	 */
	public function render(string $template, array $params = [])
	{
		exit($this->twig->render($template.'.twig', $params));
	}

	public function notFound()
	{
		Headers::set()->notFound();
		$this->render('notFound');
	}

	public function forbidden()
	{
		Headers::set()->forbidden();
		$this->render('forbidden');
	}

	public function redirect($url)
	{
		Headers::set()->redirect($url);
	}
}