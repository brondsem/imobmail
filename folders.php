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
    <ul id="folders" title="<?php echo l('Ordner')?>">
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
    sort($list);
    $folder_output = array('read'=>'', 'unread'=>'');
	foreach ($list as $val) {
        $folder_type = "read";
		$utdec = imap_utf7_decode($val);
        $utdec = preg_replace("/{(.*)}/","",$utdec);
        $utdec = preg_replace("/\//","",$utdec,1);
        $utdec = preg_replace("/INBOX/","",$utdec,1);
        $utdec = preg_replace("/\./","",$utdec,1);
		imap_reopen($mbox, $val);
		$check = imap_status($mbox,$val,SA_UNSEEN|SA_MESSAGES);
		$colorstr = "";
		$dotcode = "";
		if ($check->unseen > 0) {
            if ($FOLDERS_UNREAD_FIRST) $folder_type = 'unread';
			$dotcode = " background-image: url(./iui/dot.png);background-repeat:no-repeat;background-position:2px 12px;";
			$colorstr = "color:#194fdb;font-weight:bold;";
		}

		if ($utdec == "") $utdec = l("Posteingang");

		$mboxname = substr($val,strpos($val,'}')+1,strlen($val));

		$folder_output[$folder_type] .= "<li style=\"".$dotcode."\"><a href=\"folderlist.php?acc=".$accnr."&folder=".imap_utf7_decode($mboxname)."&offset=0\"  style=\"padding-left:25px;".$colorstr."\" type=\"folderlist\">".$utdec;
		$folder_output[$folder_type] .= "<span style=\"padding-left:8px;font-size:11px;\">(".$check->messages.")</span>";


		if ($check->unseen > 0) {
			$folder_output[$folder_type] .= "<span style=\"padding-left:8px;font-size:18px;\">(".$check->unseen.")</span>";
		}


		echo "</a></li>\n";

	}
    echo $folder_output['unread'];
    echo $folder_output['read'];
} else {
	echo "<li ><a href=\"folderlist.php?acc=".$accnr."&folder=INBOX&offset=0\" style=\"padding-left:25px;\">".l('Posteingang')."</a></li>";
}
imap_close($mbox);
			  ?>
			  
			  </ul>
