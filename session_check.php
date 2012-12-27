<?

session_start();
require_once 'sql/get_information.php';
/* 30分钟刷新程序
     先统计出在线的用户数,如果没有在线用户，系统要保证一个系统指定用户。该系统用户时时在线的原因是
  保证该刷新程序的执行
     如果该登陆用户Session不存在了，表示用该用户离线。统计出时间。
 */
$sql = new sql();
$NowDate = date("Y-m-d");
//$NowDate = "to_date('" . $NowDate . "','YY/MM/DD')";
$NowTime = time();
//统计在线人数。30分钟更新一次

$stmtCount = "select * from tb_onlineuser";
$res = $sql->fetch_array3($stmtCount);
$CountUser = count($res);

echo 'online Number:' . $CountUser . "<br/><br/>";
//echo 'ID:' . $res[0]['N_OnlineUserId'] . '<br/>';
//判断在线否？
if ($CountUser == 0) {
    $j = 0;
    echo "no one is online";
} else {
    $stmtOnlineUser = "select N_OnlineUserId,D_LoginTime,N_OnlineID from tb_onlineuser";
    $resOnline = $sql->fetch_array3($stmtOnlineUser);

    $j = count($resOnline);
}

for ($b = 0; $b < $j; $b++) { //因为存入二维数组中，所以双重循环
    if (ceil(($NowTime - $resOnline[$b][1]) / 60) > 3) {//如果当前时间与一条记录的旧时间相差大于3分钟,则进行检查
        if ($_SESSION['useremail'] == "") {//如果此用户session不存在,表示已经退出。删掉
            $temGlid = $resOnline[$b][2]; //关联系统ID
            $temuserid = $resOnline[$b][0]; //用户ID
            $stmt = "delete from tb_onlineuser where N_OnlineID ='$temGlid' and N_ONLINEUSERID='$temuserid'";
            $sql->query($stmt);
            echo 'delete user<br/>';
            //并修改用户在线状态为0
            //$update = "UPDATE user SET online='0' where email='$useremail'";
            //不应该把在线状态放在个人信息表那里，应该统一存放在在线用户表那里，如果在线用户表没有该用户，则说明该用户不在线
            //
                //添加到统计表中
            $tmpTime = time(); //结束时间
            $stmtUserCount = "update tb_onlineusercount set D_OverDate=now() ,D_OverTime=$tmpTime where N_OnlineID='$temGlid'"; //条件是相关联的系统ID 
            $sql->query($stmtUserCount);
            echo 'update info success<br/>';
        } else {
            $tmpTime = time(); //取得临时用户时间
            $temuserid = $resOnline[$b][0];
            $stmt = "update tb_onlineuser set d_logintime=$tmpTime where 
N_ONLINEUSERID='$temuserid'";
            $sql->query($stmt);
            echo 'userID:' . $resOnline[$b][0];
            echo '<br/>update at  ' . $tmpTime . '<br/><br/>';
        }
    } else {
        echo 'sessionID:' . session_id();
        echo '<br/>userID:' . $resOnline[$b][0];
        echo '<br/>system current time:' . $NowTime . '<br/>';
        echo 'system old time:' . $resOnline[$b][1] . '<br/>';
        echo '<br/>';
    }
}
/* 如果要欢察统计表与在线表用户时间（当用户未离线时）
     select a.D_Logintime,b.D_logintime from tb_onlineuser a,tb_onlineusercount b
     where a.N_OnlineID=b.N_ONLINEID; 相差
      如果要统计出指定用户在线时间（当用户离线时）
     select D_logintime,D_OverTime from tb_onlineusercount where N_OnlineUserId='$USERID'; 相差
 */
?>