<?php 
namespace Core\sexy;

use Internal\InternalEnum;
/**
 * View Builder
 */
class View
{
	// class self
	private $instance = null;

	// error code
	protected $errCode = 000;

	protected static $debug = null;

	// error data
	protected static $data = [];

	private function __construct()
	{
		self::$debug = config('app.app_debug');
		dd(4145415);
	}

	private function __clone(){}

	// 单例出口
	public static function instance()
	{
		if(self::$instance && self::$instance instanceof self){
			return self::$instance;
		}
		self::$instance = new self;
		return self::$instance;
	}

	// calling to show the view template.
	public static function show($tplName, $data = [])
	{
		return self::fetch($tplName, $data);
	}

	// binding datas to view template.
	public static function bind(array $data)
	{
		if(!empty($data)){
			self::$data = $data;
		}
		return false;
	}

	// include template internal
	protected static function fetch($tplName, $data)
	{
		$suffix = config('app.tpl_suffix', '.php');
		$file = __DIR__.'/../../../Application/View/' . $tplName . $suffix;
		if(!is_file($file)){
			self::bind([
				'title'=>'模板不存在！',
				'content'=>'模板[<span>'. $tplName . $suffix . '</span>]不存在！'
			]);
			$file = __DIR__ . '/../../../Core/common/template/error' . $suffix;
		}
		if(!empty($data)){
			self::$data = $data;
		}
		if(!empty(self::$data)){
			foreach(self::$data as $var => $value){
				$$var = $value;
			}
		}
		include_once $file;
		exit;
	}

	/**
	* show error page
	*/
	public function showErr(array $info)
	{
		if(config('app.app_debug')){
			self::bind([
				'code' => $info['code'],
				'title' => $info['title'],
				'content' => $info['content']
			]);
			if(!empty($data)){
				self::$data = $data;
			}
			if(!empty(self::$data)){
				foreach(self::$data as $var => $value){
					$$var = $value;
				}
			}
			include_once __DIR__.'/../../../Core/common/template/error'.config('app.tpl_suffix', '.php');
			exit;
		}else{
			exit('Something was error');
		}
	}

	public static function __callStatic($method, $arguments)
	{
		$self = self::instance();
		if(method_exists($self, $method)){
			return call_user_func_array([$self, $method], $arguments);
		}else{
			View::showErr([
				'code' => InternalEnum::METHOD_NOT_EXIST,
				'title' => 'Method not found.',
				'content' => 'Method is not exist on static calling way.'
			]);
		}
	}
}