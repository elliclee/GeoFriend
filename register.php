<?php

if (!empty($_SERVER['SCRIPT_FILENAME']) && 'register.php' == basename($_SERVER['SCRIPT_FILENAME']))
    if (!isset($_POST['email_input']))
        die('You can not access this page directly!');

require_once 'sql/get_information.php';
require_once 'sql/login_sql.php';
//取得POST过来的数据
$useremail = trim($_POST['email_input']);
//应该需要加密的
$pwd = trim($_POST['password_input']);
$longitude = trim($_POST['longitude_register']);
$latitude = trim($_POST['latitude_register']);
$ip = $_SERVER['REMOTE_ADDR']; //获取客户端的IP
$address = get_address_by_location($latitude, $longitude);
register($useremail, $pwd, $latitude, $longitude, $address, date("Y-m-d H-i-s"), date("Y-m-d H-i-s"), $ip);
//取得id
session_start();
$_SESSION['userid'] = get_id($useremail, $pwd);
$_SESSION['useremail'] = $useremail;
$_SESSION['longitude'] = $longitude;
$_SESSION['latitude'] = $latitude;
header("location:index.html");
?>
