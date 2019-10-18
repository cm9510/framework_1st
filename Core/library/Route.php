<?php 
namespace Core;

use Core\sexy\View;
use Internal\InternalEnum;

/**
 * Rule of route register
 */
class Route
{
    private static $instance = null;

	private function __construct(){}

	private function __clone(){}

	private static $getRule = [];

	public static function instance()
    {
        if(self::$instance && self::$instance instanceof self){
            return self::$instance;
        }
        self::$instance = new self;
        return self::$instance;
    }

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
				throw new \Exception("Request Method Error.", 1);
			}
			$mca = explode('\\', $appAction['mca']);
			return ['module'=> ucfirst($mca[0]), 'controller'=> ucfirst($mca[1]), 'action'=> $mca[2]];
		}catch(\Exception $e){
			Log::instance()->notice($e->getMessage());
			View::showErr([
				'code'=> InternalEnum::ERR_COMMON,
				'title'=> InternalEnum::ERR_ERROR_TITLE,
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
				'code'=> InternalEnum::METHOD_NOT_EXIST,
				'title'=> 'Method is not exist.',
				'content'=> 'Method is not exist on static calling way.'
			]);
		}
        exit;
	}
}

