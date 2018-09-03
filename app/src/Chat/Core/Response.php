<?php

namespace Chat\Core;

use Chat\Managers\FavoriteManager;

class Response
{
	/** @var \Twig_Environment */
	protected $twig;

	public function __construct()
	{
		$loader = new \Twig_Loader_Filesystem(Paths::TPL_PATH);
		$loader->setPaths(Paths::TPL_PATH,'app');
		$this->twig = new \Twig_Environment($loader, array(
			'cache' => false, //ROOT.'/cache/twig/',
		));
		$twigFunction = new \Twig_Function('FavoriteManager', function($isFavorite,$user,$favoriteId) {
		    return FavoriteManager::$isFavorite($user,$favoriteId);
		});
		$this->twig->addFunction($twigFunction);
	}

	public function json(array $data)
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
		$template = str_replace(':','/',$template).'.twig';
		exit($this->twig->render($template, $params));
	}

	/**
	* @throws \Twig_Error_Loader
	* @throws \Twig_Error_Runtime
	* @throws \Twig_Error_Syntax
	*/
	public function notFound()
	{
		Headers::set()->notFound();
		$this->render('error:notFound');
	}

	/**
	* @throws \Twig_Error_Loader
	* @throws \Twig_Error_Runtime
	* @throws \Twig_Error_Syntax
	*/
	public function forbidden()
	{
		Headers::set()->forbidden();
		$this->render('error:forbidden');
	}

	public function redirect($url)
	{
		Headers::set()->redirect($url);
	}
}
