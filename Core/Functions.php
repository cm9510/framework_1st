<?php

use Config\appConfig;
use Core\{Config, View};


if(!function_exists('config')){
	/**
	* get config item
	*/
	function config(string $config)
	{
		return Config::get($config);
	}
}
if(!function_exists('e')){
	/**
	* filter string to print
	*/
	function e(string $string = '')
	{
		return htmlspecialchars($string);
	}
}
if(!function_exists('view')){
	/*
	* show template
	*/
	function view($tpl){
		return View::show($tpl);
	}
}
