<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

require_once 'get_information.php';

// 根据当前用户输入的条件进行搜索
function search_users($user_id, $tags, $gender, $age, $m_th = 1, $num = 5, $distance = 50000) {
    $sql = new sql(); 
    $recommand = array();
    $tag_to_uid = array();
    $uid_to_tag = array();     
    //=============================搜索有相同兴趣的用户=======================================//
    $total = 0;
    for($i = 0; $i < count($tags); $i++) {
        $select = "SELECT ut.user_id 
                FROM tag as t,user_to_tag as ut
                WHERE t.user_tag LIKE '%" . $tags[$i] . "%' 
                AND t.tag_id = ut.tag_id 
                AND ut.user_id != $user_id ";
        $result = $sql->fetch_array3($select);
        $total2 = 0;
        for($j = 0; $j < count($result); $j++) {
            $recommand[$total++] = $result[$j]['user_id'];
            $tag = $tags[$i];
            $tag_to_uid[$tag][$total2++] = $result[$j]['user_id'];
            $uid = $result[$j]['user_id']; 
            $uid_to_tag[$uid]['user_tag'] .= $tags[$i] . ";";
        }
    } 
    
    //=============================根据距离近到远排列用户=======================================//   
    $num_fields = get_point($sql, $user_id);
    $latitude = $num_fields['latitude'];
    $longitude = $num_fields['longitude'];
    
    // 先查找$distance公里以内的用户
    $birth1 = 2011 - $age[0] . "-01-01";
    $birth2 = 2011 - $age[1] . "-12-12";
    $sql1 = "SELECT user_id, nick_name, image, online, gender, birthday, X(position) AS latitude, Y(position) AS longitude, ";
    $select = $sql1 . " ATAN2(    
                        SQRT(      
                            POW(COS(RADIANS(" . $latitude . ")) *           
                                SIN(RADIANS(Y(position) - " . $longitude . ")), 2) +       
                            POW(COS(RADIANS(X(position))) * SIN(RADIANS(" . $latitude . ")) -           
                                SIN(RADIANS(X(position))) * COS(RADIANS(" . $latitude . ")) *           
                                COS(RADIANS(Y(position) - " . $longitude . ")), 2)),     
                            (SIN(RADIANS(X(position))) * SIN(RADIANS(" . $latitude . ")) +      
                            COS(RADIANS(X(position))) * COS(RADIANS(" . $latitude . ")) *      
                            COS(RADIANS(Y(position) - " . $longitude . ")))  ) * 6372.795 AS distance 
                FROM user HAVING distance < " . $distance . " AND gender = '" . $gender . "' AND birthday <= '" . $birth1 . "' AND birthday >= '" . $birth2
            . "' ORDER BY distance ASC ";  
    $dis = $dis_users = $sql->fetch_array3($select); 

    // 为有爱好匹配的用户计算距离 
    for ($i = 0; $i < count($dis_users); $i++) {
        if (in_array($dis_users[$i]['user_id'], $recommand)) {
            $lat = $dis_users[$i]['latitude'];
            $lng = $dis_users[$i]['longitude'];
            $distance = get_distance($lat, $lng, $latitude, $longitude);
            $dis_users[$i]['distance'] = $distance; 
            $dis[$i]['judge'] = 1;
        }
        else {
            $dis[$i]['judge'] = 0;
        }
    }   
    // 取出tag
    for ($i = 0; $i < count($dis); $i++) {
        for ($j = 0; $j < count($tags); $j++) {        
            for ($k = 0; $k < count($tag_to_uid[$tags[$j]]); $k++) { 
                if ($tag_to_uid[$tags[$j]][$k] == $dis[$i]['user_id'])
                    if (1 == $dis[$i]['judge']) 
                        $dis[$i]['user_tag'] = $uid_to_tag[$dis[$i]['user_id']]['user_tag']; 
            }
        }
    } 
    // 从dis中按顺序取出兴趣匹配的用户
    $result = array();   
    for ($i = 0, $j = 0; $i < count($dis); $i++) {
        if (1 == $dis[$i]['judge'])
            $result[$j++] = $dis[$i];
    }
    // 取第$m_th的$num个用户
    $begin = $num * ($m_th - 1);
    $final_result = array();
    if ($num > count($result))
        $num = count($result);
    for ($i = $begin, $j = 0; $i < $begin + $num; $i++) {
         $final_result[$j++] = $result[$i];
    }

    return $final_result;
}

