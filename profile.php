<?php
/* =========================================连接数据库================================================= */
include_once 'sql/get_information.php';

/* =========================================获取user的基本信息================================================= */
session_start();
$new_tweet_form = false;
if (!isset($_GET['user_id'])) {
    $user_id = $_SESSION['userid'];
    $new_tweet_form = true;
}
else
    $user_id = $_GET['user_id'];

$currentUser = get_user($user_id);

/* =========================================获取user的兴趣爱好================================================= */
$tags = get_tags($user_id);

$userTag = "";
for ($i = 0; $i < count($tags); $i++) {
    $userTag .= $tags[$i]['user_tag'] . ";";
}

/* =========================================获取用户年龄================================================= */
$userAge = get_age($user_id);
?>
<div data-role="page" data-theme="b" data-id="header">
    <div data-role="header" data-theme="b" data-id="header">
        <a data-role="button" data-icon="" href="index.html" data-ajax="false">返回</a>
        <h2><?php echo $currentUser['nick_name'] . "的主页"; ?></h2>
        <!--通过地址栏传递参数  2011-7-7 首先判断是否关注该用户，如果没有则显示为关注按钮，如果有则显示为留言按钮-->
        <?php
        include_once 'sql/login_sql.php';
        if (!is_following($_SESSION['userid'], $_GET['user_id'])) {
            //还没有关注
            echo '<a data-role="button" data-icon="gear" id="chatnull" class="ui-btn-right" 
                   href="add_following.php?userid=' . $_SESSION['userid'] . '&followerid=' . $_GET['user_id'] . '">关注</a>
                       ';
        } else {
            //已经关注
            echo '<a data-role="button" data-icon="gear" id="chatnull" class="ui-btn-right" 
                   href="chat.php?currentuserid=' . $_SESSION['userid'] . '&userid=' . $_GET['user_id'] . '">留言</a>
                <script>
                chat();
                </script>';
        }
        ?>
    </div>
    <div data-role="content" class="l-inset-content">
        <ul class="l-inset l-detail l-profile-view " data-role="listview" data-inset="true">
            <?php
            if ($new_tweet_form) {
                ?>
                <li>
                    <form id="tweet_form">
                        <textarea id="new_tweet" class="ui-input-text ui-body-null ui-corner-all ui-shadow-inset ui-body-c" name="introduction" rows="8" cols="40">告诉朋友们你在做什么...</textarea>
                        <div>
                            <a id="submit_tweet" class="l-displaynone l-button-right ui-btn ui-btn-up-c ui-btn-inline ui-btn-corner-all ui-shadow" data-inline="true" href="" data-role="button" data-theme="c">
                                <span class="ui-btn-inner ui-btn-corner-all">
                                    <span class="ui-btn-text">提交</span>
                                </span>
                            </a>
                        </div>
                    </form>
                </li>

                <?php
            }
            ?>
            <li class="l-photo-contain">
                <div class="l-photo l-shadowbox" style="background-image: url(<?php $URL = $currentUser['image'];
            echo ($URL == null ? './images/avatar.png' : small_to_large($URL)); ?>)">    
                </div>
            </li>
            <li><label>昵称：</label><label><?php echo $currentUser['nick_name']; ?></label></li>
            <li><label>位置：</label><label class="l-describe"><?php echo $currentUser['address']; ?></label></li>
            <li><label>爱好：</label><label class="l-describe"><?php echo $userTag; ?></label></li>
            <li><label>性别：</label><label><?php echo $currentUser['gender']; ?></label></li>
            <li><label>年龄：</label><label><?php echo $userAge; ?></label></li>
            <li><label>身高：</label><label><?php echo $currentUser['height'] . "cm"; ?></label></li>
            <li>
                <label>简介：</label>
                <label class="l-describe"><?php echo $currentUser['introduction']; ?></label>
            </li>
        </ul>
        <input type="hidden" name="userid" id="userid" value="<?php echo $currentUser['user_id']; ?>"/>
        <input type="hidden" name="nickname" id="nickname" value="<?php echo $currentUser['nick_name']; ?>"/>
    </div>


    <div data-role="footer" data-theme="b" data-id="footer">
        <h2 >Team Luff</h2>
    </div>
</div>

