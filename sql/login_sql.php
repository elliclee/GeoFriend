<?php
require_once 'sql.php';
//2011-7-3  登录功能

function find_user($useremail, $pwd) {
    $sql = new sql();
    $findUser = "select * from user where email='$useremail' and password='$pwd'";
    return $sql->fetch_array2($findUser);
}

function update_user($useremail, $ip) {
    $sql = new sql();
   // $loginTime = Date("Y-m-d h:m:s");
    $update = "UPDATE user SET logon_time=now(),ip='$ip',online='1' where email='$useremail'";
    return $sql->query($update);
}

//======================================== 更新用户信息=================================================//
function update_position($user_id, $latitude, $longitude,$address) {
    $sql = new sql();
    $update = "UPDATE user SET position = PointFromText('POINT(" . $latitude . " " . $longitude . " )'),address='$address' 
              WHERE user_id = " . $user_id;
    $sql->query($update);
}

//===============================================注册功能===============================================//
function register($email, $password, $latitude, $longitude,$address, $register_datetime, $logon_time, $login_ip) {
    $sql = new sql();
    $register = "INSERT INTO user (email,password,address,register_datetime,logon_time,ip ) Values ('$email','$password','$address','$register_datetime','$logon_time','$login_ip')";
    $sql->query($register);
    $update = "UPDATE user SET position = PointFromText('POINT(" . $latitude . " " . $longitude . " )')
              WHERE email = '" . $email . "'";
    $sql->query($update);
}

//===========================================根据邮箱和密码获取user_id======================================//
function get_id($email, $password) {
    $sql = new sql();
    $findid = "select user_id from user where email='$email' and password='$password'";
    $rs = $sql->fetch_array2($findid);
    return $rs['user_id'];
}
//===========================================根据user_id获取用户头像地址=============6-23，李本卿================//
function get_img($user_id) {
    $sql = new sql();
    $select = "select image from user where user_id = " . $user_id;
    $rs = $sql->fetch_array2($select);
    return $rs['image'];
}
//===========================================根据user_id获取用户状态=============7-4，李本卿================//
function get_tweet_count($user_id){
    $sql=new sql();
    $select = "select * from tweet where user_id = " . $user_id;
    $result = $sql->fetch_array2($select);
    return count($result);
}

//根据tweet_id来获取相关评论，也就是comment表的内容，需要把评论者id，被评论者id都返回
function get_comment($tweet_id) {
    $sql = new sql();
    $select = "select * from comment where tweet_id = " . $tweet_id;
    return $sql->fetch_array3($select);
}

//评论状态函数
function insert_comment($tweet_id,$c_userid,$b_c_userid,$content){
    $sql=new sql();
    $insert="INSERT INTO comment (tweet_id,c_user_id ,bc_user_id,comment_datetime,content)
        VALUES ($tweet_id,$c_userid,$b_c_userid,now(),'$content')";
    return $sql->query($insert);
       
}

// 插入聊天内容
function insert_message($current_userid, $userid, $message) {
    $sql=new sql();
    $insert_message='INSERT INTO message VALUES ( null,"' . $current_userid . '",' . $userid . ', "' . $message . '")';
    $sql->query($insert_message);

    $select_message='SELECT * FROM message where (userid1="' . $current_userid . '" 
        and userid2="' . $userid . '") or ( userid1="' . $userid . '" and userid2="' . $current_userid . '")';
    return $sql->fetch_array3($select_message);
}
//获取聊天内容
function select_message($current_userid, $userid){
    $sql=new sql();
     $select_message='SELECT * FROM message where (userid1="' . $current_userid . '" 
        and userid2="' . $userid . '") or ( userid1="' . $userid . '" and userid2="' . $current_userid . '")';
    return $sql->fetch_array3($select_message);
}
//更新个人信息函数
function update_profile($user_id,$nick_name,$longitude,$latitude,$address,$gender,$age,$height,$introduction){
    $sql = new sql();
    $update = "UPDATE user SET nick_name='$nick_name',gender='$gender',introduction='$introduction',
    address='$address',height='$height',
    position = PointFromText('POINT(" . $latitude . " " . $longitude . " )'),address='$address' 
              WHERE user_id = " . $user_id;
    $sql->query($update);
}

