<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
    </head>

    <BODY>
        <?php
        require_once 'MyImage.php';
        require_once 'login_sql.php';
        //==========================================获取用户信息===========================================//
        //$user_id = $_GET['user_id'];
        session_start();
        if (!isset($_GET['user_id']))
            $user_id = $_SESSION['userid'];
        else
            $user_id = $_GET['user_id'];

        //==========================================获取图片信息===========================================//
        $tmp_name = $_FILES["image"]["tmp_name"]; // 文件在Web服务器中临时存储的位置
        $name = $_FILES["image"]["name"];         // 用户系统中文件的名称
        $size = $_FILES["image"]["size"];         // 文件的字节大小
        $type = $_FILES["image"]["type"];         // 文件的MIME类型，如：text/plain或image/gif
        $error = $_FILES["image"]["error"];       // 与文件上传相关的错误代码
        //==========================================图片处理与保存=========================================//
        if (!(($type == "image/gif") || ($type == "image/jpeg") || ($type == "image/png"))) {
            echo "格式只支持gif或jpg或png<br/>";
            break;
        } else if ($size >= 2000000) {
            echo "图片太大<br/>";
            break;
        } else {
            if ($error > 0) {
                echo "Return Code: " . $error . "<br />";
            } else {
                echo "Upload: " . $name . "<br />";
                echo "Type: " . $type . "<br />";
                echo "Size: " . ($size / 1024) . " Kb<br />";
                echo "Temp file: " . $tmp_name . "<br />";

                if (file_exists("../images/user/" . $name)) {
                    echo $name . " already exists. ";
                } else {
                    //====================图片大小处理======================// 
                    $expand = ".jpg";
                    switch ($type) {
                        case "image/gif":
                            $expand = ".gif";
                            break;
                        case "image/jpeg":
                            echo $expand;
                            break;
                        case "image/png":
                            $expand = ".png";
                    }
                    $current_time = date("YmdHis");
                    $myImg = new MyImage();
                    $myImg->load($tmp_name);       // 获取临时文件中的图片
                    // 存大图
                    $myImg->resize(285, 285);      // 调整大小   
                    $myImg->save("../images/user/" . $user_id . "_" . $current_time . "_l" . $expand); // 将经过大小处理后的图片存入images/user目录下                  
                    // 存小图
                    $myImg->resize(80, 80);        // 调整大小      
                    $smallUrl = "../images/user/" . $user_id . "_" . $current_time . "_s" . $expand;
                    $myImg->save($smallUrl);

                    // 往数据库里面存入小图地址
                    $smallUrl = substr($smallUrl, 1);
                    save_img_url($user_id, $smallUrl);
                }
            }
        }

        echo '<a href="../index.html">图片上传成功，请点击返回</a>'
        ?>
    </BODY>
</HTML>
