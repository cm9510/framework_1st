<?php 
namespace Core;

use Core\{Config, Log};
use Internal\InternalEnum;

/**
 * database class
 */
class DB
{
	private $sql = 'select * from table where id=1';

	// self 
	private static $instance = null;

	// PDO object
	protected $dbh;

	// final sql string
	protected $getSql = '';

	// where condition
	protected $where = '';

	// field columns
	protected $cols = ' * ';

	// sort by columns ASC|DESC
	protected $orderBy = '';

	// table name
	protected $table = '';

	// limits
	protected $limit = '';

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

	private function __clone(){}

	public static function instance()
	{
		if(self::$instance && self::$instance instanceof self){
			return self::$instance;
		}
		self::$instance = new self;
		return self::$instance;
	}

	// set table name
	public function table(string $table)
	{
		$this->table .= ' `'.$table.'` ';
		return $this;
	}

	// excute a sql string
	public function query(string $sql)
	{
		try{
			$result = $this->dbh->query($sql);
			switch (gettype($result)) {
				case 'intger': $result = (int)$result; break;
				case 'boolean': $result = (bool)$result; break;				
				default:
					$result = $result->fetchAll(PDO::FETCH_ASSOC);
					break;
			}
			return $result;
		}catch(\PDOException $e){
			Log::instance()->notice($e->getMessage());
			exit($e->getMessage());
		}
	}

	// select，二维数组
	public function select():array
	{
		$finalSql = 'SELECT' . $this->cols . 'FROM' . $this->table . $this->where .
					$this->orderBy . $this->limit;
		$this->getSql = $finalSql;
		$result = $this->dbh->query($finalSql);
		$result = $result->fetchAll(PDO::FETCH_ASSOC);
		return $result ? $result : [];
	}

	// insert only
	public function insert(array $arr)
	{
		if(!is_array($arr)){
			return '插入数据必须是数组.';
		}

		$columns = '';
		$values = '';
		foreach ($arr as $col => $val) {
			$columns .= '`'.$col.'`,';
			$values .= "'".$val."',";
		}
		$columns = trim($columns, ',');
		$values = trim($values, ',');

		$finalSql = 'INSERT INTO'.$this->table.'('.$columns.') VALUES('.$values.')';
		$this->getSql = $finalSql;

		try {
			$row = $this->dbh->exec($finalSql);
			return $row ? $row : 0;
		} catch (\PDOException $e) {
			Log::instance()->notice($e->getMessage());
			return false;
		}
	}

	// 多条插入
	public function insertAll(array $arr)
	{
		$result = null;
		if($arr){
			if (count($arr) == count($arr, 1)) {
				exit('插入数据格式错误.');
			}
			$columns = '';
			$values = '';
			foreach ($arr[0] as $col => $val) {
				$columns .= '`'.$col.'`,';
			}
			foreach ($arr as $v1) {
				$vals = '';
				foreach ($v1 as $v2) {
					$vals .= "'".$v2."',";
				}
				$vals = rtrim($vals, ',');
				$values .= '('.$vals.'),';
			}
			$columns = rtrim($columns, ',');
			$values = rtrim($values, ',');

			$finalSql = 'INSERT INTO'.$this->table.'('.$columns.') VALUES'.$values;
			$this->getSql = $finalSql;
			
			try {
				$row = $this->dbh->exec($finalSql);
				return $row;
			} catch (PDOException $e) {
				Log::instance()->notice($e->getMessage());
				return false;
			}
		}
	return $result;
	}

	// 更新
	public function update(array $arr)
	{
		if(!is_array($arr)){
			return '数据格式必须是数组.';
		}

		$col_val = '';
		foreach ($arr as $col => $val) {
			$col_val .= " `".$col."`='".$val."',";
		}
		$col_val = trim($col_val,',');

		$finalSql = 'UPDATE'.$this->table.'SET'.$col_val.$this->where;
		$this->getSql = $finalSql;

		try {
			$row = $this->dbh->exec($finalSql);
			return $row;
		} catch (PDOException $e) {
			Log::instance()->notice($e->getMessage());
			return $e->getMessage();
		}
	}

	// 删除
	// return int|bool
	public function delete()
	{
		if(func_num_args() > 1){
			return '参数错误.';
		}
		if(func_num_args() === 0){
			$finalSql = 'DELETE FROM'.$this->table.$this->where;
		}elseif(func_num_args() === 1){
			$finalSql = 'DELETE FROM'.$this->table.' WHERE `id`='.func_get_arg(0);
		}
		$this->getSql = $finalSql;

		try{
			$row = $this->dbh->query($finalSql);
			return $row ? $row->rowCount() : 0;
		}catch(\PDOException $e){
			Log::instance()->notice($e->getMessage());
			return $e->getMessage();
		}
	}

	// 指定字段
	public function field($cols = [])
	{
		if(is_string($cols)){
			$this->cols = trim($cols).' ';
		}elseif(is_array($cols)){
			$this->cols = '';
			foreach ($cols as $v) {
				$this->cols .= '`'.$v.'`,';
			}
			$this->cols = ' '.trim($this->cols, ',').' ';
		}
		return $this;
	}

	// where条件
	public function where()
	{
		try{
			if(func_num_args() === 0){
				throw new \Exception("Missing arguments.", 1);
			}

			// 多条件数组
			if(func_num_args() === 1 && !is_array(func_get_arg(0))){
				throw new \Exception("arguments type error, incorect for array.", 1);
			}elseif(func_num_args() === 1 && !is_array(func_get_arg(0)[0])){
				throw new \Exception("arguments type error, incorect for array.", 1);
			}elseif(func_num_args() === 1 && is_array(func_get_arg(0))){
				foreach (func_get_arg(0) as $v) {
					$this->where .= ' `'.$v[0].'` '.$v[1].''.$v[2].' and';
				}
				$this->where = trim(' WHERE '.$this->where, 'and');
			}

			// 单条件
			if(func_num_args() === 2){
				$this->where .= ' WHERE `'.func_get_arg(0).'` = '.func_get_arg(1).' ';
			}elseif(func_num_args() === 3){
				$this->where .= ' WHERE `'.func_get_arg(0).'`'.func_get_arg(1).func_get_arg(2).' ';
			}
		}catch(\Exception $e){
			Log::instance()->notice($e->getMessage());
			View::showErr([
				'code'=> InternalEnum::ERR_COMMON,
				'title'=> InternalEnum::ERR_EXCEPTION_TITLE,
				'content'=> $e->getMessage()
			]);
		}
		return $this;
	}

	// order by sort
	public function orderBy(string $col, $type = 'ASC')
	{
		$this->orderBy = ' ORDER BY `'.$col.'` '.$type.' ';
		return $this;
	}

	// limit trunck
	public function limit($start = 1, $takes)
	{
		$this->limit = ' LIMIT '.($start-1).', '.$takes;
	}

	// 获取SQL语句
	public function getSql()
	{
		dd($this->getSql);
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
	}
}