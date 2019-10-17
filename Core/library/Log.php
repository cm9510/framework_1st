<?php 
namespace Core;

use Internal\InternalEnum;
/**
 * write log
 */
class Log
{
	// self object
	private static $instance = null;

	private function __construct(){}

	private function __clone(){}

	// Get self
	public static function instance()
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
		$logFile = __DIR__.'/../../Storage/logs'.date('Ymd').'.txt';
		if(!file_exists($logFile)){
			$file = fopen($logFile, 'w');
		}else{
			$file = fopen($logFile, 'a+');
		}
		fwrite($file, '['.date('Y-m-d/H:i').']'.$text."\n");
		fclose($file);
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
