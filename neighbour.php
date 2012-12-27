<?php

//2011-6-23 修改userid的传递
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'neighbour.php' == basename($_SERVER['SCRIPT_FILENAME']))
    if (!isset($_GET['slide_seq']))
        die('You can not access this page directly!');

include_once 'sql/get_information.php';
include_once 'sql/login_sql.php';
session_start();
$user_id = $_SESSION['userid'];

$slide_count = count_neighbours_slide($user_id);

if (isset($_GET['slide_seq']))
    $slide_seq = $_GET['slide_seq'];
else
    $slide_seq = 1;

$users = get_neighbours($user_id, $slide_seq); // 默认grid类型 
// grid类型：user_id, nick_name, image, online, gender, latitude, longitude
// list类型：user_id, nick_name, image, online, gender, introduction, latitude, longitude

if ($slide_seq == 1)
    $slide_count_attr = 'data-slide-count = "' . $slide_count . '"';
else
    $slide_count_attr = "";

//Display content
if ($slide_count == 0)
    echo "很抱歉，我们在您周围连半个邻居都找不到。";
else {

//Show empty message
    echo '<div class="l-slide" data-slide-seq = "' . $slide_seq . '" ' . $slide_count_attr . ' >';

    echo '<div class="ui-grid-c l-grid">';

    for ($i = 0; $i < count($users); $i++) {   // 用法 
        if (!empty($users[$i]['user_id'])) {
            $box_seq = "";
            switch ($i % 4) {
                case 0:$box_seq = 'a';
                    break;
                case 1:$box_seq = 'b';
                    break;
                case 2:$box_seq = 'c';
                    break;
                case 3:$box_seq = 'd';
            }

            echo '<div class="ui-block-' . $box_seq . '">';
            echo '<div class="l-people">';
            echo '<a href="profile.php?user_id=' . $users[$i]['user_id'] . '" data-transition="slide">';
            echo '<div class="l-avatar l-shadowbox" style="background-image:url(' . ($users[$i]['image'] == null ? './images/avatar.png' : $users[$i]['image']) . ')">';
            echo '<div class="l-status">';
            echo '<img src="' . (is_online($users[$i]['user_id']) == true ? './images/online.png' : './images/offline.png') . '"/>';
            echo '<p>' . get_dis_info($users[$i]['distance']) . '</p>';
            echo '</div>';
            echo '<h4>' . $users[$i]['nick_name'] . '</h4>';
            echo '</div>';
            echo '</a>';
            echo '</div>';
            echo '</div>';
        }
    }

    echo '</div>';

    echo '</div>';
    echo '<div class="l-dot-line ui-grid-b">';

    echo '<div class="ui-block-a l-block-center tc"></a>';
    if ($slide_seq != 1) {
        echo '<a id="prev_button" class="ui-btn ui-btn-inline ui-btn-icon-notext ui-btn-corner-all ui-shadow ui-btn-up-c" data-iconpos="notext" data-icon="arrow-r" data-inline="true" href="" data-role="button" title="上一页" data-theme="c">';
        echo '<span class="ui-btn-inner ui-btn-corner-all">';
        echo '<span class="ui-btn-text">上一页</span>';
        echo '<span class="ui-icon ui-icon-arrow-l ui-icon-shadow"></span>';
        echo '</span>';
        echo '</a>';
    }
    echo'</div>';

    echo '<div class="ui-block-b">';
    for ($i = 1; $i <= $slide_count; $i++) {
        if ($i == $slide_seq)
            echo '<img src="images/dotthis.png"/>';
        else
            echo '<img src="images/dotthat.png"/>';
    }
    echo'</div>';

    echo '<div class="ui-block-c l-block-center tc">';
    if ($slide_seq != $slide_count) {
        echo '<a id="next_button" class="ui-btn ui-btn-inline ui-btn-icon-notext ui-btn-corner-all ui-shadow ui-btn-up-c" data-iconpos="notext" data-icon="arrow-r" data-inline="true" href="" data-role="button" title="下一页" data-theme="c">';
        echo '<span class="ui-btn-inner ui-btn-corner-all">';
        echo '<span class="ui-btn-text">下一页</span>';
        echo '<span class="ui-icon ui-icon-arrow-r ui-icon-shadow"></span>';
        echo '</span>';
    }
    echo '</a>';

    echo'</div>';

    echo '</div>';

    echo '</div>';
}
?>

