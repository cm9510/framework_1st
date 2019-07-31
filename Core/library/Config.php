<?php 
namespace Core;

/**
 * config
 */
class Config
{
	// All config file.
	static private $configFile = NULL;

	// Config item.
	static private $returnItem = NULL;

	/**
	* Get config item.
	*/
	public function get(string $config, $default = '6666')
	{
		self::requireConfigFile(__DIR__.'/../../Config/');
		$config = strtolower(trim($config));
		if(!strpos($config, '.')){
			if(isset(self::$configFile[$config])){
				self::$returnItem = self::$configFile[$config];
			}else{
				self::$returnItem = $default;
			}
		}else{
			$confDir = explode('.', $config);
			if(isset(self::$configFile[$confDir[0]])){
				$moduleConfig = self::$configFile[$confDir[0]];
				if(isset($moduleConfig[$confDir[1]])){
					self::$returnItem = $moduleConfig[$confDir[1]];
				}else{
					self::$returnItem = $default;
				}
			}else{
				self::$returnItem = $default;
			}
		}
		return self::$returnItem;
	}


	/**
	* Require all config file form dir.
	*/
	static private function requireConfigFile($dir)
	{
		if(is_null(self::$configFile)){
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
			self::$configFile = $result;
		}
		return NULL;
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