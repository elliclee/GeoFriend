<?php

if (!empty($_SERVER['SCRIPT_FILENAME']) && 'add_following.php' == basename($_SERVER['SCRIPT_FILENAME']))
    if (!isset($_GET['userid']))
        die('You can not access this page directly!');

require_once 'sql/login_sql.php';
$user_id = $_GET['userid'];
$follower_id = $_GET['followerid'];
add_follow($user_id, $follower_id);
?>
