<?php
/*
 +-----------------------------------------------------------------------+
 | logout.php                                                            |
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

session_unset();
session_destroy();

setcookie("touchmailuser", "", time()-3600);
setcookie("touchmailpw","", time()-3600);

header("location: index.php");

?>