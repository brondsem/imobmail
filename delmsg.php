<?php
/*
 +-----------------------------------------------------------------------+
 | delmsg.php                                                            |
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

include('sessioncheck.php');
include('config.php');

$accnr = $_GET['acc'];
if (!isset($_GET['acc']) || !is_numeric($accnr)) die("violation");

$serverarg = get_server_part($accnr);
$serveruser = get_server_user($accnr);
$serverpw = get_server_pw($accnr);

$imapfolder =  $_GET["folder"];
$msgno = $_GET["msgid"];

$mbox = imap_open($serverarg.$imapfolder, $serveruser, $serverpw)
or die("can't connect: " . imap_last_error());

imap_setflag_full($mbox, $msgno, "\\Deleted");
imap_close($mbox);
?>