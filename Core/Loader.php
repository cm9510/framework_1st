<?php 

/**
 * 类的自动加载
 */
class AutoLoader
{
	private function __construct(){}

	static protected $mvcMap = [
		'App' => __DIR__.'/../Application',
		'Controller' => __DIR__.'/../Application/Controller',
		'Model' => __DIR__.'/../Application/Model',
		'View' => __DIR__.'/../Application/View',
		'Route' => __DIR__.'/../Route',
		'Core' => __DIR__.'/../Core/library',
		'' => '/'
	];

	static public function autoload($class)
	{
		$top = substr($class, 0, strpos($class, '\\'));
		$topDir = self::$mvcMap[$top];
		$path = substr($class, strlen($top)).'.php';
		$file = strtr($topDir.$path, '\\', '/');

		if(file_exists($file) && is_file($file)){
			include_once $file;
		}
	}
}
spl_autoload_register('AutoLoader::autoload');
