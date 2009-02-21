<?php
/*
 +-----------------------------------------------------------------------+
 | sessioncheck.php                                                      |
 |                                                                       |
 | This file is part of the iMobMail, the webbased eMail application     |
 | for iPod touch(R) and iPhone(R)                                       |
 | Copyright (C) 2007 by Andreas Schwelling                              |
 | Licensed under the GNU GPL                                            |
 | See http://www.imobmail.org/ for more details or visit our bugtracker |
 | at http://trac.imobmail.org/                                          |
 |                                                                       |    
 | Use of iMobMail at your own risk!                                     |
 |                                                                       |
 +-----------------------------------------------------------------------+

*/

session_start();
include('config.php');
include_once('lang.php');

if ($_SESSION['user'] == $USERNAME && $_SESSION['passwd'] == md5($PASSWORD)) {
}  else  {
	header("location: index.php"); die;
}


function get_server_part($acc) {

	include('config.php');
	$server_host_var = "ACCOUNT_".$acc."_SERVER";

	$server_host = $$server_host_var;

	$server_args_var = "ACCOUNT_".$acc."_ARGS";
	$server_args = $$server_args_var;

	return "{".$server_host.$server_args."}";

}

function get_server_user($acc) {
	include('config.php');

	$server_user = "ACCOUNT_".$acc."_USERNAME";
	$server_user = $$server_user;

	return $server_user;

}

function get_server_pw($acc) {
	include('config.php');

	$server_pw = "ACCOUNT_".$acc."_PASSWORD";
	$server_pw = $$server_pw;

	return $server_pw;

}

?>