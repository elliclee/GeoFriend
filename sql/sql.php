<?php

/*
 * 	mysql数据库操作类
 * 	@package	sql
 * 	@author		mooner
 * 	@version	2011-05-20
 * 	@copyrigth	Team Luff
 */

class sql {

    private $db_host;           //数据库主机
    private $db_user;           //数据库用户名
    private $db_pwd;            //数据库密码
    private $db_database;       //数据库名
    private $conn;              //数据库连接标识;
    private $sql;               //sql执行的语句
    private $result;            //query的资源标识符
    private $coding;            //数据库编码,gbk,utf8,gb2312
    private $show_error = true; //本地调试使用,打印错误（你们不用理这个）

    /**
     * 构造函数
     *
     * @access public
     * @param string $db_host   数据库主机
     * @param string $db_user   数据库用户名
     * @param string $db_pwd    数据库密码
     * @param string $db_database   数据库名
     * @param string $coding    编码格式：gbk,utf8,gb2312
     * @return void
     */

    public function __construct() {
        $this->db_host = "127.0.0.1";
        $this->db_user = "root";
        $this->db_pwd = "";
        $this->db_database = "geofriend";
        $this->coding = "UTF8";
        $this->connect();
    }

    /**
     * 链接数据库
     *
     * @access private
     * @return void
     */
    private function connect() {
        $this->conn = @mysql_connect($this->db_host, $this->db_user, $this->db_pwd) or die("数据库连接失败");
        mysql_query("SET NAMES 'UTF'");
        if (!$this->conn) {
            //show_error开启时,打印错误
            if ($this->show_error) {
                $this->show_error('错误提示：链接数据库失败！');
            }
        }

        if (!@mysql_select_db($this->db_database, $this->conn)) {
            //打开数据库失败
            if ($this->show_error) {
                $this->show_error('错误提示：打开数据库失败！');
            }
        }

        if (!@mysql_query("set names $this->coding")) {
            //设置编码失败
            if ($this->show_error) {
                $this->show_error('错误提示：设置编码失败！');
            }
        }
    }

    /**
     * 可执行查询添加修改删除等任何sql语句
     *
     * @access public
     * @parameter string $sql   sql语句
     * @return resource  资源标识符
     */
    public function query($sql) {
        $this->sql = $sql;
        $result = mysql_query($this->sql, $this->conn);
        //echo $result . "<br/>";
        if (!$result) {
            //query执行失败,打印错误
            $this->show_error("错误的sql语句：", $this->sql);
        } else {
            //返回资源标识符
            return $this->result = $result;
        }
    }

    /**
     * 查询mysql服务器中所有的数据库
     *
     * @access public
     * @return void
     */
    public function show_databases() {
        $this->query("show databases");
        //打印数据库的总数
        echo "现有数据库：" . mysql_num_rows($this->result);
        echo "<br />";
        $i = 1;
        //循环输出每个数据库的名称
        while ($row = mysql_fetch_array($this->result)) {
            echo "$i $row[database]" . "<br />";
            $i++;
        }
    }

    /**
     * 查询数据库下所有表名
     *
     * @access public
     * @return void
     */
    public function show_tables() {
        $this->query("show tables");
        //打印表的总数
        echo "数据库{$this->db_database}共有" . mysql_num_rows($this->result) . "张表：";
        echo "<br />";
        //构造数组下标,循环出数据库所有表名
        $column_name = "tables_in_" . $this->db_database;
        $i = 1;
        //循环输出每个表的名称
        while ($row = mysql_fetch_array($this->result)) {
            echo "$i $row[$column_name]" . "<br />";
            $i++;
        }
    }

    /**
     * 取得上一步 insert 操作产生的 id
     *
     * @access public
     * @return integer
     */
    public function insert_id() {
        return mysql_insert_id();
    }