//==========================================================================================================//
// 为当前登录用户推荐与其兴趣爱好匹配的其他用户（按距离近到远排序）
// 获取所有推荐的用户
function recommend_users_total($user_id) {
    $tags = get_tags($user_id);
    
    $recommand = array();
    $tag_to_uid = array();
    $uid_to_tag = array();
    $sql = new sql();   
    //=============================搜索有相同兴趣的用户=======================================//
    $total = 0;
    for($i = 0; $i < count($tags); $i++) { 
        $select = "SELECT ut.user_id 
                FROM tag as t,user_to_tag as ut
                WHERE t.user_tag LIKE '%" . $tags[$i]['user_tag'] . "%' 
                AND t.tag_id = ut.tag_id 
                AND ut.user_id != $user_id ";
        $result = $sql->fetch_array3($select);
        $total2 = 0;
        for($j = 0; $j < count($result); $j++) {
            $recommand[$total++] = $result[$j]['user_id'];
            $tag = $tags[$i]['user_tag'];
            $tag_to_uid[$tag][$total2++] = $result[$j]['user_id'];
            $uid = $result[$j]['user_id']; 
            $uid_to_tag[$uid]['user_tag'] .= $tags[$i]['user_tag'] . ";";
            //echo $uid . "=" . $uid_to_tag[$uid]['user_tag'] . "<br/>";
        }
    }
    //=============================根据距离近到远排列用户=======================================//   
    $num_fields = get_point($sql, $user_id);
    $latitude = $num_fields['latitude'];
    $longitude = $num_fields['longitude'];
    $distance = 50000;  
    // 先查找$distance公里以内的用户
    $sql1 = "SELECT user_id, nick_name, image, online, gender, X(position) AS latitude, Y(position) AS longitude, ";
    $select = $sql1 . " ATAN2(    
                        SQRT(      
                            POW(COS(RADIANS(" . $latitude . ")) *           
                                SIN(RADIANS(Y(position) - " . $longitude . ")), 2) +       
                            POW(COS(RADIANS(X(position))) * SIN(RADIANS(" . $latitude . ")) -           
                                SIN(RADIANS(X(position))) * COS(RADIANS(" . $latitude . ")) *           
                                COS(RADIANS(Y(position) - " . $longitude . ")), 2)),     
                            (SIN(RADIANS(X(position))) * SIN(RADIANS(" . $latitude . ")) +      
                            COS(RADIANS(X(position))) * COS(RADIANS(" . $latitude . ")) *      
                            COS(RADIANS(Y(position) - " . $longitude . ")))  ) * 6372.795 AS distance 
                FROM user HAVING distance < " . $distance . " AND user_id != " . $user_id . " ORDER BY distance ASC ";  
    $dis = $dis_users = $sql->fetch_array3($select);

    // 为有爱好匹配的用户计算距离 
    for ($i = 0; $i < count($dis_users); $i++) {
        if (in_array($dis_users[$i]['user_id'], $recommand)) {
            $lat = $dis_users[$i]['latitude'];
            $lng = $dis_users[$i]['longitude'];
            $distance = get_distance($lat, $lng, $latitude, $longitude);
            $dis_users[$i]['distance'] = $distance; 
            $dis[$i]['judge'] = 1;
        }
        else {
            $dis[$i]['judge'] = 0;
        }
    }   
    // 取出tag
    for ($i = 0; $i < count($dis); $i++) {
        for ($j = 0; $j < count($tags); $j++) {
            for ($k = 0; $k < count($tag_to_uid[$tags[$j]['user_tag']]); $k++) {
                if ($tag_to_uid[$tags[$j]['user_tag']][$k] == $dis[$i]['user_id']) 
                    if (1 == $dis[$i]['judge'])
                        $dis[$i]['user_tag'] = $uid_to_tag[$dis[$i]['user_id']]['user_tag'];
            }
        }
    } 
    // 从dis中按顺序取出兴趣匹配的用户
    $result = array();   
    for ($i = 0, $j = 0; $i < count($dis); $i++) {
        if (1 == $dis[$i]['judge'])
            $result[$j++] = $dis[$i];
    }
    
    return $result;
}
// 分页获取推荐的用户
function recommend_users($user_id, $m_th = 1, $num = 5) {
    $result = recommend_users_total($user_id);
    // 取第$m_th的$num个用户
    $begin = $num * ($m_th - 1);
    $final_result = array();
    if ($num > count($result))
        $num = count($result);
    for ($i = $begin, $j = 0; $i < $begin + $num; $i++) {
         $final_result[$j++] = $result[$i];
    }

    return $final_result;
} 
// 计算推荐用户的页数
function count_recommend_slide($user_id, $page_size) {
    $amount = count(recommend_users_total($user_id));
    
    return get_page_numbers($amount, $page_size);
}

?>
