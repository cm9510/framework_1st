<?php
namespace Core\sexy;

use Core\Log;

/*
* Model Builder
*/
class Model
{
	protected $table = null;

	protected $lastSql = null;

	private function __construct()
	{
		$host 		= config('database.host');
		$dbname 	= config('database.dbname');
		$port 		= config('database.port');
		$user 		= config('database.user');
		$password 	= config('database.password');

		try{
			$this->dbh = new PDO('mysql:host='.$host.';dbname='.$dbname.';port='.$port,$user,$password);
			$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->dbh->query('SET NAMES utf8;');
		}catch(\PDOException $e){
			Log::instance()->notice($e->getMessage());
			exit($e->getMessage());
		}
		return $this;
	}

	// create a model.
	public function create(array $data)
	{
		if(!is_array($data)){
			exit('数据格式错误，必须是数组。');
		}
		$sql = 'INSERT INTO '.$this->table.' ('.$this->arrangeCol($data).') VALUES';
		$sql .= $this->arrangeVal($data);
		



	}

	private function arrangeCol(array $data)
	{
		if(!is_array($data)){
			exit('数据格式错误，必须是数组。');
		}
		$fields = '';
		if(count($data, 0) != count($data, 1)){
			$data = reset($data);
		}
		foreach ($data as $key => $value) {
			$fields .= '`'.$key.'`,';
		}
		return rtrim($fields, ',');
	}

	private function arrangeVal(array $data)
	{
		if(!is_array($data)){
			exit('数据格式错误，必须是数组。');
		}
		$result = null;
		$values = '';
		if(count($data, 0) == count($data, 1)){
			foreach ($data as $key => $value) {
				$values .= "'{$value}',";
			}
			$result = "({$values});";
		}else{
			foreach ($data as $value) {
				$values .= "(";
				$str = '';
				foreach ($values as $key => $val) {
					$str .= "'{$val}',";
				}
				$values .= rtrim($str, ',')."),";
			}
			$result = rtrim($values, ',');
		}
		return $result;
	}

	private function getModelById(int $id)
	{

	}
}
