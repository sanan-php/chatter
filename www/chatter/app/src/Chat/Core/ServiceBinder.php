<?php

namespace Chat\Core;

class ServiceBinder
{
	public static function bind(string $class){
		if (!class_exists($class)) {
			die("Class {$class} not found.");
		}

		return new $class();
	}
}
