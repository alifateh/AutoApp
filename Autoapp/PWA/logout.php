<?php

// clear all the session variables and redirect to login.php
require('config/public_conf.php');
require_once('Model/LoginModel.php');

use fateh\login\Member as member;

session_start();

if (isset($_SESSION["Mechanic_GUID"])) {
	$username = $_SESSION["Mechanic_GUID"];
	$member = new member($username);
	$x = $member->logout_Member($username);
	if ($x == 1) {
		session_unset();
		session_write_close();
		$url = "/";
		header("Location: $url");
	} else {
		session_unset();
		session_write_close();
		$url = "/";
		header("Location: $url");
	}
} else {
	session_unset();
	session_write_close();
	$url = "/";
	header("Location: $url");
}
