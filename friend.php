<?php
//获取周遭好友
// get_following用法：查找在指定距离内的被$user_id关注的人
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'friend.php' == basename($_SERVER['SCRIPT_FILENAME']))
    if (!isset($_GET['slide_seq']))
        die('You can not access this page directly!');

include_once 'sql/get_information.php';
include_once 'sql/login_sql.php';

session_start();
$user_id = $_SESSION['userid'];

if (isset($_GET['slide_seq']))
    $slide_seq = $_GET['slide_seq'];
else
    $slide_seq = 1;

$friends = get_followings($user_id, $slide_seq);

// grid类型：user_id, nick_name, image, online, gender, latitude, longitude
// list类型：user_id, nick_name, image, online, gender, introduction, latitude, longitude

if ($slide_seq == 1) {
    $slide_count = count_following_slide($user_id);
    ?>
    <li class="l-displaynone" data-slide-count="<?php echo $slide_count ?>"></li>
    <?php
}


for ($i = 0; $i < count($friends); $i++) {   // 用法
    echo '<li class="ui-li-has-thumb l-list-item">';
    echo '<div class="ui-li-thumb l-avatar" data-img="' . ($friends[$i]['image'] == null ? './images/avatar.png' : $friends[$i]['image']) . '">';
    echo '<div class="l-status">';
    echo '<img src="' . (is_online($friends[$i]['user_id']) == true ? './images/online.png' : './images/offline.png') . '"/>';
    echo '<p>' . get_dis_info($friends[$i]['distance']) . '';
    echo '</div>';
    echo '</div>';
    echo '<a href="status.php?user_id=' . $friends[$i]['user_id'] . '" data-transition="slide">' . $friends[$i]['nick_name'];
    echo '<p>' . $friends[$i]['tweet_content'] . '</p>';
    echo '<p>' . $friends[$i]['tweet_time'] . '</p>';
    echo '</a>';
    echo '</li>';
}
?>

