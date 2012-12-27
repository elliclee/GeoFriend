<?php

require_once 'sql.php';

// 获取个人信息
//$count_per_grid_slid = 12;

function get_user($user_id) {
    $sql = new sql();
    $select = "SELECT user_id, email, nick_name, birthday, gender, introduction, image, X(position) AS latitude, Y(position) AS longitude, address,logon_time, ip, online, height 
            from user
            where user_id = " . $user_id;
    $result = $sql->fetch_array2($select);

    return $result;
}

// 从tag表中获取用户的兴趣爱好
function get_tags($user_id) {
    $sql = new sql();
    $select = "SELECT u.user_id,t.tag_id,t.user_tag FROM tag AS t, user_to_tag AS u WHERE
               t.tag_id = u.tag_id AND u.user_id = " . $user_id;
    $result = $sql->fetch_array3($select);

    return $result;
}

// 获取用户年龄
function get_age($user_id) {
    $sql = new sql();
    $where = "user_id = " . $user_id;
    $num_fields = $sql->fetch_array("user", $where);
    $birthday = $num_fields['birthday'];

    $birt_year = substr($birthday, 0, 4);
    $current_date = date("Y-m-d H:i:s");
    $current_year = substr($current_date, 0, 4);

    return $current_year - $birt_year;
}

// 判读字符串是否相等，忽略大小写，并返回相应的sql语句
function result_sql($resultType = "grid") {
    $sql1 = "";
    if (0 == strcasecmp($resultType, "grid"))
        $sql1 = "SELECT user_id, nick_name, image, online, gender, X(position) AS latitude, Y(position) AS longitude, ";
    else if (0 == strcasecmp($resultType, "list"))
        $sql1 = "SELECT user_id, nick_name, image, online, gender, introduction, X(position) AS latitude, Y(position) AS longitude, ";
    else {
        echo $resultType . " is not one type!!!";
        break;
    }

    return $sql1;
}

function numbers($resultType) {
    $num = 12;
    if (0 == strcasecmp($resultType, "grid"))
        $num = 12;
    else if (0 == strcasecmp($resultType, "list"))
        $num = 10;
    else {
        echo $resultType . " is not one type!!!";
        break;
    }

    return $num;
}

// 从tweet表中获取user-id的发布消息的时间和消息内容   
function get_tweet($sql, $user_id) {
    $select = "select release_datetime AS tweet_time, content AS tweet_content
                    from tweet where user_id = " . $user_id . " ORDER BY release_datetime DESC LIMIT 0,1";
    $result = $sql->fetch_array2($select);
    return $result;
}

// 按照从新到旧的顺序
// 获取一个用户的tweets，5个一组，组序号为m_th
function get_tweets($user_id, $m_th = 1, $num = 5) {
    $sql = new sql();
    $begin = $num * ($m_th - 1);
    $select = "select tweet_id as tweet_id ,release_datetime AS tweet_time, content AS tweet_content
                    from tweet where user_id = " . $user_id .
            " ORDER BY release_datetime DESC  LIMIT " . $begin . "," . $num;
    return $sql->fetch_array3($select);
}

// 根据2个经纬度计算它们之间的距离
function rad($d) {
    return $d * 3.1415926535898 / 180.0;
}

function get_distance($lat1, $lng1, $lat2, $lng2) {
    $EARTH_RADIUS = 6378.137;
    $radLat1 = rad($lat1);
    //echo $radLat1;  
    $radLat2 = rad($lat2);
    $a = $radLat1 - $radLat2;
    $b = rad($lng1) - rad($lng2);
    $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
    $s = $s * $EARTH_RADIUS;
    $s = round($s * 10000) / 10000;

    return $s;
}

// 获取user-id的经纬度
function get_point($sql, $user_id) {
    $select = "select X(position) AS latitude, Y(position) AS longitude 
                    from user where user_id = " . $user_id;

    return $sql->fetch_array2($select);
}

