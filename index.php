<?php

/*
 +-----------------------------------------------------------------------+
 | index.php                                                             |
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

if (!strstr($_SERVER['HTTP_USER_AGENT'],"Safari") && !isset($_GET['cont']) && !isset($_POST['user']) && !isset($_POST['passwd'])) {

	echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n".
	"\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\"><html><body>Diese Webapplikation ist fuer mobile Safari-Browser entwickelt. Bei anderen Browsern wie Firefox, Opera, IE etc. kann es zu Fehlfunktionen kommen! <a href=\"index.php?cont\">Seite trotzdem oeffnen?</a></body></html>";
	exit;

}
include('config.php');
session_start();

if ($_SESSION['user'] == $USERNAME && $_SESSION['passwd'] == md5($PASSWORD)) {



	header("location: intro.php");
	return;
}

$cookuser = $_COOKIE['touchmailuser'];
$cookpw = $_COOKIE['touchmailpw'];

if ($cookuser == $USERNAME && $cookpw = md5($PASSWORD)) {
	$_SESSION['user'] = $cookuser;
	$_SESSION['passwd'] = $cookpw;
	header("location: intro.php");
	return;

}

$un = $_POST['user'];
$pw = $_POST['passwd'];



if ($un == "" || $pw == "") {


} else

if ($un == $USERNAME && $pw == $PASSWORD) {
	if ($_POST['keepli'] == '1') {

		setcookie("touchmailuser", $USERNAME, time()+3600*24);
		setcookie("touchmailpw", md5($PASSWORD), time()+3600*24);
	}
	$_SESSION['user'] = $USERNAME;
	$_SESSION['passwd'] = md5($PASSWORD);

	header("location: intro.php");
	return;
} else {

	$errmsg = "Logindaten falsch!";

}

echo <<<END

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
         "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>iMobMail</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=320; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>
<style type="text/css" media="screen">@import "./iui/iui.css";</style>
<script type="application/x-javascript" src="./iui/iui.js"></script>
</head>
<body>







    <form id="home" class="dialog" selected="true" action="index.php"  method="post" target="_top">
        <fieldset style="height:490px;" >
            <h1>Login</h1>
            

            <a class="button blueButton" type="submit" onclick="JavaScript:document.getElementById('home').removeAttribute('selected');">Senden</a>

            
            <div style="color:#ffffff;width:80%;padding:8px;text-align:left;margin-top:15px;margin-left:20px;margin-right:20px;" align="center">
            Bitte authentifizieren Sie sich mit Ihren Account-Daten!
            </div>
            
            <label>Username:</label>
         <input type="text" id="un" name="user" style="margin-right:0px;padding-right:0px;width:60%;padding-left:100px;"/>
            <label>Passwort:</label>
           <input type="password" id="pw" name="passwd"  style="padding-left:100px;margin-right:0px;padding-right:0px;width:60%;"/>
             <label style="width:70%;background:#ffffff;-webkit-border-radius: 7px;border:1px solid black;padding:10px;margin:0px;margin-top:8px;margin-left:10px;margin-right:10px;">eingeloggt bleiben</label>
             <input type="checkbox" name="keepli" value="1" style="position:absolute;right:0px;padding:0px;height:40px;width:40px;z-index:9999;"/>
            
                        <div style="color:red;width:80%;padding:8px;text-align:center;margin-top:55px;margin-left:20px;margin-right:20px;font-size:18px;font-weight:bold;" align="center">
            $errmsg
            </div>
            <div style="width:90%;padding:8px;text-align:center;margin-top:0px;padding-top:0px;font-size:15px;margin-left:10px;color:#ffffff;" align="center"><img src="./iui/logo.png"><br/>
            <a href="http://www.imobmail.org/" target="_blank" style="color:#ffffff;background:transparent;padding:1px;text-shadow: rgba(0, 0, 0, 0.8) 2px 2px 5px;text-decoration:none;">iMobMail</a> - &copy; 2007 <a href="http://www.andi.de/" target="_blank" style="color:#ffffff;background:transparent;padding:1px;text-shadow: rgba(0, 0, 0, 0.8) 2px 2px 5px;text-decoration:none;">Andreas Schwelling</a>
     		<br/>
     		Benutzung auf eigene Gefahr!<br/>
     		Diese Anwendung verwendet adaptierte Bestandteile des <a href="http://code.google.com/p/iui/" target="_blank" style="color:#ffffff;background:transparent;padding:1px;text-shadow: rgba(0, 0, 0, 0.8) 2px 2px 5px;text-decoration:none;">iui-Toolkits</a>.
            </div>
            
        </fieldset>
    </form>
    </body>
</html>

END;

?>
