
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        //@author  凌少虎  2011-6-8  修改此php文件
        
        require_once 'search_recommend.php';
        require_once 'get_information.php';
        require_once 'login_sql.php';
        
        // 这里演示mysql类的用法，你们试着调用类里面的函数，看看运行结果就行了···
        //$table = "user";
        
        //============================================查询邮箱是否已注册过===================================================//
//        $field = 'email';       // 要查询的字段，这里要用单引号！！
//        $input = "ling@163.com";// 这是要查询的账号
//        if ($sql->is_exist($table, $field, $input)) {
//            echo "对不起，该邮箱已经注册过<br/>";
//            /* 这里可以写代码，处理邮箱已注册过的情况 */
//        }
//        else {
//            echo "恭喜你，该邮箱可以使用<br/>";
//            /* 这里可以写代码，处理邮箱可以使用的情况 */
//        }
//        
//        //===============================================插入================================================================//
//        // 新建一个用户（注意user_id不用我们添加的，数据库会自动添加的）
//        // 新建用户的时候，你们一定要先查询user表，确保email是未注册过的。前面有例子
//        $newUser = array(
//            "email" => "test22@163.com", "password" => "1234", "nick_name" => "ling", 
//            "birthday" => "1989-08-24", "gender" => "男", "longitude" => 12.12, "latitude" => 34.3,
//            "register_datetime" => "2011-09-10 12-09-43"
//        );
//        
//        // 向表里插入一个数据(插入成功则返回true)
//        $result = $sql->insert("user", $newUser);     
//        if ($result)
//            echo "插入成功<br/>";
//        else
//            echo "插入失败<br/>";
//        
//        //===============================================更新================================================================//
//        // 填写要更新的信息
//        $updateUser = array(
//            "email" => "ling@163.com", "nick_name" => "更新函数测试", 
//        );
//        $where = "email = 'test@qq.com'";           // 注意双引号里面还有单引号！！！
//        // 开始更新
//        $result = $sql->update($table, $updateUser, $where);
//        if ($result)
//            echo "更新成功<br/>";
//        else
//            echo "更新失败<br/>";
        
        //===============================================查询================================================================//
//        echo 'user: <br/>';
//        $sql = new sql();
//        $where = "email = 'test@163.com'";          // 注意双引号里面还有单引号！！！        
//        print_r($sql->fetch_row($table, $where)); // 输出获取到的一行记录,这行记录是存在一个数组里面的。
//        echo "<br/>";
//        $num_fields = $sql->fetch_array($table, $where);     
//        print_r($num_fields['gender']);             // 注意这个用法！很实用的
//        echo "<br/><br/>";
        
        //===============================================删除================================================================//
        // 如果不指定 where 子句，delete 将删除表中所有的记录，而且是立即删除，即使你想哭都没有地方，也没有时间
        // 所有你们两个delete数据前，一定要备份数据库···
//        $where = "email = 'test22@163.com'";        // 注意双引号里面还有单引号！！！
//        // 开始删除
//        $result = $sql->delete($table, $where);
//        if ($result)
//            echo "删除成功<br/>";
//        else
//            echo "删除失败<br/>";
        
        //===============================================获取neighbours================================================================//
        /////////////////////////////////////////////////////////////////////////////////////////
        // get_neighbours用法：查找$user_id在指定距离内的注册用户        
        $user_id = 5;            
