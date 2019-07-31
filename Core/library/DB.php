<?php 
namespace Core;

use Core\{Config, Log};
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

	// finally sql to get
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

	// 私有构造方法，不可外部实例化
	private function __construct()
	{
		$host = Config::get('database.host');
		$dbname = Config::get('database.dbname');
		$port = Config::get('database.port');
		$user = Config::get('database.user');
		$password = Config::get('database.password');

		try{
			$this->dbh = new PDO('mysql:host='.$host.';dbname='.$dbname.';port='.$port,$user,$password);
			$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}catch(PDOException $e){
			Log::instance()->notice($e->getMessage());
			exit($e->getMessage());
		}
		return $this;
	}

	// 单例出口
	public static function instance()
	{
		if(self::$instance && self::$instance instanceof self){
			return self::$instance;
		}
		self::$instance = new self;
		return self::$instance;
	}

	// 设置数据表名称
	public function table($table)
	{
		$this->table .= ' `'.$table.'` ';
		return $this;
	}

	// select，返回多条，二维数组
	public function select():array
	{
		$finalSql = 'SELECT'.$this->cols.'FROM'.
					$this->table.
					$this->where.
					$this->orderBy.
					$this->limit;
		$this->getSql = $finalSql;
		// echo $finalSql.'<br>'; //die;
		$result = $this->dbh->query($finalSql);
		$result = $result->fetchAll(PDO::FETCH_ASSOC);
		return $result ? $result : [];
	}

	// 单条插入
	// return int|bool
	public function insert(array $arr)
	{
		if(!is_array($arr)){
			throw new Exception("argument type for array.", 1);
			return false;
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
		} catch (PDOException $e) {
			Log::instance()->notice($e->getMessage());
			return false;
		}
	}

	// 多条插入
	public function insertAll(array $arr)
	{
		if(!is_array($arr)){
			throw new Exception("argument type for array.", 1);
			return false;
		}elseif(!is_array($arr[0])){
			throw new Exception("argument type for array.", 1);
			return false;
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
			$vals = trim($vals, ',');
			$values .= '('.$vals.'),';
		}
		$columns = trim($columns, ',');
		$values = trim($values, ',');

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

	// 更新
	public function update(array $arr)
	{
		if(!is_array($arr)){
			throw new Exception("argument type for array.", 1);
			return false;
		}

		$col_val = '';
		foreach ($arr as $col => $val) {
			$col_val .= " `".$col."`='".$val."',";
		}
		$col_val = trim($col_val,',');

		$finalSql = 'UPDATE'.$this->table.'SET'.$col_val.$this->where;
		echo $finalSql;// die;
		$this->getSql = $finalSql;

		try {
			$row = $this->dbh->exec($finalSql);
			return $row;
		} catch (PDOException $e) {
			Log::instance()->notice($e->getMessage());
			return false;
		}
	}

	// 删除
	// return int|bool
	public function delete()
	{
		if(func_num_args() > 1){
			throw new Exception("too many parameters.", 1);
			return false;
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
		}catch(PDOException $e){
			Log::instance()->notice($e->getMessage());
			return false;
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
		if(func_num_args() === 0){
			throw new Exception("missing arguments.", 1);
			return false;
		}

		// 多条件数组
		if(func_num_args() === 1 && !is_array(func_get_arg(0))){
			throw new Exception("arguments type error, incorect for array.", 1);
			return false;
		}elseif(func_num_args() === 1 && !is_array(func_get_arg(0)[0])){
			throw new Exception("arguments type error, incorect for array.", 1);
			return false;
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
		return $this;
	}

	// order by排序
	public function orderBy($col, $type = 'ASC')
	{
		$this->orderBy = ' ORDER BY `'.$col.'` '.$type.' ';
		return $this;
	}

	// limit 结果块
	public function limit($start = 1, $takes)
	{
		$this->limit = ' LIMIT '.($start-1).', '.$takes;
	}

	// 获取SQL语句
	public function getSql()
	{
		return $this->getSql;
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



$db = DB::instance();
// $res = DB::table('font_credit_log')->field(['user_id','credit','type'])
// 			->where([
// 				['credit','>',30]
// 			])->select();

// $row = $db->table('font_credit_log')->insertAll([
// 	[
// 	'user_id' => 9,
// 	'credit' => 19,
// 	'customer_order_id' => 3,
// 	'type' => 'abcdefg',
// 	'status' => 1,
// 	'create_time' => time()
// 	],
// 	[
// 	'user_id' => 8,
// 	'credit' => 18,
// 	'customer_order_id' => 5,
// 	'type' => 'abcdefg',
// 	'status' => 4,
// 	'create_time' => time()
// 	],
// 	[
// 	'user_id' => 7,
// 	'credit' => 17,
// 	'customer_order_id' => 7,
// 	'type' => 'abcdefg',
// 	'status' => 7,
// 	'create_time' => time()
// 	],
// ]);

// $row = $db->table('font_credit_log')->where('id', 130)->update(['status'=>1,'create_time'=>time()]);

$row = $db->table('font_credit_log')->delete(133);

var_dump($row);


// var_dump($res);
echo '<hr>'.$db->getSql();