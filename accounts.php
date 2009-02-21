<?php

/*
 +-----------------------------------------------------------------------+
 | accounts.php                                                          |
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

$nr = 0;

$accnamearray = Array();

do {
	$nr++;
	$accountdesc = "ACCOUNT_".$nr."_DESCR";
	$accdesc = $$accountdesc;

	array_push($accnamearray, $accdesc);

} while ($accdesc != "");

if ($nr<=2) {
	header('Location: folders.php?acc=1');
}

?>

 <ul id="accounts" title="Accounts">

<?php
$accnr = 1;
foreach($accnamearray as $accname) {
	if ($accname == "") continue;

	echo "<li><a href=\"folders.php?acc=".$accnr."\">".$accname."</a></li>";
	$accnr++;
}

?>

</ul>

