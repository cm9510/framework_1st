<?php
use Core\{Config, Route};
use Core\Sexy_mvc\View;

/**
 * Launch work...
 */
class Start
{
	// self
	static private $instance = NULL;

	private function __construct(){}

	/**
	* get app debug
	*/
	static private function debug()
	{
		return Config::get('app.app_debug');
	}

	/**
	* Craete self single object.
	*/
	static public function app()
	{
		if(self::$instance && self::$instance instanceof self){
			return self::$instance;
		}
		self::$instance = new self;
		return self::$instance;
	}

	/**
	* start work.
	*/
	public function launch()
	{
		self::checkIsRegisterRule(self::getUrlInfo());
	}

	/**
	* Get url information. 
	*/
	static private function getUrlInfo()
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
	static private function checkIsRegisterRule(string $rule)
	{

		$check = Route::check($rule);
		if(!$check){
			self::notRegistedUrl($rule);
		}else{
			self::hasRegistedUrl($check);
		}
		return NUll;
	}

	/**
	* Do action if the url rule has been registed.
	*/
	static private function hasRegistedUrl(array $appAction)
	{
		$controller = 'App\\Controller\\'.$appAction['module'].'\\'.$appAction['controller'];
		$action = $appAction['action'];
		self::callFunction($controller, $action);
	}

	/**
	* Do action if the url rule not registed.
	*/
	static private function notRegistedUrl(string $uri)
	{
		if($uri === '/'){
			$module = 'App\\Controller\\'.Config::get('app.default_module');
			$controller = $module.'\\'.Config::get('app.default_controller');
			$action = Config::get('app.default_action');
		}else{
			$urlArr = explode('/', $uri);
			$module = 'App\\Controller\\'.$urlArr[1];
			$controller = isset($urlArr[2]) ?
				(empty($urlArr[2]) ? $module.'\\'.Config::get('app.default_controller') : $module.'\\'.$urlArr[2]) :
				$module.'\\'.Config::get('app.default_controller');

			$action = isset($urlArr[3]) ?
				(empty($urlArr[3]) ? Config::get('app.default_action') : $urlArr[3]) :
				Config::get('app.default_action');
		}
		self::callFunction($controller, $action);
	}

	/**
	* Calling method from class.
	*/
	static private function callFunction(string $controller, string $action)
	{
		if(!class_exists($controller)){
			if(self::debug()){
				View::bind([
					'code' => 404,
					'title' => 'Controller not found.',
					'content' => '1.Controller "'.$controller.'" not found'
				]);
				View::show('lib/error');
				exit;
			}else{
				exit('Controller not found.');
			}
		}

		$class = new $controller();
		if(!method_exists($class, $action)){
			if(self::debug()){
				View::bind([
					'code' => 404,
					'title' => 'Method not found.',
					'content' => '1.Method "'.$action.'" not found in controller '.$class.'.'
				]);
				View::show('lib/error');
				exit;
			}else{
				exit('Method not found.');
			}
		}
		return call_user_func_array([$class, $action], []);
	}
}