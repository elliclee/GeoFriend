<?

//register_check.php  功能：异步检测用户名是否可用
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'register_check.php' == basename($_SERVER['SCRIPT_FILENAME']))
    if (!isset($_POST['useremail']))
        die('You can not access this page directly!');
require_once 'sql/get_information.php';

$useremail = $_POST['useremail']; // get the username
$useremail = trim(htmlentities($useremail)); // strip some crap out of it

echo check_useremail($useremail); // call the check_username function and echo the results.
//============================================查询邮箱是否已注册过===================================================//

function check_useremail($useremail) {
    //首先判断用户名是否为空
    if ($useremail == "") {
        return '用户名不能为空';
    } else {
        $sql = new sql;
        $table = "user";         //要进行查询的数据库表
        $field = 'email';       // 要查询的字段，这里要用单引号！！
        if ($sql->is_exist($table, $field, $useremail)) {
            /* 这里可以写代码，处理邮箱已注册过的情况 */
            return '该邮件地址已被注册，请重新输入.';
        } else {
            /* 这里可以写代码，处理邮箱可以使用的情况 */
            return '该邮件地址可以使用.';
        }
    }
}

?>