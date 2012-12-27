<?php

if (!empty($_SERVER['SCRIPT_FILENAME']) && 'tweet_update.php' == basename($_SERVER['SCRIPT_FILENAME']))
    if (!isset($_POST['content']))
        die('You can not access this page directly!');

require_once 'sql/login_sql.php';

session_start();
$user_id = $_SESSION['userid'];
$content = trim($_POST['content']);

update_tweet($user_id, $content);
?>