//======================================下面是Mooner添加的（2011-07-06）===========================================//
// 保存图片地址
function save_img_url($user_id, $img_url) {
    $sql = new sql();
    // 更改和新增图片都可以用update
    $update = "UPDATE user SET image = '" . $img_url . "' WHERE user_id = " . $user_id;
    $sql->query($update);
}

// 因为存入数据库的地址是小图片的。这个函数就是把小图片地址转换为大图片地址

//2011-7-6  判断tag存在不存在
//==================================对兴趣爱好的处理==========================================//
//2011-7-6  判断tag存在不存在
function check_tag($user_id, $content){
    $select = "SELECT * FROM tag WHERE user_tag = '$content '";
    $sql = new sql();
    $result = $sql->fetch_array2($select);
    if (empty($result)) {
        //插入Tag，再建立关联
        $insert_tag = "INSERT INTO tag(user_tag) VALUES('" . $content . "')";
        $sql->query($insert_tag);
        $select_new = "SELECT tag_id FROM tag WHERE user_tag = '$content '";
        $new_id = $sql->fetch_array2($select_new);
        $insert_relation = "INSERT INTO user_to_tag(user_id,tag_id) VALUES(" . $user_id . "," . $new_id['tag_id'] . ")";
        $sql->query($insert_relation);
        
        return "刚插入的tag的id值：" . $new_id['tag_id'];
    }
    else {
        //判断是否关联，如果已关联，返回一个字符串; 如果没有关联，则建立关联
        $select_relation = "SELECT * FROM user_to_tag WHERE user_id = " . $user_id;
        $relation = $sql->fetch_array3($select_relation);
        for ($i = 0; $i < count($relation); $i++) {
             if ($relation[$i]['tag_id'] == $result['tag_id'])  // 如果已经建立连接
                 return $result['tag_id'];
        }
        // 如果没有建立连接，则建立新的连接
        $insert_new_relation = "INSERT INTO user_to_tag(user_id,tag_id) VALUES(" . $user_id . ",'" . $result['tag_id'] . "')";
        $sql->query($insert_new_relation);
        
        return $result['tag_id'];
    }
}

// 给update_tags用的，本卿可以不理它
function check_tag2($user_id, $content, $sql){
    $select = "SELECT * FROM tag WHERE user_tag = '$content '";
    $result = $sql->fetch_array2($select);
    if (empty($result)) {
        //插入Tag，再建立关联
        $insert_tag = "INSERT INTO tag(user_tag) VALUES('" . $content . "')";
        $sql->query($insert_tag);
        $select_new = "SELECT tag_id FROM tag WHERE user_tag = '$content '";
        $new_id = $sql->fetch_array2($select_new);
        $insert_relation = "INSERT INTO user_to_tag(user_id,tag_id) VALUES(" . $user_id . "," . $new_id['tag_id'] . ")";
        $sql->query($insert_relation);
        
        return "刚插入的tag的id值：" . $new_id['tag_id'];
    }
    else {
        //判断是否关联，如果已关联，返回一个字符串; 如果没有关联，则建立关联
        $select_relation = "SELECT * FROM user_to_tag WHERE user_id = " . $user_id;
        $relation = $sql->fetch_array3($select_relation);
        for ($i = 0; $i < count($relation); $i++) {
             if ($relation[$i]['tag_id'] == $result['tag_id'])  // 如果已经建立连接
                 return $result['tag_id'];
        }
        // 如果没有建立连接，则建立新的连接
        $insert_new_relation = "INSERT INTO user_to_tag(user_id,tag_id) VALUES(" . $user_id . ",'" . $result['tag_id'] . "')";
        $sql->query($insert_new_relation);
        
        return $result['tag_id'];
    }
}

