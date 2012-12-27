<?php
//获取周遭好友
// get_following用法：查找在指定距离内的被$user_id关注的人

include_once 'sql/get_information.php';
include_once 'sql/search_recommend.php';
include_once 'sql/login_sql.php';
session_start();
$user_id = $_SESSION['userid'];

if (isset($_GET['slide_seq']))
    $slide_seq = $_GET['slide_seq'];
else
    $slide_seq = 1;

$recommend = recommend_users($user_id, $slide_seq, 5);

// grid类型：user_id, nick_name, image, online, gender, latitude, longitude
// list类型：user_id, nick_name, image, online, gender, introduction, latitude, longitude

if ($slide_seq == 1) {
    $slide_count = count_recommend_slide($user_id, 5);
    echo '<li class="l-displaynone" data-slide-count="' . $slide_count . '"></li>';
}


for ($i = 0; $i < count($recommend); $i++) {   // 用法
    if (!empty($recommend[$i])) {
        echo '<li class="ui-li-has-thumb l-list-item">';
        echo '<div class="ui-li-thumb l-avatar" data-img="' . ($recommend[$i]['image'] == null ? './images/avatar.png' : $recommend[$i]['image']) . '">';
        echo '<div class="l-status">';
        echo '<img src="' . (is_online($recommend[$i]['user_id']) == true ? './images/online.png' : './images/offline.png') . '"/>';
        echo '<p>' . get_dis_info($recommend[$i]['distance']) . '';
        echo '</div>';
        echo '</div>';
        echo '<a href="profile.php?user_id=' . $recommend[$i]['user_id'] . '" data-transition="slide">' . $recommend[$i]['nick_name'];
        //Todo 显示location

        echo '<p>推荐理由: ' . $recommend[$i]['user_tag'] . '</p>';
        echo '</a>';
        echo '</li>';
    }
}
?>

