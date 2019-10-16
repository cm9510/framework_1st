<?php 
namespace Core;

use Core\sexy\View;

/**
 * config
 */
class Config
{
	// All config file.
	private $configFile = null;

	private function __construct(){}

	private function __clone(){}

	/**
	* Get config item.
	*/
	public function get(string $config = '', $default = null)
	{
		$config = strtolower(trim($config));
		if(empty($config)){
			return null;
		}
		$result = null;
		$this->requireConfigFile(__DIR__ . '/../../Config/');
		if(!strpos($config, '.')){
			$result = $this->getSimpleConfig($config, $default);
		}else{
			$confDir = explode('.', $config);
			$result = $this->getMultiConfig($confDir, $default);
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
			if(isset($this->configFile[$config])){
				$result = $this->configFile[$config];
			}elseif(!is_null($default)){
				$result = $default;
			}else{
				$result = null;
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

			}catch(\Exeception $e){

			}
			foreach ($configArr as $key) {
				if(empty($result)){
					if(isset($this->configFile[$key])){
						$result = $this->configFile[$key];
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
		if(is_null($this->configFile)){
			$handle = opendir($dir);
			$result = [];
			while (false !== ($file = readdir($handle))) {
				if($file != '.' && $file != '..'){
					$filepath = $dir.'/'.$file;
					if(filetype($filepath) == 'dir'){
						echo $filepath;
						requireConfigFile($filepath);
					}else{
						$confName = explode('.', $file)[0];
						$conf = require $filepath;
						$result[strtolower($confName)] = $conf;
					}
				}
			}
			closedir($handle);
			$this->configFile = $result;
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
				'code'=> InteralEnum::METHOD_NOT_EXIST,
				'title'=> 'Method is not exist.',
				'content'=> 'Method is not exist on static calling way.'
			]);
		}
	}
}