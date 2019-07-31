<?php 
namespace Core;

/**
 * write log
 */
class Log
{
	// self object
	static private $instance = null;

	private function __construct(){}

	// Get self
	static public function instance()
	{
		if(self::$instance && self::$instance instanceof self){
			return self::$instance;
		}
		self::$instance = new self;
		return self::$instance;
	}

	// Write log to doc txt.
	public function notice(string $text)
	{
		$logFile = __DIR__.'/../../Logs/log'.date('Ymd').'.txt';
		if(!file_exists($logFile)){
			$file = fopen($logFile, 'w');
		}else{
			$file = fopen($logFile, 'a+');
		}
		fwrite($file, '['.date('Y-m-d/H:i').']'.$text."\n");
		fclose($file);
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
