<?php
/* 李本卿 2011-6-14 获取POST过来的数据存入数据库
 */
header("Content-Type: text/html; charset=utf-8");
require_once '../sql/get_information.php';
require_once '../sql/login_sql.php';
session_start();
$current_userid = $_SESSION['userid'];
$userid = trim($_POST['userid']);
$message = $_POST['message'];

//2011-7-6  统一数据库连接方式
insert_message($current_userid, $userid, $message);
$res = select_message($current_userid, $userid);

for ($i = 0; $i < count($res); $i++) {
    if ($res[$i]['userid1'] == $current_userid) {
        ?> <li class="clear even">
            <img class="avatar" src="<?php echo get_img($current_userid) ?>"/>
            <div class="comment-content">
                <?php echo($res[$i]['message']) ?></div></li>
    <?php } else { ?>
        <li class="clear odd">
            <img class="avatar" src="<?php echo get_img($userid) ?>"/>
            <div class="comment-content">
                <?php echo($res[$i]['message']) ?> </div></li>
                <?php
            }
        }
        ?>