    ///=================================下面这几个函数是最实用的，看我的test.php里面的用法===========================================//
    /**
     * select查询语句
     *
     * @access public
     * @parameter string $tbname  表名
     * @parameter string $where   查询的条件
     * @parameter string $limit   
     * @parameter string $fields  字段名
     * @parameter string $orderby 排序标准(默认为user_id，用的时候要根据具体情况来设置)
     * @parameter string $sort    递增或递减排序
     * @return 
     */
    public function select($tbname, $where="", $limit=0, $fields="*", $orderby="user_id", $sort="DESC") {
        $select_sql = "SELECT " . $fields . " FROM " . $tbname . " " . ($where ? " WHERE " . $where : "") . " ORDER BY " . $orderby . " " . $sort . ($limit ? " limit " . $limit : "");
        //echo "<br/>" . $sql . "<br/>";
        return $this->query($select_sql);
    }
    

    /**
     * insert插入语句
     *
     * @access public
     * @param string $tbname  表名
     * @param array $row      一行记录
     * @return bool
     */
    public function insert($tbname, $row) {
        foreach ($row as $key => $value) {
            $sqlfield .= $key . ",";
            $sqlvalue .= "'" . $value . "',";
        }
        $insert_sql = "INSERT INTO `" . $tbname . "`(" . substr($sqlfield, 0, -1) . ") VALUES (" . substr($sqlvalue, 0, -1) . ")";
        //mysql_query("set names utf8"); // 防止中文字符插入mysql数据库后成了乱码
        return $this->query($insert_sql);
    }

    /**
     * update更新语句
     *
     * @access public
     * @param string $tbname     表名
     * @param string $row        更新的内容
     * @param string $where      条件
     * @return bool
     */
    public function update($tbname, $row, $where) {
        foreach ($row as $key => $value) {
            $sqlud .= $key . "= '" . $value . "',";
        }
        $select_sql = "UPDATE `" . $tbname . "` SET " . substr($sqlud, 0, -1) . " WHERE " . $where;
        //echo $sql;
        return $this->query($select_sql);
    }

    /**
     * delete查询语句
     *
     * @access public
     * @param string $tbname    表名
     * @param string $where     删除的条件
     * @return bool
     */
    public function delete($tbname, $where) {
        $delete_sql = "DELETE FROM `" . $tbname . "` WHERE " . $where;
        return $this->query($delete_sql);
    }

    /**
     * 判断表中的某个字段是否已存在
     * 可用于注册时判断email是否已存在
     *
     * @access public
     * @param string $tbname    表名
     * @param string $field     字段名
     * @param string $input     输入值
     * @return bool
     */
    public function is_exist($tbname, $field, $input) {
        $isExist = false;
        $query = $this->select($tbname, "");
        while ($row = mysql_fetch_array($query)) {
            //print_r($row[$field]);
            if($input == $row[$field]) {
                $isExist = true;
                break;
            }
        }
        return $isExist;
    }
    
    /**
     * 获取一个表中有多少条记录
     *
     * @access public
     * @param string $tbname    表名
     * @return integer
     */    
    public function total_rows($tbname) {
        $sql = "select * from " . $tbname;
        $query = $this->query($sql);
        $count =  mysql_num_rows($query); 
        
        return $count; 
    }

    ///=======================================================================================================//

    /**
     * 计算结果集条数
     *
     * @access public
     * @return integer
     */
    public function num_rows() {
        return mysql_num_rows($this->result);
    }

    /**
     * 从结果集中取得一行作为枚举数组 
     * 
     * @param object $query 
     * @return array 
     */
    public function fetch_row($tbname, $where="") {
        $query = $this->select($tbname, $where);
        //echo $query;
        return mysql_fetch_row($query);     
    }

    /**
     * 取得记录集,获取数组：索引和关联
     * (fetch_row只可以通过数字来索引，而fetch_array可以通过字符所以，如row['email']="11@qq.com")
     *
     * @access public
     * @return array
     */
    public function fetch_array($tbname, $where="") {
        $my_query = $this->select($tbname, $where);
        return mysql_fetch_array($my_query);
    }
    
