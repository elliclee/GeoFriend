<?php
require_once 'get_information.php';

// 根据当前用户输入的条件进行搜索
function search_users($user_id, $tags, $gender, $age, $distance = 50, $m_th = 1, $num = 12) {
    $total = 0;
    $recommand = array();
    $sql = new sql();
    //=============================搜索有相同兴趣的用户=======================================//
    for($i = 0; $i < count($tags); $i++) { 
        $select = "SELECT user_id FROM tag WHERE user_tag LIKE '%" . $tags[$i] . "%' AND user_id != $user_id LIMIT 0,5";
        $result = $sql->fetch_array3($select);
        for($j = 0; $j < count($result); $j++) {
            $recommand[$total++] = $result[$j]['user_id'];
        }
    }
    $recommand = array_unique($recommand); // 删除重复的user_id
    
    //=============================根据距离近到远排列用户=======================================//   
    $num_fields = get_point($sql, $user_id);
    $latitude = $num_fields['latitude'];
    $longitude = $num_fields['longitude'];
    
    // 先查找50公里以内的用户
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
    $dis_users = $sql->fetch_array3($select); 
    
    // 再筛选有爱好匹配的用户
    for ($i = 0; $i < count($dis_users); $i++) {
        if(!in_array($dis_users[$i]['user_id'], $recommand))
                unset($dis_users[$i]); // 删除没有共同兴趣爱好的用户
    }
    for ($i = 0; $i < count($dis_users); $i++) {
        if (!empty($dis_users[$i])) {
            $lat = $dis_users[$i]['latitude'];
            $lng = $dis_users[$i]['longitude'];
            $distance = get_distance($lat, $lng, $latitude, $longitude);
            $dis_users[$i]['distance'] = $distance;   
        }
    }
    // 取第$m_th的$num个用户
    $result = array();   
    for ($i = 0, $j = 0; $i < count($dis_users); $i++) {
        if (!empty($dis_users[$i])) {
            $result[$j++] = $dis_users[$i];
        }
    }
    $begin = $num * ($m_th - 1);
    $final_result = array();
    for ($i = $begin, $j = 0; $i < $num; $i++) {
        if (!empty($result[$i])) {
            $final_result[$j++] =  $result[$i];
        }
    }
    
    return $final_result;
}

// 为当前登录用户推荐与其兴趣爱好匹配的其他用户（按距离近到远排序）
function recommand_users($user_id, $m_th = 1, $num = 12) {
    $tags = get_tags($user_id);
    $total = 0;
    $recommand = array();
    $sql = new sql();
    //=============================搜索有相同兴趣的用户=======================================//
    for($i = 0; $i < count($tags); $i++) { 
        $select = "SELECT user_id FROM tag WHERE user_tag LIKE '%" . $tags[$i]['user_tag'] . "%' AND user_id != $user_id LIMIT 0,5";
        $result = $sql->fetch_array3($select);
        for($j = 0; $j < count($result); $j++) {
            $recommand[$total++] = $result[$j]['user_id'];
        }
    }
    $recommand = array_unique($recommand); // 删除重复的user_id
    
    //=============================根据距离近到远排列用户=======================================//   
    $num_fields = get_point($sql, $user_id);
    $latitude = $num_fields['latitude'];
    $longitude = $num_fields['longitude'];
    $distance = 50; 
    // 先查找50公里以内的用户
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
                FROM user HAVING distance < " . $distance . " ORDER BY distance ASC ";  
    $dis_users = $sql->fetch_array3($select);
    
    // 再筛选有爱好匹配的用户
    for ($i = 0; $i < count($dis_users); $i++) {
        if(!in_array($dis_users[$i]['user_id'], $recommand))
                unset($dis_users[$i]); // 删除没有共同兴趣爱好的用户
    }
    for ($i = 0; $i < count($dis_users); $i++) {
        if (!empty($dis_users[$i])) {
            $lat = $dis_users[$i]['latitude'];
            $lng = $dis_users[$i]['longitude'];
            $distance = get_distance($lat, $lng, $latitude, $longitude);
            $dis_users[$i]['distance'] = $distance;           
        }
    }
    // 取第$m_th的$num个用户
    $result = array();   
    for ($i = 0, $j = 0; $i < count($dis_users); $i++) {
        if (!empty($dis_users[$i])) {
            $result[$j++] = $dis_users[$i];
        }
    }
    $begin = $num * ($m_th - 1);
    $final_result = array();
    for ($i = $begin, $j = 0; $i < $num; $i++) {
        if (!empty($result[$i])) {
            $final_result[$j++] =  $result[$i];
        }
    }
    
    return $final_result;
}

?>
