<?php
class DB {
	private $DB_HOST;
	private $DB_USER;
	private $DB_PASSWORD;
	private $DB_NAME;
	public $conn;
	public function __construct($HOST,$USER,$PASSWORD,$DBNAME) {
		$this->HOST = $HOST;
		$this->USER= $USER;
		$this->PASSWORD = $PASSWORD;
		$this->DBNAME = $DBNAME;
		$this->dbconnect();
	}
	//连接数据库
	public function dbconnect() {
		$this->conn=@mysql_connect($this->HOST,$this->USER,$this->PASSWORD);
		if (!@$this->conn) {
			exit('数据库连接失败');
		}
		if (!@mysql_select_db($this->DBNAME,$this->conn)) {
			exit('数据库连接失败'.mysql_error());
		}
		if (!@mysql_query('SET NAMES UTF8')) {
			exit('字符集设置错误');
		}
	}
	//添加数据
	public function add ($sql) {
		$row=@mysql_query($sql);
		if (!$row) {
			exit;
		}else {
			if (mysql_affected_rows()>0) {
				$this->myclose();
			}else {
				exit;
			}
		}
	}
	//关闭数据库
	public function myclose() {
		if (!@mysql_close($this->conn)) {
			exit('数据库关闭异常');
		}
	}
}
?>