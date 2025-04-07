<?php
// clear all the session variables and redirect to login.php

require_once('config/public_conf.php');
require_once('Model/LoginModel.php');

use fateh\login\Admin as user;

session_start();
if (isset($_SESSION["Admin_ID"])) {
	$username = $_SESSION["Admin_ID"];
	$member = new user($_SESSION["Admin_GUID"]);
	$member->logout_Admin($username);
} else {
	session_unset();
	session_write_close();
	$url = "./sysAdmin.php";
	header("Location: $url");
}
?>