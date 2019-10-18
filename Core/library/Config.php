<?php 
namespace Core;

use Core\sexy\View;
use Internal\InternalEnum;

/**
 * config
 */
class Config
{
    private static $instance = null;
	// All config file.
	private static $configFile = null;

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
	/**
	* Get config item.
	*/
	public function get($config = '', $default = null)
	{
		$config = strtolower(trim($config));
		if(empty($config)){
			return null;
		}
		$result = null;
		self::requireConfigFile(__DIR__ . '/../../Config/');
		if(!strpos($config, '.')){
			$result = self::getSimpleConfig($config, $default);
		}else{
			$confDir = explode('.', $config);
			$result = self::getMultiConfig($confDir, $default);
			unset($confDir);
		}
		return $result;
	}

	/**
	* get config by simple one key.
	*/
	protected function getSimpleConfig(string $config, $default = null)
	{
		$result = null;
		if(!empty($config)){
			if(isset(self::$configFile[$config])){
				$result = self::$configFile[$config];
			}elseif(!is_null($default)){
				$result = $default;
			}
		}
		return $result;
	} 

	/**
	* get config by array.
	*/
	protected function getMultiConfig(array $configArr, $default = null)
	{
		$result = null;
		if(!empty($configArr)){
			try{

			}catch(\Exception $e){

			}
			foreach ($configArr as $key) {
				if(empty($result)){
					if(isset(self::$configFile[$key])){
						$result = self::$configFile[$key];
					}elseif(!is_null($default)){
						$result = $default;
					}
				}else{
					if(isset($result[$key])){
						$result = $result[$key];
					}elseif(!is_null($default)){
						$result = $default;
					}
				}
			}
		}
		return $result;
	}


	/**
	* Require all config file form dir.
	*/
	protected function requireConfigFile(string $dir)
	{
		if(is_null(self::$configFile)){
			$handle = opendir($dir);
			$result = [];
			while (false !== ($file = readdir($handle))) {
				if($file != '.' && $file != '..' && $file != '.gitignore'){
					$filepath = $dir.'/'.$file;
					if(filetype($filepath) == 'dir'){
						$this->requireConfigFile($filepath);
					}else{
						$confName = explode('.', $file)[0];
						$conf = require $filepath;
						$result[strtolower($confName)] = $conf;
					}
				}
			}
			closedir($handle);
			self::$configFile = $result;
		}
		return null;
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