/**
 * 把用户不要的Tag的关联关系删除。插入到Tag表中没有的Tag，并建立关系
 * @param $ex_tags        get_tags函数返回的值，用户修改兴趣前的兴趣
 * @param $current_tags   字符串数组，用户修改后的兴趣
 */
function update_tags($user_id, $ex_tags, $current_tags){
    $sql = new sql();
    $count_ex = count($ex_tags);
    $count_cu = count($current_tags);
    
    $judge_ex = array();
    for ($i = 0; $i < $count_ex; $i++) {
        $judge_ex[$ex_tags[$i]['user_tag']] = 1; // 如果遍历后某一项=0则说明这一项tag不再被用户感兴趣，需要删除关联
    }
    $judge_cu = array();
    for ($j = 0; $j < $count_cu; $j++) {
        $judge_cu[$current_tags[$j]] = 0;        // 如果遍历后某一项=1则说明这一项tag是原有的兴趣
    }
    
    for ($i = 0; $i < $count_ex; $i++) {
        for ($j = 0; $j < $count_cu; $j++) {
            // 不匹配，则说明这个tag是新增的
            if ($ex_tags[$i]['user_tag'] != $current_tags[$j] && 0 != $judge_ex[$ex_tags[$i]['user_tag']]) { 
                if ($j == $count_cu - 1) {
                    $judge_ex[$ex_tags[$i]['user_tag']] = 0;
                }
            }
            // 匹配，则说明没有改变
            else { 
                $judge_cu[$current_tags[$j]] = 1;
                $j = $count_cu;
            }
        }
    }
    
    // 先把那些用户原有但现在没有了的兴趣关联删掉
    for ($i = 0; $i < $count_ex; $i++) {
        if (0 == $judge_ex[$ex_tags[$i]['user_tag']]) { // 如果遍历后某一项=0则说明这一项tag不再被用户感兴趣，需要删除关联
            //echo $ex_tags[$i]['user_tag']."+";
            $delete = "DELETE FROM user_to_tag WHERE user_id = $user_id AND tag_id = " . $ex_tags[$i]['tag_id'];
            $sql->query($delete);
        }
    }
     
    // 再把那些用户新增的兴趣加进来
    for ($i = 0; $i < $count_cu; $i++) {
        if (0 == $judge_cu[$current_tags[$i]]) { // 如果遍历后某一项=1则说明这一项tag是原有的兴趣
            check_tag2($user_id, $current_tags[$i], $sql);
        }
    }
}

//=============================================================================//
// 判断userid1是否已经关注了userid2，就是那个following表里是否有关系了
function is_following($user_id1,$user_id2) {
    $select = "SELECT * FROM follower  WHERE user_id = $user_id1 AND follower_id = $user_id2";
    $sql = new sql();
    $select_result = $sql->fetch_array2($select);

    if (empty($select_result))
        return false;
    else
        return true;
}

//userid1关注了userid2的用户
function add_follow($user_id,$follower_id){
    $insert='INSERT INTO follower VALUES ( null,' .$user_id. ',' . $follower_id .')';
    $sql=new sql();
    $sql->query($insert);
}

//2011-7-13 判断用户是否在线
function is_online($user_id){
    $is_online="SELECT * FROM `tb_onlineuser` where N_OnlineUserId='$user_id'";
    $sql= new sql();
    if(count ($sql->fetch_array3($is_online))==0)
            return  false;
    else 
        return true;  
}
//更新个人状态
function update_tweet($user_id,$content){
    $update_tweet="INSERT INTO tweet(user_id,release_datetime,content) VALUES('$user_id' , now() ,'$content')";
    $sql=new sql();
    $sql->query($update_tweet);
    
}
?>
