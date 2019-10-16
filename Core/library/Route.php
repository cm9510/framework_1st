<?php 
namespace Core;

use Core\Log;
use Core\sexy\View;
use Core\Common\InteralEnum;

/**
 * Rule of rute register
 */
class Route
{
	private function __construct(){}

	private function __clone(){}

	private static $getRule = [];

	// sign get url
	public static function get(string $routeRule, string $appAction)
	{
		self::$getRule[trim($routeRule)] = ['mca' => trim($appAction), 'method' => 'GET'];
		return false;
	}

	// sign post url
	public static function post(string $routeRule, string $appAction)
	{
		self::$getRule[trim($routeRule)] = ['mca' => trim($appAction), 'method' => 'POST'];
		return false;
	}

	//checkout rule has registed
	public static function check(string $rule)
	{
		$rule = trim($rule);
		// if not
		if(!isset(self::$getRule[$rule])){
			return false;
		}

		// if down
		try{
			$appAction = self::$getRule[$rule];
			if(strtoupper($_SERVER['REQUEST_METHOD']) != $appAction['method']){
				throw new Exception("Request Method Error.", 1);
			}
			$mca = explode('/', $appAction['mca']);
			return ['module' => $mca[0], 'controller' => $mca[1], 'action' => $mca[2]];
		}catch(\Exception $e){
			Log::instance()->notice($e->getMessage());
			View::showErr([
				'code'=> InteralEnum::ERR_COMMON,
				'title'=> InteralEnum::ERR_ERROR_TITLE,
				'content'=> $e->getMessage()
			]);
		}
	}

	public static function __callStatic($method, $arguments)
	{
		$self = self::instance();
		if(method_exists($self, $method)){
			return call_user_func_array([$self, $method], $arguments);
		}else{
			View::showErr([
				'code'=> InteralEnum::METHOD_NOT_EXIST,
				'title'=> 'Method is not exist.',
				'content'=> 'Method is not exist on static calling way.'
			]);
		}
	}
}