// 根据性别判断sql语句
function gender_select($gender) {
    $gender_sql = "";
    if ("" == $gender)
        $gender_sql = "";
    else if ("男" == $gender) {
        $gender_sql = "AND gender = '男'";
    } else if ("女" == $gender) {
        $gender_sql = "AND gender = '女'";
    }

    return $gender_sql;
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * 获取user_id周围的人，按距离短到长排序（默认获取5公里以内）
 * 
 * 扫盲时刻：地球大概半径6372.795千米, 赤道周长 40075.7km 
 * 
 * @param integer $user_id    用户id
 * @param integer $num        一组用户的个数，默认12个
 * @param integer $m_th       第几组用户
 * @param float $distance     距离
 * @param string  $resultType 视图类型，默认grid
 * 
 * @return Array  用户信息的数组
 */
function get_neighbours($user_id, $m_th = 1, $gender="", $distance = 50, $num = 12, $resultType = "grid") {
    /* ==================================先获取user-id用户所在位置========================================== */
    $sql = new sql();
    $num_fields = get_point($sql, $user_id);
    $latitude = $num_fields['latitude'];
    $longitude = $num_fields['longitude'];

    // 获取性别sql语句
    $gender_sql = gender_select($gender);
    /* ==================================获取user-id周围的用户的信息========================================== */
    $sql1 = result_sql($resultType);
    $num = numbers($resultType);
    $begin = $num * ($m_th - 1);
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
                FROM user HAVING distance < " . $distance . " AND user_id != " . $user_id . " " . $gender_sql .
            " ORDER BY distance ASC LIMIT " . $begin . "," . $num;

    $result = $sql->fetch_array3($select);
    for ($i = 0; $i < count($result); $i++) {
        $lat = $result[$i]['latitude'];
        $lng = $result[$i]['longitude'];
        $distance = get_distance($lat, $lng, $latitude, $longitude);
        $result[$i]['distance'] = $distance;
    }

    return $result;
}

function count_neighbours($user_id, $gender="", $distance = 50) {
    /* ==================================先获取user-id用户所在位置========================================== */
    $sql = new sql();
    $num_fields = get_point($sql, $user_id);
    $latitude = $num_fields['latitude'];
    $longitude = $num_fields['longitude'];
    // 获取性别sql语句
    $gender_sql = gender_select($gender);

    $select = "SELECT user_id,  ATAN2(    
                        SQRT(      
                            POW(COS(RADIANS(" . $latitude . ")) *           
                                SIN(RADIANS(Y(position) - " . $longitude . ")), 2) +       
                            POW(COS(RADIANS(X(position))) * SIN(RADIANS(" . $latitude . ")) -           
                                SIN(RADIANS(X(position))) * COS(RADIANS(" . $latitude . ")) *           
                                COS(RADIANS(Y(position) - " . $longitude . ")), 2)),     
                            (SIN(RADIANS(X(position))) * SIN(RADIANS(" . $latitude . ")) +      
                            COS(RADIANS(X(position))) * COS(RADIANS(" . $latitude . ")) *      
                            COS(RADIANS(Y(position) - " . $longitude . ")))  ) * 6372.795 AS distance 
                FROM user HAVING distance < " . $distance . " AND user_id != " . $user_id . " " . $gender_sql;

    $result = $sql->fetch_array3($select);
    return count($result);
}

// 获取neighbours的页面数
//Modified by Stan 2011/7/6
function count_neighbours_slide($user_id, $num = 12, $gender="", $distance = 50) {
    $amount = count_neighbours($user_id, $gender, $distance);
    return get_page_numbers($amount, $num);
}

// 获取following的页面数
function count_following_slide($user_id, $num = 5, $gender="", $distance = 500000) {
    $amount = count_following($user_id, $gender, $distance);
    return get_page_numbers($amount, $num);
}

// 获取distance_neighbours的页面数
function count_position_neighbours_slide($user_id, $latitude, $longitude, $num = 12, $gender="", $distance = 50) {
    $amount = count_position_neighbours($user_id, $latitude, $longitude, $gender, $distance);
    return get_page_numbers($amount, $num);
}

// 记算总共有多少页
function get_page_numbers($amount, $page_size) {
    $page_count = 0;
    if ($amount) {
        if ($amount < $page_size) {
            $page_count = 1;                               //如果总数据量小于$PageSize，那么只有一页
        }
        if ($amount % $page_size) {                        //取总数据量除以每页数的余数
            $page_count = (int) ($amount / $page_size) + 1; //如果有余数，则页数等于总数据量除以每页数的结果取整再加一
        } else {
            $page_count = $amount / $page_size;            //如果没有余数，则页数等于总数据量除以每页数的结果
        }
    } else {
        $page_count = 0;
    }

    return $page_count;
}

/**
 * 获取user_id周围的自己关注的人，按距离短到长排序
 * 
 * @param integer $user_id    用户id
 * @param integer $num        一组用户的个数，默认12个
 * @param integer $m_th       第几组用户
 * @param float $distance     距离
 * @param string  $resultType 视图类型，默认grid
 * 
 * @return Array  用户信息数组
 */
