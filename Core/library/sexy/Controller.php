<?php 
namespace Core\sexy;

/*
* Model Builder
*/
class Controller
{
	public function view($tpl = '', array $data = [])
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