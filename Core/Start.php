<?php

use Core\{Config, Route};
use Core\sexy\View;
use Core\Common\InteralEnum;

/**
 * Launch work...
 */
class Start
{
	// self
	private static $instance = NULL;

	private function __construct(){}

	private function __clone(){}

	/**
	* Craete self single object.
	*/
	public static function app()
	{
		if(self::$instance && self::$instance instanceof self){
			return self::$instance;
		}
		self::$instance = new self;
		return self::$instance;
	}

	/**
	* get app debug
	*/
	private function debug()
	{
		return Config::get('app.app_debug');
	}

	/**
	* start work.
	*/
	public function launch()
	{
		$this->checkIsRegisterRule($this->getUrlInfo());
		return false;
	}

	/**
	* Get url information. 
	*/
	private function getUrlInfo()
	{
		// get uri.
		if(isset($_SERVER['PATH_INFO'])){
			$urlInfo = $_SERVER['PATH_INFO'];
		}elseif(isset($_SERVER['REQUEST_URI'])){
			$urlInfo = $_SERVER['REQUEST_URI'];
		}else{
			$urlInfo = '/';
		}
		if(strpos($urlInfo, '?')){
			$urlInfo = explode('?', $urlInfo)[0];
		}
		return $urlInfo;
	}

	/**
	* Check the url rule has been registed.
	*/
	private function checkIsRegisterRule(string $rule)
	{

		$check = Route::check($rule);
		if(!$check){
			$this->notRegistedUrl($rule);
		}else{
			$this->hasRegistedUrl($check);
		}
		return false;
	}

	/**
	* Do action if the url rule has been registed.
	*/
	private function hasRegistedUrl(array $appAction)
	{
		$controller = 'App\\Controller\\'.$appAction['module'].'\\'.$appAction['controller'];
		$this->callFunction($controller, $appAction['action']);
		return false;
	}

	/**
	* Do action if the url rule not registed.
	*/
	private function notRegistedUrl(string $uri)
	{
		if($uri === '/'){
			$module = 'App\\Controller\\' . ucfirst(Config::get('app.default_module'));
			$controller = $module . '\\' . ucfirst(Config::get('app.default_controller'));
			$action = Config::get('app.default_action');
		}else{
			$urlArr = explode('/', $uri);
			$module = 'App\\Controller\\'.ucfirst($urlArr[1]);
			$controller = isset($urlArr[2]) ?
				(empty($urlArr[2]) ? $module.'\\'.ucfirst(Config::get('app.default_controller')) : $module.'\\'.ucfirst($urlArr[2])) :
				$module.'\\'.ucfirst(Config::get('app.default_controller'));
			$action = isset($urlArr[3]) ?
				(empty($urlArr[3]) ? ucfirst(Config::get('app.default_action')) : $urlArr[3]) :
				Config::get('app.default_action');
		}
		unset($module);
		$this->callFunction($controller, $action);
		return false;
	}

	/**
	* Calling method from class.
	*/
	private function callFunction(string $controller, string $action)
	{
		if(!class_exists($controller)){
			View::showErr([
				'code'=> InteralEnum::CONTROLLER_NOT_EXIST,
				'title' => 'Controller not found.',
				'content' => 'Controller "'.$controller.'" not found'
			]);
		}
		$class = new $controller();
		if(!method_exists($class, $action)){
			View::showErr([
				'code' => InteralEnum::METHOD_NOT_EXIST,
				'title' => 'Method not found.',
				'content' => '1.Method "'.$action.'" not found in controller "'.get_class($class).'".'
			]);
		}
		return call_user_func_array([$class, $action], []);
	}
}