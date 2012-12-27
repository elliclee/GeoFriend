<?php
/* =========================================连接数据库================================================= */
include_once 'sql/get_information.php';

/* =========================================获取user的基本信息================================================= */
session_start();
$user_id = $_SESSION['userid'];

$currentUser = get_user($user_id);

/* =========================================获取user的兴趣爱好================================================= */
$tags = get_tags($user_id);

$userTag = "";
for ($i = 0; $i < count($tags); $i++) {
    $userTag .= $tags[$i]['user_tag'] . ";";
}

$tagMaxNumber = 10;

/* =========================================获取用户年龄================================================= */
$userAge = get_age($user_id);
?>

<div data-role="page" data-theme="b" data-id="profile_edit_page">
    <div data-role="header" data-id="header" data-theme="b" >
        <h2>Home</h2>
        <a data-role="button" data-icon="back" data-rel="back">返回</a>
        <a id="edit_confirm" data-role="button" data-icon="forward" class="ui-btn-right">确定</a>
        <!--submit函数好像有点冲突，这里用post函数，为了方便测试先写在这里，整理时再放到js文件里-->
        <script>
            $(document).ready(function(){
                $('#edit_confirm').click(function(){
                    $.post("profile_update.php", $('form#profile').serialize(), function(data){
                        alert('用户信息更新成功');
                    });
                });
            });
        </script>
    </div>

    <div data-role="content">
        <form id="profile">
            <ul class="l-detail l-edit l-inset" data-role="listview" data-inset="true">
                <li class="l-li-photo">
                    <div class="l-photo l-shadowbox" style="background-image: url(<?php echo small_to_large($currentUser['image']); ?>)">          

                    </div>
                    <div>
                        <a data-role="button" href="photo_upload.php"data-inline="true" class="l-button">上传图片</a>
                        <a data-role="button" href=""data-inline="true" class="l-button">刷新图片</a>
                    </div>
                </li>
                <li>
                    <label>昵称：</label>
                    <input type="text" name="nick_name" id="nick_name" value="<?php echo $currentUser['nick_name']; ?>"/>
                </li>
                <li>
                    <label>位置：</label>
                    <input type="hidden" name="longitude" id="longitude" value="<?php echo $currentUser['longitude']; ?>"/>
                    <input type="hidden" name="latitude" id="latitude" value="<?php echo $currentUser['latitude']; ?>"/>
                    <input type="text" name="address" id="address" class="l-position" value="<?php echo $currentUser['address']; ?>"/>

                    <div>
                        <a data-role="button" href=""data-inline="true" class="l-button" id="reGetPosition">重新获取</a>
                        <script>
                            //整理时再整合起来
                            $(document).ready(function(){
                                $('#reGetPosition').click(function(){
                                    if (navigator.geolocation) {
                                        navigator.geolocation.getCurrentPosition(
                                        function(position) { 
                                            var lat = position.coords.latitude; //纬度
                                            var lon = position.coords.longitude;  //经度 
                                            $.post('address_update.php', {
                                                longitude:lon,
                                                latitude:lat
                                            },function(data){
                                                $('#address').val(data);
                                                
                                            })
                                            //to do:把经纬度POST过去，取得返回值更新地理位置
                                            alert('address update seccess')
                                        }
                                    );
                                    } else {
                                        alert('Your browser does not support geolocation.');
                                    }
                                });
                            });
                        </script>
                    </div>
                </li>

                <li>
                    <label>爱好：</label>
                    <fieldset class="l-taglist">
                        <?php
                        $i = 0;
                        $count = count($tags);
                        for (; $i < $count; $i++) {
                            ?>
                            <input type="search" name="tag<?php echo $i; ?>" data-tag-id="<?php echo $i; ?>" value="<?php echo trim($tags[$i]['user_tag']); ?>"/>
                            <?php
                        }
                        ?>
                        <?php
                        for (; $i < $tagMaxNumber; $i++) {
                            ?>
                            <input type="search" name="tag<?php echo $i; ?>" data-tag-id="<?php echo $i ?>" value="" hidden/>
                            <?php
                        }
                        ?>
                        <input id="new_tag" type="search" data-tag-count="<?php echo $count; ?>" data-tag-max-number="<?php echo $tagMaxNumber; ?>" value="new_tag"/>
                    </fieldset>

                </li>
                <li>
                    <label>性别：</label>
                    <fieldset data-role="controlgroup" data-type="horizontal" class="l-button">
                        <input type="radio" name="gender" id="radio-choice-1" value="男" checked="checked" />
                        <label for="radio-choice-1">男</label>

                        <input type="radio" name="gender" id="radio-choice-2" value="女"  />
                        <label for="radio-choice-2">女</label>
                    </fieldset>
                </li>
                <li>
                    <label>年龄：</label>
                    <input class="l-number" type="number" name="age" id="" value="<?php echo $userAge; ?>" min="18" max="100"  />
                    <label>岁</label>

                    <label class="l-warning l-hidden">年龄范围为18～100岁</label>
                </li>
                <li>
                    <label>身高：</label>
                    <input class="l-number" type="number" name="height" id="" value="<?php echo $currentUser['height']; ?>" min="100" max="250" />
                    <label>厘米</label>
                    <label class="l-warning l-hidden">身高范围为100～250厘米</label>
                </li>
                <li>
                    <label>简介：</label>
                    <textarea cols="40" rows="8" name="introduction" id="textarea" ><?php echo $currentUser['introduction']; ?></textarea>
                </li> 
            </ul>
        </form>
    </div>

    <div data-role="footer" data-theme="b" data-id="footer">
        <h2 >Team Luff</h2>
    </div>

</div>
<!--    </body>
</html>-->
