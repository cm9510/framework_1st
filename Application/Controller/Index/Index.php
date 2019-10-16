<?php 
namespace App\Controller\Index;

use Core\sexy\View;
use App\Controller\Controller;
use Core\{Log, Config, Request};

class Index extends Controller
{
	public function index()
	{
		// $this->bind(['name'=>'Alice','age'=>20,'job'=>'student']);
		View::bind(['name'=>'Alice','age'=>20,'job'=>'student']);
		return $this->view();
	}

	public function test()
	{
		dd(Config::get('app'));
		// $request = Request::instance();
		// $id = $request->input(['sd']);
		// var_dump($id['sd']);
		// Log::notice('466');
	}
}

