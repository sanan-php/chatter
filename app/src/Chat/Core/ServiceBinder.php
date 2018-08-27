<?php

namespace Chat\Core;

abstract class ServiceBinder
{
	final public static function bind(string $class){
		if(!class_exists($class)) {
			die("Class {$class} not found.");
		}

		return new $class();
	}
}