    // 这个可以直接传入select等语句，会返回相应的结果的
    public function fetch_array2($my_sql) {
        $my_query = $this->query($my_sql);
        //echo $my_query . "<br/>";
        return mysql_fetch_array($my_query);
    }

    // 这个可以直接传入select等语句，会返回一个二维数组
    // 主要用于获取位置信息get_informatioin.php那里,或者可用于获取多条记录的时候
    public function fetch_array3($my_sql) {
        $result = array();
        $my_query = $this->query($my_sql);
        $i = 0;
        while($row = mysql_fetch_array($my_query)) {
            $result[$i++] = $row;
        }
        
        return $result;
    }

    /**
     * 取影响条数 
     * 
     * @return int 
     */
    function affected_rows() {
        return mysql_affected_rows($this->conn);
    }

    /**
     * 查询字段数量和字段信息
     *
     * @access public
     * @parameter string $table  表名
     * @return void
     */
    public function num_fields($table) {
        $this->query("select * from $table");
        echo "<br />";
        //打印字段数
        echo "字段数：" . $total = mysql_num_fields($this->result); //打印字段数
        echo "<pre>";
        //mysql_fetch_field() 函数从结果集中取得列信息并作为对象返回。
        for ($i = 0; $i < $total; $i++) {
            print_r(mysql_fetch_field($this->result, $i));
        }
        echo "</pre>";
        echo "<br />";
    }

    /**
     * 从结果集中取得列信息并作为对象返回 
     * 
     * @param object $query 
     * @return object 
     */
    function fetch_fields($query) {
        return mysql_fetch_field($query);
    }

    /**
     * 返回查询结果 
     * 
     * @param object $query 
     * @param string $row 
     * @return mixed 
     */
    function result($query, $row) {
        $query = @mysql_result($query, $row);
        return $query;
    }

    //==========================================================================================================================//
    //================================下面这些函数你们一般不会用到的，所以不用细看了============================================//
    //==========================================================================================================================//
    /**
     * 输出sql语句错误信息(这个函数只是我在类内部用的，你们不用理)
     *
     * @access public
     * @parameter string $message 提示信息
     * @return void
     */
    public function show_error($message='', $sql='') {
        echo "<fieldset>";
        echo "<legend>错误信息提示:</legend><br />";
        echo "<div style='font-size:14px; clear:both; font-family:verdana, arial, helvetica, sans-serif;'>";
        //打印错误原因
        echo "错误原因：" . mysql_error() . "<br /><br />";
        //打印错误信息
        //mysql_error() 函数返回上一个 mysql 操作产生的文本错误信息。
        echo "<div style='height:20px; background:#ff0000; border:1px #ff0000 solid'>";
        echo "<font color='white'>" . $message . "</font>";
        echo "</div>";
        //打印错误sql语句
        echo "<font color='red'><pre>" . $sql . "</pre></font>";
        echo "</div>";
        echo "</fieldset>";
    }

    /**
     * 选择数据库 
     * 
     * @param string $dbname 
     * @return 
     */
    function select_db($dbname) {
        return mysql_select_db($dbname, $this->conn);
    }

    /**
     * 释放结果集 
     * 
     * @param object $query 
     * @return bool 
     */
    function free_result($query) {
        return mysql_free_result($query);
    }

    /**
     * 返回mysql版本 
     * 
     * @return string 
     */
    function version() {
        return mysql_get_server_info($this->conn);
    }

    /**
     * 关闭非持久的连接 
     * 
     * @return bool 
     */
    function close() {
        return mysql_close($this->conn);
    }

    /**
     * 获得客户端真实的IP地址 
     * 
     * @return ip地址 
     */
    function getip() {
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) {
            $ip = getenv("HTTP_CLIENT_IP");
        } else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) {
            $ip = getenv("REMOTE_ADDR");
        } else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = "unknown";
        }
        return($ip);
    }

    /**
     * 析构函数，自动关闭数据库 
     * 
     * @return  
     */
    public function __destruct() {
        
        mysql_close($this->conn) or die("关闭失败");
    }

}

?>
