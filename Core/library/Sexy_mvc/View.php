<?php 
namespace Core\Sexy_mvc;

/**
 * View Builder
 */
class View
{
	// error code
	protected static $errCode = 000;

	// error data
	protected static $data = [];

	// calling to show the view template.
	public function show($tplName, $data = []):bool
	{
		return self::fetch($tplName, $data);
	}

	// binding datas to view template.
	public static function bind(array $data)
	{
		if(empty($data)){
			return false;
		}
		self::$data = $data;
		return false;
	}

	// 内部引入模板方法
	protected function fetch($tplName, $data):bool
	{
		$file = __DIR__.'/../../../Application/View/'.$tplName.'.sexy.php';
		if(!is_file($file)){
			self::bind([
				'title'=>'模板不存在！',
				'content'=>'模板[<span>'.$tplName.'.sexy.php</span>]不存在！'
			]);
			$file = __DIR__.'/../../../Application/View/lib/error.sexy.php';
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

	static public function __callStatic($method, $arguments)
	{
		$self = self::instance();
		if(method_exists($self, $method)){
			return call_user_func_array([$self, $method], $arguments);
		}
		throw new Exception('Method is not exist on static calling way.');
	}
}