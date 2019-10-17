<?php
use \Core\Route;
use \Core\sexy\View;
use \Internal\InternalEnum;

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
		return config('app.app_debug');
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
		$this->callFunction($controller, $appAction['action'], true);
		return false;
	}

	/**
	* Do action if the url rule not registed.
	*/
	private function notRegistedUrl(string $uri)
	{	
		$defController = ucfirst(config('app.default_controller'));
		$defAction = config('app.default_action');
		if($uri === '/'){
			$module = 'App\\Controller\\' . ucfirst(config('app.default_module'));
			$controller = $module . '\\' . $defController;
			$action = $defAction;
		}else{
			$urlArr = explode('/', $uri);
			$module = 'App\\Controller\\' . ucfirst($urlArr[1]);
			$controller = isset($urlArr[2]) ?
				(empty($urlArr[2]) ? $module.'\\'.$defController : $module.'\\'.ucfirst($urlArr[2])) :
				$module . '\\' . $defController;
			$action = isset($urlArr[3]) ? (empty($urlArr[3]) ? $defAction : $urlArr[3]) : $defAction;
		}
		unset($defController, $defAction, $module);
		$this->callFunction($controller, $action, false);
		return false;
	}

	/**
	* Calling method from class.
	*/
	private function callFunction(string $controller, string $action, bool $isRule)
	{
		if(!class_exists($controller)){
			$controller = explode("\\", $controller);
			$controller = end($controller);
			if($isRule){
				View::showErr([
					'code'=> InternalEnum::CONTROLLER_NOT_EXIST,
					'title' => 'Controller not found.',
					'content' => 'Controller "'.$controller.'" not found'
				]);
			}else{
				View::showErr([
					'code'=> InternalEnum::URL_RULE_ERR,
					'title' => 'Routing errors.',
					'content' => 'Routing errors, cause the controller not found.'
				]);
			}
		}
		$class = new $controller();
		if(!method_exists($class, $action)){
			if($isRule){
				View::showErr([
					'code' => InternalEnum::METHOD_NOT_EXIST,
					'title' => 'Method not found.',
					'content' => 'Method "'.$action.'" not found in controller "'.get_class($class).'".'
				]);
			}else{
				View::showErr([
					'code' => InternalEnum::URL_RULE_ERR,
					'title' => 'Routing errors.',
					'content' => 'Routing errors, cause the method not found in controller "'.get_class($class).'".'
				]);
			}
		}
		return call_user_func_array([$class, $action], []);
	}
}