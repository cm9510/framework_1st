<?php 

/**
 * autoload class
 */
class AutoLoader
{
	private function __construct(){}

	private function __clone(){}

	protected static $mvcMap = [
		'App' => __DIR__ . '/../Application',
		'Route' => __DIR__ . '/../Route',
		'Core' => __DIR__ . '/../Core/library',
		'Internal' => __DIR__ . '/../Core/common',
		'' => __DIR__ . '/',
		'\\' => ''
	];

	public static function autoload($class)
	{
		$top = substr($class, 0, strpos($class, "\\"));
		$topDir = self::$mvcMap[$top];
		$path = substr($class, strlen($top)) . '.php';
		$file = strtr($topDir . $path, "\\", '/');

		if(file_exists($file) && is_file($file)){
			include_once $file;
		}
		return false;
	}
}
spl_autoload_register('AutoLoader::autoload');