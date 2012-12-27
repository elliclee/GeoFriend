<?php
/* =========================================连接数据库================================================= */
include_once 'sql/get_information.php';
include_once 'sql/login_sql.php';

/* =========================================获取user的基本信息================================================= */
session_start();
if (!isset($_GET['user_id']))
    $user_id = $_SESSION['userid'];
else
    $user_id = $_GET['user_id'];
$currentUser = get_user($user_id);

/* =========================================获取user的状态================================================= */
/* 首先判断状态的条数，如果小于5条则全部显示，如果大小5条则只显示最新的5条，需要提供一个查看全部状态的功能按钮 */
$tweet_count = get_tweet_count($user_id);
if ($tweet_count <= 5) {
    $tweet = get_tweets($user_id, 1, $tweet_count);
} else {
    $tweet = get_tweets($user_id);
}
?>
<!DOCTYPE html> 
<html> 
    <head> 
        <title></title> 
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta content="yes" name="apple-mobile-web-app-capable"/>
        <link rel="stylesheet" href="jquery/jquery.mobile-1.0b1.min.css" />
        <link rel="stylesheet" href="css/index.css"/>
        <link rel="stylesheet" href="css/status.css"/>
        <script src="jquery/jquery-1.6.1.min.js" type="text/javascript"></script>
        <script src="jquery/jquery.mobile-1.0b1.min.js" type="text/javascript"></script>

    </head> 
    <body> 

        <div data-role="page" data-theme="b" data-id="header" data-add-back-btn="true">
            <script src="js/status.js"></script>

            <div data-role="header" data-theme="b" data-id="header">
                <a data-role="button" data-icon="" data-rel="back" src="">返回</a>
                <h2><?php echo $currentUser['nick_name']; ?>的状态</h2>
                <!--通过地址栏传递参数-->
                <a data-role="button" data-icon="gear" id="chatnull" class="ui-btn-right" 
                   href="chat.php?currentuserid=<?php echo $_SESSION['userid'] ?>&userid=<?php echo $_GET['user_id'] ?>">聊天</a>
            </div>
            <div data-role="content" id="content" data-scroll="true">

                <ul class="l-inset" data-role="listview" data-inset="true">
                    <li class="l-intro">
                        <div>
                            <a href="profile.php?user_id=<?php echo $_GET['user_id'] ?>">
                                <div class="l-avatar l-frame" style="background-image: url(<?php echo ($currentUser['image'] == null ? './images/avatar.png' : $currentUser['image']); ?>)"></div>
                            </a>    
                        </div>


                        <!--Todo 补全超链接地址-->
                        <h3><a href="profile.php?user_id=<?php echo $_GET['user_id'] ?>"><?php echo $currentUser['nick_name']; ?></a></h3>
                        <p><?php echo $currentUser['address']; ?></p>
                    </li>
                    <!--以下显示状态-->
                    <?php
                    for ($i = 0; $i < count($tweet); $i++) {
                        $userTweetTime = $tweet[$i]['tweet_time'];
                        $userTweetContent = $tweet[$i]['tweet_content'];

                        //需要根据tweet_id来获取评论
                        $userTweetId = $tweet[$i]['tweet_id'];

                        echo ' <li class="l-tweet">
                        <div class="l-tweet-content">
                            <p><strong>' . $userTweetTime . '</strong></p>
                            <p>' . $userTweetContent . '</p>
                
                            <div class="l-tweet-button l-button-show">
                                <a data-role="button" href=""data-inline="true" class="l-button ">评论</a>
                            </div>
                        </div>
                        
                        <div class="l-tweet-comment l-invalid l-displaynone">
                            <div id="comment_list">';
                        $comments = get_comment($userTweetId);
                        for ($j = 0; $j < count($comments); $j++) {
                            //评论者头像与昵称
                            $commentuser = get_user($comments[$j]['c_user_id']);
                            echo '<div class="l-tweet-content l-tweet-reply">
                                    <a href="profile.php?id=">
                                        <div class="l-thumbnail l-frame" style="background-image: url(' . $commentuser['image'] . ')"></div>
                                    </a>
                                    <div class="l-tweet-reply-content">
                                        <p><strong><a href="profile.php?id=">' . $commentuser['nick_name'] . '</a> ' . $comments[$j]['comment_datetime'] . '</strong></p>
                                        <p>' . $comments[$j]['content'] . '</p> 
                                    </div>
                                </div>';
                        }
                        echo '
                            </div>

                            <form id="comment_form">
                                <textarea cols="40" rows="8" name="comment" class="textarea">说点什么吧</textarea>
                                                                <input type="hidden" class="tweetid" name="tweetid"  value="' . $userTweetId . '"/>

                                <div class="l-tweet-button  l-button-add">
                                <a data-role="button" href="" data-inline="true" class="l-button">提交</a>
                            </div>
                            </form>
                            
                        </div>
                    </li>';
                    }
                    ?>

                </ul>
                <!--bcuserid: 被评论用户id-->
                <input type="hidden" name="tweetid" id="tweetid" value="<?php echo '' ?>"/>
                <input type="hidden" name="cuserid" id="cuserid" value="<?php echo $_SESSION['userid']; ?>"/>
                <input type="hidden" name="bcuserid" id="bcuserid" value="<?php echo $_GET['user_id']; ?>"/>
            </div>

            <div data-role="footer" data-theme="b" data-id="footer">
                <h2 >Team Luff</h2>
            </div>
        </div>
    </body>
</html>

