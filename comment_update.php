<?php

/* 李本卿  2011-7-4
 * 换取status.php页面POST过来的值，并插入数据库，返回成功或者失败 
 * Tag的数量判断，存入数据库时要注意的地方
 */
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comment_update.php' == basename($_SERVER['SCRIPT_FILENAME']))
    if (!isset($_POST['content']))
        die('You can not access this page directly!');

require_once 'sql/login_sql.php';
$tweet_id = trim($_POST['tweet_id']);
$c_userid = trim($_POST['c_userid']);
$b_c_userid = trim($_POST['b_c_userid']);
$content = trim($_POST['content']);

insert_comment($tweet_id, $c_userid, $b_c_userid, $content);

echo 'success';
?>
