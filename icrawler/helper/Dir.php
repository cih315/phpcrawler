<?php 

/**
 * 检测目录是否存在，如不存在则创建
 *
 * @param  string $path 目录 
 * @param  int     $mode  目录权限
 * @return boolean
 **/ 

if(!function_exists('check_path')){
    function check_path($path, $mode = 0755){
        if(!is_dir($path)){
            check_path(dirname($path), $mode);
        }
        return @mkdir($path, $mode);
    }
}

function my_mysql_query($sql){
    $db = init_db();
    $result = mysql_query($sql, $db);
    $data = array();
    if($result){
        while($row = mysql_fetch_assoc($result)){
            $data[]  = $row;
        }
    }
    return $data;
}

function my_mysql_insert($sql){
    $db = init_db();
    $result = mysql_query($sql, $db);
    return mysql_insert_id($db);
}

function init_db($config = array()){
    $config = empty($config) ? Loader::load_config('db') : $config;
    if(empty($config)){
        exit('db config is empty');
    }
    static $_db = null;
    if($_db){
        return $_db;
    }
    ($_db = mysql_connect($config['host'], $config['port'])) or die('Could not connect to mysql server.');
    mysql_select_db($config['dbname'], $_db) or die('Could not select database.');
    mysql_query("SET NAMES utf8", $_db);
    return $_db;
}
