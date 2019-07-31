<?php 
namespace Core\Sexy_mvc;

use Core\Sexy_mvc\View;
/*
* Model Builder
*/
class Controller
{
	public function view(string $tpl = '', array $data = [])
	{
		if(empty($tpl)){
			$module = get_class($this);
			$module = explode('\\', $module);
			$module = strtolower(end($module));
			$tpl = $module.'/'.$module;
		}
		if(!empty($data)){
			View::bind($data);
		}
		View::show($tpl);
	}

	public function bind(array $data = [])
	{
		View::bind($data);
	}
}