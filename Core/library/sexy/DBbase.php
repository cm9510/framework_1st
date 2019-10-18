<?php
namespace Core\sexy;

use Core\{Config, Log};

class DBbase
{
	// table name
	protected $table = null;

	// PDO statementt
	protected $dbh = null;


	private function __construct()
	{
		$host 		= Config::get('database.host');
		$dbname 	= Config::get('database.dbname');
		$port 		= Config::get('database.port');
		$user 		= Config::get('database.user');
		$password 	= Config::get('database.password');

		try{
			$this->dbh = new PDO('mysql:host='.$host.';dbname='.$dbname.';port='.$port,$user,$password);
			$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}catch(\PDOException $e){
			Log::instance()->notice($e->getMessage());
			exit($e->getMessage());
		}
		return $this;
	}

	// insert a strip
	public function _insert(array $arr, $getId = false)
	{
		$result = false;
		if($arr){
			$columns = '';
			$values = '';
			$data = [];

			foreach ($arr as $col => $val) {
				$columns .= '`'.$col.'`,';
				$values .= '?,';
				array_push($data, $val);
			}
			$columns = rtrim($columns, ',');
			$values = rtrim($values, ',');
			$sql = 'INSERT INTO'.$this->table.'('.$columns.') VALUES('.$values.')';
			$this->getSql = $sql;

			try {
				$stmt = $this->dbh->prepare($sql);
				$result = $stmt->execute($data);
				if($result && $getId){
					$result = $stmt->lastInsertId();
				}
			} catch (\PDOException $e) {
				Log::instance()->notice($e->getMessage());
				$result = false;
			}
		}
		return $result;
	}

}