function get_following($user_id, $m_th = 1, $gender="", $distance = 50, $num = 12, $resultType = "grid") {
    /* ==================================先获取user-id用户所在位置========================================== */
    $sql = new sql();
    $num_fields = get_point($sql, $user_id);
    $latitude = $num_fields['latitude'];
    $longitude = $num_fields['longitude'];
    // 获取性别sql语句
    $gender_sql = gender_select($gender);
    /* ==================================获取user-id周围的其关注的人的信息========================================== */
    $sql1 = result_sql($resultType);
    $sql2 = "(SELECT following_id FROM following WHERE user_id = " . $user_id . " )";
    $num = numbers($resultType);
    $begin = $num * ($m_th - 1);
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
                FROM user WHERE user_id != " . $user_id . " AND user_id IN " . $sql2 . "  " . $gender_sql . "
                    HAVING distance < " . $distance .
            " ORDER BY distance ASC LIMIT " . $begin . "," . $num;

    //=======================================获取tweet表中的相关信息================================================//
    $result = $sql->fetch_array3($select);
    for ($i = 0; $i < count($result); $i++) {
        if (!empty($result[$i]['user_id'])) {
            $tweet = get_tweet($sql, $result[$i]['user_id']);
            $result[$i]['tweet_time'] = $tweet['tweet_time'];
            $result[$i]['tweet_content'] = $tweet['tweet_content'];
        }
    }
    return $result;
}

function count_following($user_id, $gender="", $distance = 5000000) {
    /* ==================================先获取user-id用户所在位置========================================== */
    $sql = new sql();
    $num_fields = get_point($sql, $user_id);
    $latitude = $num_fields['latitude'];
    $longitude = $num_fields['longitude'];
    // 获取性别sql语句
    $gender_sql = gender_select($gender);
    $sql2 = "(SELECT following_id FROM following WHERE user_id = " . $user_id . " )";

    $select = "SELECT user_id,  ATAN2(    
                        SQRT(      
                            POW(COS(RADIANS(" . $latitude . ")) *           
                                SIN(RADIANS(Y(position) - " . $longitude . ")), 2) +       
                            POW(COS(RADIANS(X(position))) * SIN(RADIANS(" . $latitude . ")) -           
                                SIN(RADIANS(X(position))) * COS(RADIANS(" . $latitude . ")) *           
                                COS(RADIANS(Y(position) - " . $longitude . ")), 2)),     
                            (SIN(RADIANS(X(position))) * SIN(RADIANS(" . $latitude . ")) +      
                            COS(RADIANS(X(position))) * COS(RADIANS(" . $latitude . ")) *      
                            COS(RADIANS(Y(position) - " . $longitude . ")))  ) * 6372.795 AS distance 
                FROM user HAVING distance < " . $distance . " AND user_id != " . $user_id . " AND user_id IN " . $sql2 . "  " . $gender_sql;

    $result = $sql->fetch_array3($select);
    return count($result);
}

// 检索user_id在经纬度（lat，lon）周围的用户信息【最近的第m-th组num（默认为12）个用户】
function get_position_neighbours($user_id, $latitude, $longitude, $m_th = 1, $gender="", $distance = 50, $num = 12, $resultType = "grid") {
    /* ==================================获取user-id周围的用户的信息========================================== */
    $sql = new sql();
    $sql1 = result_sql($resultType);
    $num = numbers($resultType);
    $begin = $num * ($m_th - 1);
    // 获取性别sql语句
    $gender_sql = gender_select($gender);

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
                FROM user HAVING distance < " . $distance . " AND user_id != " . $user_id . "  " . $gender_sql .
            " ORDER BY distance ASC LIMIT " . $begin . "," . $num;

    $result = $sql->fetch_array3($select);
    for ($i = 0; $i < count($result); $i++) {
        $lat = $result[$i]['latitude'];
        $lng = $result[$i]['longitude'];
        $distance = get_distance($lat, $lng, $latitude, $longitude);
        $result[$i]['distance'] = $distance;
    }

    return $result;
}

function count_position_neighbours($user_id, $latitude, $longitude, $gender="", $distance = 50) {
    /* ==================================先获取user-id用户所在位置========================================== */
    $sql = new sql();
    // 获取性别sql语句
    $gender_sql = gender_select($gender);

    $select = "SELECT user_id,  ATAN2(    
                        SQRT(      
                            POW(COS(RADIANS(" . $latitude . ")) *           
                                SIN(RADIANS(Y(position) - " . $longitude . ")), 2) +       
                            POW(COS(RADIANS(X(position))) * SIN(RADIANS(" . $latitude . ")) -           
                                SIN(RADIANS(X(position))) * COS(RADIANS(" . $latitude . ")) *           
                                COS(RADIANS(Y(position) - " . $longitude . ")), 2)),     
                            (SIN(RADIANS(X(position))) * SIN(RADIANS(" . $latitude . ")) +      
                            COS(RADIANS(X(position))) * COS(RADIANS(" . $latitude . ")) *      
                            COS(RADIANS(Y(position) - " . $longitude . ")))  ) * 6372.795 AS distance 
                FROM user HAVING distance < " . $distance . " AND user_id != " . $user_id . " " . $gender_sql;

    $result = $sql->fetch_array3($select);
    return count($result);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//===============================================根据经纬度进行反向地址解析======================================================//
