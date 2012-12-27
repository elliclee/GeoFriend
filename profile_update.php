<?php

/* 李本卿  2011-6-15
 * 换取profile-edit页面POST过来的值，并插入数据库，返回成功或者失败
 * 数据库user表应该增加一个字段，保存用户的位置  
 * Tag的数量判断，存入数据库时要注意的地方
 */
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'profile_update.php' == basename($_SERVER['SCRIPT_FILENAME']))
    if (!isset($_POST['nick_name']))
        die('You can not access this page directly!');

require_once 'sql/login_sql.php';
require_once 'sql/get_information.php';

session_start();
$user_id = $_SESSION['userid'];
$nick_name = trim($_POST['nick_name']);
$longitude = trim($_POST['longitude']);
$latitude = trim($_POST['latitude']);
$address = trim($_POST['address']);
$gender = trim($_POST['gender']);
$age = trim($_POST['age']);
$height = trim($_POST['height']);
$introduction = trim($_POST['introduction']);

$ex_tags = get_tags($user_id);

//获得当前Tag表
$current_tags = array();

for ($i = 0; $i < 10; $i++) {
    if (trim($_POST['tag' . $i]) != "") {
        $current_tags[] = trim($_POST['tag' . $i]);
    }
}
//先更新个人信息，Tag再处理
update_profile($user_id, $nick_name, $longitude, $latitude, $address, $gender, $age, $height, $introduction);

//更新Tag
update_tags($user_id, $ex_tags, $current_tags);
?>