//        $users = get_neighbours($user_id); // 默认grid类型 
//        // grid类型：user_id, nick_name, image, online, gender, latitude, longitude
//        // list类型：user_id, nick_name, image, online, gender, introduction, latitude, longitude
//        for($i = 0; $i < count($users); $i++) 
//        {   // 用法
//            if (!empty ($users[$i]['user_id'])) {
//                echo "user_id: " . $users[$i]['user_id'] . "</br>"; 
//                echo "nick_name: " . $users[$i]['nick_name'] . "</br>";
//                echo "image url: " . $users[$i]['image'] . "</br>";
//                echo "online: " . $users[$i]['online'] . "</br>";
//                echo "gender: " . $users[$i]['gender'] . "</br>";
//                echo "latitude: " . $users[$i]['latitude'] . "</br>";
//                echo "longitude: " . $users[$i]['longitude'] . "</br>";
//                echo "距离user-id为5的用户的距离为：". $users[$i]['distance'] . "</br></br>";               
//            }
//        } 
//        echo "neighbours页面总数：" . count_neighbours_slide($user_id);
//        echo "</br></br>";
//        /////////////////////////////////////////////////////////////////////////////////////////
//        // get_following用法：查找在指定距离内的被$user_id关注的人
//        $friends = get_following($user_id);
//        for($i = 0; $i < count($friends); $i++) 
//        {   // 用法
//            if (!empty ($users[$i]['user_id'])) {
//                echo "user_id: " . $friends[$i]['user_id'] . "</br>"; 
//                //echo "nick_name: " . $users2[$i]['nick_name'] . "</br>";
//                //echo "image url: " . $users2[$i]['image'] . "</br>";
//                //echo "online: " . $users2[$i]['online'] . "</br>";
//                //echo "gentder: " . $users2[$i]['gentder'] . "</br>";
//                //echo "introduction: " . $users2[$i]['introduction'] . "</br>";
//                //echo "latitude: " . $users2[$i]['latitude'] . "</br>";
//                //echo "longitude: " . $users2[$i]['longitude'] . "</br>";
//                echo "tweet_time: " . $friends[$i]['tweet_time'] . "</br>";       // 获取tweet表中的:release_datetime 发布消息的时间
//                echo "tweet_content: " . $friends[$i]['tweet_content'] . "</br></br>"; // 获取tweet表中的:content 消息内容
//            }
//        }
//        echo "following页面总数：" . count_following_slide($user_id);
//        echo "</br></br>";
//        /////////////////////////////////////////////////////////////////////////////////////////
//        // get_position_neighbours用法：检索user_id在经纬度（lat，lon）周围的用户信息
//        echo "</br>";
//        $users3 = get_position_neighbours($user_id, 23, 23.1);
//        for($i = 0; $i < count($users3); $i++) 
//        {   // 用法
//            echo "user_id: " . $users3[$i]['user_id'] . "</br>"; 
//            //echo "nick_name: " . $users3[$i]['nick_name'] . "</br>";
//            //echo "image url: " . $users3[$i]['image'] . "</br>";
//            //echo "online: " . $users3[$i]['online'] . "</br>";
//            //echo "latitude: " . $users3[$i]['latitude'] . "</br>";
//            //echo "longitude: " . $users3[$i]['longitude'] . "</br></br>";
//            echo "距离（23,23.1）的距离为: " . $users3[$i]['distance'] . "</br></br>";
//        }
//        echo "position_neighbours页面总数：" . count_position_neighbours_slide($user_id, 23, 23.1);
//        echo "</br></br>";
//        
//        /////////////////////////////////////////////////////////////////////////////////////////
//        // get_age用法：获取用户年龄
//        echo "用户年龄:" . get_age($user_id) . "</br></br>";
//        
//        /////////////////////////////////////////////////////////////////////////////////////////
//        // get_user用法：获取$user_id用户的所有个人信息
//        $my_user = get_user($user_id);
//        echo $my_user['gender'] . "<br/><br/>";
//        
//        /////////////////////////////////////////////////////////////////////////////////////////
//        // get_address_by_location用法：根据经纬度获取所在地理位置
//        $location = get_address_by_location(23.1291630,113.2644350); // 变量：纬度，经度
//        //$location = get_address_by_location(23.049424,113.4005379);
//        echo "用户所在位置:" . $location . "<br/><br/>";
        
        /////////////////////////////////////////////////////////////////////////////////////////
        // update_position用法：更新用户的经纬度
        //update_position(3, 23.1291630, 113.2644350);
        
        /////////////////////////////////////////////////////////////////////////////////////////
        echo "=====================================================================================<br/>";
        echo "推荐用户 用法：<br/>";
        $user_id = 1;
        $recommand = recommend_users($user_id, 4);
        for($i = 0; $i < count($recommand); $i++) {
            if(!empty($recommand[$i])) {
            echo "user_id: " . $recommand[$i]['user_id'] . "<br/>";
            echo "nick_name: " . $recommand[$i]['nick_name'] . "</br>";
            echo "image url: " . $recommand[$i]['image'] . "</br>";
            echo "online: " . $recommand[$i]['online'] . "</br>";
            echo "gender: " . $recommand[$i]['gender'] . "</br>";
            echo "latitude: " . $recommand[$i]['latitude'] . "</br>";
            echo "longitude: " . $recommand[$i]['longitude'] . "</br>";
            echo "匹配的兴趣: " . $recommand[$i]['user_tag'] . "</br>";
            echo "距离user_id=5的距离为: " . $recommand[$i]['distance'] . "</br></br>";
            }
        }
        
        /////////////////////////////////////////////////////////////////////////////////////////
