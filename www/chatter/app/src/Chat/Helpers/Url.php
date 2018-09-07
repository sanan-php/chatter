<?php

namespace Chat\Helpers;

use Chat\Core\Reference;

class Url
{
	final public static function createLinkToAction(string $controller, string $get)
	{
		$params = [
			Reference::CONTROLLER_QUERY_PARAM => $controller,
			Reference::ACTION_QUERY_PARAM => $get,
		];

		return '/?' . http_build_query($params);
	}
}