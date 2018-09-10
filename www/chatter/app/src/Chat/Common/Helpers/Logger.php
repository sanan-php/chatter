<?php

namespace Chat\Common\Helpers;

use Chat\Common\Enums\ContentTypes;
use Chat\Front\Http\Headers;

class Logger
{
	final public static function write($message, $error = false)
	{
		$type = $error ? 'Error' : 'Log';
		$resource = fopen(ROOT.'../log/'.date('y-m-d').'.log','ab+');
		$content = '[' . date('Y-m-d T H:i:s') . "] [{$type}]: {$message} \n";
		fwrite($resource, $content);
		fclose($resource);
		if ($error) {
			Headers::set()->contentType(ContentTypes::JSON);
			Headers::set()->forbidden();
			die($message);
		}
	}
}