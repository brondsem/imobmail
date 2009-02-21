<?php

/*
 +-----------------------------------------------------------------------+
 | folders.php                                                           |
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
?>
    <ul id="folders" title="Ordner">
<?php

include('config.php');

$accnr = $_GET['acc'];
if (!isset($_GET['acc']) || !is_numeric($accnr)) die("violation");

$serverarg = get_server_part($accnr);
$serveruser = get_server_user($accnr);
$serverpw = get_server_pw($accnr);


$mbox = imap_open($serverarg, $serveruser, $serverpw)
or die("can't connect: " . imap_last_error());



$list = imap_lsub($mbox, $serverarg, "*");
if (is_array($list)) {
	foreach ($list as $val) {
		$utdec = imap_utf7_decode($val);
        $utdec = preg_replace("/{(.*)}/","",$utdec);
        $utdec = preg_replace("/\//","",$utdec,1);
        $utdec = preg_replace("/INBOX/","",$utdec,1);
        $utdec = preg_replace("/\./","",$utdec,1);
		imap_reopen($mbox, $val);

		$check = imap_status($mbox,$val,SA_UNSEEN);
		$colorstr = "";
		$dotcode = "";
		if ($check->unseen > 0) {
			$dotcode = " background-image: url(./iui/dot.png);background-repeat:no-repeat;background-position:2px 12px;";
			$colorstr = "color:#194fdb;font-weight:bold;";
		}

		if ($utdec == "") $utdec = "Posteingang";

		$mboxname = substr($val,strpos($val,'}')+1,strlen($val));

		echo "<li style=\"".$dotcode."\"><a href=\"folderlist.php?acc=".$accnr."&folder=".imap_utf7_decode($mboxname)."&offset=0\"  style=\"padding-left:25px;".$colorstr."\" type=\"folderlist\">".$utdec;

		if ($hdr = imap_check($mbox)) { $nummsg = $hdr->Nmsgs; } else { $nummsg = 0;}

		echo "<span style=\"padding-left:8px;font-size:11px;\">(".$nummsg.")</span>";


		if ($check->unseen > 0) {
			echo "<span style=\"padding-left:8px;font-size:18px;\">(".$check->unseen.")</span>";
		}


		echo "</a></li>\n";

	}
} else {
	echo "<li ><a href=\"folderlist.php?acc=".$accnr."&folder=INBOX&offset=0\" style=\"padding-left:25px;\">Posteingang</a></li>";
}

imap_close($mbox);
			  ?>
			  
			  </ul>
