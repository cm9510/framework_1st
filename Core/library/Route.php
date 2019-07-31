<?php 
namespace Core;

/**
 * 注册路由规则
 */
class Route
{
	static protected $getRule = [];

	// 注册get路由
	static public function get(string $routeRule, string $appAction)
	{
		self::$getRule[$routeRule] = ['mca' => $appAction, 'method' => 'GET'];
	}

	// 注册post路由
	static public function post(string $routeRule, string $appAction)
	{
		self::$getRule[$routeRule] = ['mca' => $appAction, 'method' => 'POST'];
	}

	static public function check(string $rule)
	{
		// 如果未注册
		if(!isset(self::$getRule[$rule])){
			return false;
		}

		// 如果已注册
		$appAction = self::$getRule[$rule];
		if(strtoupper($_SERVER['REQUEST_METHOD']) != $appAction['method']){
			throw new Exception("Request Method Error GET.", 1);
			exit;
		}
		$mca = explode('/', $appAction['mca']);
		return [
			'module' => $mca[0],
			'controller' => $mca[1],
			'action' => $mca[2]
		];
	}

	static public function __callStatic($method, $arguments)
	{
		$self = self::instance();
		if(method_exists($self, $method)){
			return call_user_func_array([$self, $method], $arguments);
		}
		throw new Exception('Method is not exist on static calling way.');
	}
}

