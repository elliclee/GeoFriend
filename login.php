<?php

if (!empty($_SERVER['SCRIPT_FILENAME']) && 'login.php' == basename($_SERVER['SCRIPT_FILENAME']))
    if (!isset($_POST['email']))
        die('You can not access this page directly!');

require_once 'sql/get_information.php';
require_once 'sql/login_sql.php';

$useremail = trim($_POST['email']);
$pwd = trim($_POST['password']);
$longitude = trim($_POST['longitude']);
$latitude = trim($_POST['latitude']);

//2011-7-2 修改，修改数据库连接方式，统一成sql.php里面的连接

$result = find_user($useremail, $pwd);
if (count($result) > 0) {
    //把用户信息存入session里面
    session_start();
    ob_start();
    $_SESSION['userid'] = get_id($useremail, $pwd);
    $_SESSION['useremail'] = $useremail;
    $_SESSION['longitude'] = $longitude;
    $_SESSION['latitude'] = $latitude;

    //获取用户IP地址
    $ip = $_SERVER['REMOTE_ADDR'];
    $result = update_user($useremail, $ip);
    $address = get_address_by_location($latitude, $longitude);
    //这里是更新经纬度而已，还需要更新具体的地理位置
    update_position(get_id($useremail, $pwd), $latitude, $longitude, $address);
    ////////// 2011-7-13 为了实现判断用户是否在线而修改///////////////////////////////
    /*
     * 登陆成功后，如果该用户不在线(一般不在线，特殊情况如果他用另一台机器打开浏览器重新再登陆，那么
     * 他有可能在线),先进行session变量注册，取得相应条件向1.统计表与2.在线表中插数据。进入到登陆页。
     * 如果用户在线:先取得在线用户的系统ID,因为在备份该用户离开时有用。接着删除该在线用户.接着进行该
     * 用户离开时间的备份.
     */
    srand((double) microtime() * 1000000000);
    //取得一个系统ID号
    $intOnlineID = rand();
    $DatLoginDate = date('Y-m-d');  //取得系统日期存入到Online表中去
    $DatLogintime = time();  //取得系统时间
    $sql = new sql();
    $intOnlineUserID = $_SESSION['userid'];
    //先判断用户是否已经在线  2011-7-14
    $is_online = "select * from tb_onlineuser where N_ONLINEUSERID='$intOnlineUserID'";
    if (count($sql->fetch_array3($is_online)) != 0) {
        //说明已经在线
        //先取到在线用户关联系统ID
        $stmtSysID = "select N_ONLINEID from tb_onlineuser where N_ONLINEUSERID='$intOnlineUserID'";
        $res = $sql->fetch_array2($stmtSysID);
        $sys_id = $res['N_ONLINEID'];
        //然后先删除该用户
        $delete = "delete from tb_onlineuser where N_ONLINEUSERID='$intOnlineUserID'";
        $sql->query($delete);
        //最后作记录备份
        $tmpTime = time(); //结束时间
        $DatLoginDate = date("Y-m-d"); //结束日期
        $stmtUserCount = "update tb_onlineusercount set D_OverDate=now() ,D_OverTime=$tmpTime where N_OnlineID='$sys_id'"; //条件是相关联的系统ID
        $sql->query($stmtUserCount);
    }
    $stmt = "insert into tb_onlineuser (N_OnlineUserId,D_LoginTime,N_OnlineID) values ($intOnlineUserID,$DatLogintime,$intOnlineID)";
    $sql->query($stmt);
    $stmlC = "insert into tb_onlineusercount (N_OnlineID,N_OnlineUserId,D_LoginDate,D_LoginTime) values 
($intOnlineID,$intOnlineUserID,now(),$DatLogintime)";
    $sql->query($stmlC);
    ////////// 2011-7-13 为了实现判断用户是否在线而修改///////////////////////////////
    echo 'success';
} else {
    echo 'fail';
}
?>