/**
 * By Google Map API
 * 
 * @param string $x lng
 * @param string $y lat
 * @param int $level
 */
function get_address_by_location($x, $y, $level = 0) {
    //============================= 方法二：output=csv,也可以是xml或json，不过使用csv返回的数据最简洁方便解析================================//
    $res = file_get_contents("http://ditu.google.cn/maps/geo?output=csv&key=abcdef&q=$x,$y");
    //echo $res."=";
    $result = explode(",", $res);
    //echo $result;
    $result = $result[2];
    $result = explode(" ", $result);
    $result = $result[0];
    $result = explode("\"", $result);
    $result = $result[1];

    return $result;
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//===============================================根据距离返回相应信息======================================================//
/**
 * 根据距离返回相应信息
 * @return 与距离对应的提示信息
 * @param float $distance (距离，单位为KM千米)
 */
function get_dis_info($distance) {

    if ($distance < 0)
        return "???";
    else if ($distance == 0)
        return "≈0";
    else if ($distance > 0 && $distance <= 1)
        return round($distance * 1000, 1) . "m";
    else if ($distance > 1 && $distance <= 100)
        return round($distance, 1) . "km";
    else if ($distance > 100 && $distance <= 1000)
        return round($distance) . "km";
    else
        return ">1000km";
}

//Modified by Stan 2011/7/4 删除distance检索元组
function get_followings($user_id, $m_th = 1, $gender="", $num = 5, $resultType = "grid") {
    /* ==================================先获取user-id用户所在位置========================================== */
    $sql = new sql();
    $num_fields = get_point($sql, $user_id);
    $latitude = $num_fields['latitude'];
    $longitude = $num_fields['longitude'];
    if ($latitude == 0)
        $latitude = 0.000001;
    if ($longitude == 0)
        $longitude = 0.0000001;
    // 获取性别sql语句
    $gender_sql = gender_select($gender);
    /* ==================================获取user-id周围的其关注的人的信息========================================== */
    $sql1 = result_sql($resultType);
    $sql2 = "(SELECT following_id FROM following WHERE user_id = " . $user_id . " )";
    $begin = $num * ($m_th - 1);
    //echo $begin.$num;
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
                FROM user WHERE user_id != " . $user_id . " AND user_id IN " . $sql2 . "  " . $gender_sql . "
                    ORDER BY distance ASC LIMIT " . $begin . "," . $num;

    //=======================================获取tweet表中的相关信息================================================//
    $result = $sql->fetch_array3($select);
    for ($i = 0; $i < count($result); $i++) {
        if (!empty($result[$i]['user_id'])) {
            $tweet = get_tweet($sql, $result[$i]['user_id']);
            $result[$i]['tweet_time'] = $tweet['tweet_time'];
            $result[$i]['tweet_content'] = $tweet['tweet_content'];
        }
    }
    return $result;
}

function count_followings($user_id, $gender="", $distance = 50) {
    /* ==================================先获取user-id用户所在位置========================================== */
    $sql = new sql();
    $num_fields = get_point($sql, $user_id);
    $latitude = $num_fields['latitude'];
    $longitude = $num_fields['longitude'];
    // 获取性别sql语句
    $gender_sql = gender_select($gender);
    $sql2 = "(SELECT following_id FROM following WHERE user_id = " . $user_id . " )";

    $select = "SELECT user_id,  ATAN2(    
                        SQRT(      
                            POW(COS(RADIANS(" . $latitude . ")) *           
                                SIN(RADIANS(Y(position) - " . $longitude . ")), 2) +       
                            POW(COS(RADIANS(X(position))) * SIN(RADIANS(" . $latitude . ")) -           
                                SIN(RADIANS(X(position))) * COS(RADIANS(" . $latitude . ")) *           
                                COS(RADIANS(Y(position) - " . $longitude . ")), 2)),     
                            (SIN(RADIANS(X(position))) * SIN(RADIANS(" . $latitude . ")) +      
                            COS(RADIANS(X(position))) * COS(RADIANS(" . $latitude . ")) *      
                            COS(RADIANS(Y(position) - " . $longitude . ")))  ) * 6372.795 AS distance 
                FROM user HAVING user_id != " . $user_id . " AND user_id IN " . $sql2 . "  " . $gender_sql;

    $result = $sql->fetch_array3($select);
    return count($result);
}

function small_to_large($small_url) {
    $pos = strpos($small_url, "_s");
    $large_url = substr($small_url, 0, $pos);
    $large_url = $large_url . "_l" . substr($small_url, $pos + 2);
    return $large_url;
}

?>
