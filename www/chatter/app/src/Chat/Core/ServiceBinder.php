<?php

namespace Chat\Core;

class ServiceBinder
{
	private static $services = [];
	
	public static function bind(string $class){
		if(isset(self::$services[$class])) {
			return self::$services[$class];
		}
		if (!class_exists($class)) {
			die("Class {$class} not found.");
		}
		self::$services[$class] = new $class();

		return self::$services[$class];
	}
}
