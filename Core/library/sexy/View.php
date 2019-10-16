<?php 
namespace Core\sexy;

use Core\Config;
/**
 * View Builder
 */
class View
{
	// class self
	private $instance = null;

	// error code
	protected $errCode = 000;

	protected $debug = null;

	// error data
	protected static $data = [];

	private function __construct()
	{
		$this->debug = Config::get('app.app_debug');
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
	public static function show($tplName, $data = []):bool
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

	// 内部引入模板方法
	protected static function fetch($tplName, $data):bool
	{
		$file = __DIR__.'/../../../Application/View/'.$tplName.'.sexy.php';
		if(!is_file($file)){
			self::bind([
				'title'=>'模板不存在！',
				'content'=>'模板[<span>'.$tplName.'.sexy.php</span>]不存在！'
			]);
			$file = __DIR__ . '/../../../Core/common/tamplate/error.sexy.php';
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
		return false;
	}

	/**
	* show error page
	*/
	public function showErr(array $info)
	{
		if($this->debug){
			self::bind([
				'code' => $info['code'],
				'title' => $info['title'],
				'content' => $info['content']
			]);
			self::show('lib/error');
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
		}
		throw new Exception('Method is not exist on static calling way.');
	}
}