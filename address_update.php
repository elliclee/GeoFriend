<?php

if (!empty($_SERVER['SCRIPT_FILENAME']) && 'address_update.php' == basename($_SERVER['SCRIPT_FILENAME']))
    if (!isset($_POST['longitude']))
        die('You can not access this page directly!');
require_once 'sql/login_sql.php';
require_once 'sql/get_information.php';
session_start();
$user_id = $_SESSION['userid'];
$longitude = $_POST['longitude'];
$latitude = $_POST['latitude'];
$address = get_address_by_location($latitude, $longitude);
update_position($user_id, $latitude, $longitude, $address);
echo $address;
?>
