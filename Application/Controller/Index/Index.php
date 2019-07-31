<?php 
namespace App\Controller\Index;

use Core\Log;
use Core\Config;
use Core\Request;
use Core\Sexy_mvc\View;
use App\Controller\Controller;

class Index extends Controller
{
	public function index()
	{
		// $this->bind(['name'=>'Alice','age'=>20,'job'=>'student']);
		View::bind(['name'=>'Alice','age'=>20,'job'=>'student'] );
		return $this->view();
	}

	public function test()
	{
		echo '<pre>';
		// $request = Request::instance();
		// $id = $request->input(['sd']);
		// var_dump($id['sd']);
		// Log::notice('466');
		var_dump(Config::get('app'));	
	}
}