//        echo "=====================================================================================<br/>";
//        echo "搜索用户 用法：<br/>";
//        $user_id = 18;
//        $tags = array();
//        $age = array();
//        $tags[0] = "海贼王";
//        $tags[1] = "生活大爆炸";
//        $age[0] = 18;
//        $age[1] = 24;
//        $search = search_users($user_id, $tags, "男", $age, 2); // 取第2页
//        for($i = 0; $i < count($search); $i++) {
//            echo "user_id: " . $search[$i]['user_id'] . "<br/>";
//            echo "nick_name: " . $search[$i]['nick_name'] . "</br>";
//            echo "image url: " . $search[$i]['image'] . "</br>";
//            echo "online: " . $search[$i]['online'] . "</br>";
//            echo "gender: " . $search[$i]['gender'] . "</br>";
//            echo "latitude: " . $search[$i]['latitude'] . "</br>";
//            echo "longitude: " . $search[$i]['longitude'] . "</br>";       
//            echo "兴趣匹配: " . $search[$i]['user_tag'] . "</br>"; 
//            echo "距离user_id=18的距离为: " . $search[$i]['distance'] . "</br></br>";
//        }
//        
//        /////////////////////////////////////////////////////////////////////////////////////////
//        echo "=====================================================================================<br/>";
//        $tweet_id = 1;
//        $comments = get_comment($tweet_id);
//        for($i = 0; $i < count($comments); $i++) {
//            echo "评论用户id: " . $comments[$i]['c_user_id'] . "<br/>";
//            echo "被评论用户id: " . $comments[$i]['bc_user_id'] . "</br>";
//            echo "评论时间: " . $comments[$i]['comment_datetime'] . "</br>";
//            echo "评论内容: " . $comments[$i]['content'] . "</br></br>";
//        }
        /////////////////////////////////////////////////////////////////////////////////////////
        // 测试get_tags函数
//        echo "=====================================================================================<br/>";
//        echo "get_tags函数用法<br/>";
//        $user_id = 5;
//        $result = get_tags($user_id);
//        for($i = 0; $i < count($result); $i++) {
//            echo "tag_id: " . $result[$i]['tag_id'] . "<br/>";
//            echo "user_id: " . $result[$i]['user_id'] . "</br>";
//            echo "user_tag: " . $result[$i]['user_tag'] . "</br></br>";
//        }
//        
//        /////////////////////////////////////////////////////////////////////////////////////////
//        // 测试login_sql.php中的small_to_large函数
//        echo "=====================================================================================<br/>";
//        echo "small_to_large函数用法<br/>";
//        $small_url = "./images/user/1_20110706104927_s.jpg";
//        $large_url = small_to_large($small_url);
//        echo $large_url."<br/>";
//        
//        /////////////////////////////////////////////////////////////////////////////////////////
//        // 测试login_sql.php中的small_to_large函数
//        echo "=====================================================================================<br/>";
//        echo "check_tag函数用法<br/>";
//        $user_id = 2;
//        $tag = "生活大爆炸";
//        $result = check_tag($user_id, $tag);
//        echo $result."<br/>";
//        
//        /////////////////////////////////////////////////////////////////////////////////////////
//        // 测试login_sql.php中的small_to_large函数
//        echo "=====================================================================================<br/>";
//        echo "update_tags函数用法<br/>";
//        $user_id = 2;
//        $ex_tags = get_tags($user_id);    
//        $current_tags[0] = "裸婚时代";
//        $current_tags[1] = "旅行";
//        $current_tags[2] = "篮球";
//        update_tags($user_id, $ex_tags, $current_tags);
        
        ?>
    </body>
</html>
