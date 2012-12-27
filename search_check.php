<?php

require_once 'sql/login_sql.php';
require_once 'sql/get_information.php';
require_once 'sql/search_recommend.php';

session_start();
$user_id = $_SESSION['userid'];
$gender = trim($_POST['gender']);
$distance = trim($_POST['distance']);
//获得当前Tag表
$current_tags = array();
for ($i = 0; $i < 10; $i++) {
    if (trim($_POST['tag' . $i]) != "") {
        $current_tags[] = trim($_POST['tag' . $i]);
    }
}

$age = array();
$age[0] = trim($_POST['select-choice-1']);
$age[1] = trim($_POST['select-choice-2']);
$search = search_users($user_id, $current_tags, $gender, $age, 1, 5, $distance);

for ($i = 0; $i < count($search); $i++) {

    echo '<li class="ui-li-has-thumb l-list-item">';
    echo '<div class="ui-li-thumb l-avatar" style="background-image:url(' . ($search[$i]['image'] == null ? './images/avatar.png' : $search[$i]['image']) . ')">';
    echo '<div class="l-status">';
    echo '<img src="' . (is_online($search[$i]['user_id']) == true ? './images/online.png' : './images/offline.png') . '"/>';
    //   echo '<p>' . get_dis_info($recommend[$i]['distance']) . '';
    echo '</div>';
    echo '</div>';
    echo '<a href="profile.php?user_id=' . $search[$i]['user_id'] . '" data-transition="slide">' . $search[$i]['nick_name'];
    //Todo 显示location

    echo '<p>推荐理由: ' . $search[$i]['user_tag'] . '</p>';
    echo '</a>';
    echo '</li>';
}
?>
