<?php
/*
 +-----------------------------------------------------------------------+
 | folderlist.php                                                        |
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


$imapfolder = $_GET["folder"];
$offset = 0;
$offset = $_GET["offset"];
$loadprev = isset($_GET["prev"]);

$fname = imap_utf7_decode($imapfolder);
$fname = preg_replace("/INBOX/","",$fname);
$fname = preg_replace("/\//","",$fname,1);
if ($fname == "") $fname = "Posteingang";

if ($offset == 0) {
	echo " <ul id=\"folderlist\" title=\"".$fname."\"> ";
}


function _decodeHeader($input)
{
	// Remove white space between encoded-words
	$input = preg_replace('/(=\?[^?]+\?(q|b)\?[^?]*\?=)(\s)+=\?/i', '\1=?', $input);

	// For each encoded-word...
	while (preg_match('/(=\?([^?]+)\?(q|b)\?([^?]*)\?=)/i', $input, $matches)) {

		$encoded  = $matches[1];
		$charset  = $matches[2];
		$encoding = $matches[3];
		$text     = $matches[4];

		switch (strtolower($encoding)) {
			case 'b':
				$text = base64_decode($text);
				break;

			case 'q':
				$text = str_replace('_', ' ', $text);
				preg_match_all('/=([a-f0-9]{2})/i', $text, $matches);
				foreach($matches[1] as $value)
				$text = str_replace('='.$value, chr(hexdec($value)), $text);
				$text = utf8_encode($text);
				break;
		}

		$input = str_replace($encoded, $text, $input);
	}

	return $input;
}

$mbox = imap_open($serverarg.$imapfolder, $serveruser, $serverpw)
or die("can't connect: " . imap_last_error());

$MC = imap_check($mbox);
if ($offset == 0) $nummsg = $MC->Nmsgs; else $nummsg = $offset-26;

$start = $nummsg;
$end = $nummsg-25;

if ($start+1 != $MC->Nmsgs && $loadprev) {

	echo "<li id=\"prevLoader\"><a href=\"folderlist.php?prev&acc=".$accnr."&folder=".$imapfolder."&offset=".($start+52)."\" target=\"_replace\">25 vorige Nachrichten laden...</a></li>";

}
$showdeleted = $_COOKIE['tmailprefshowdel'];


if ($end <= 0) $end = 1;

$msgsh = imap_fetch_overview($mbox,$start.":".$end);

foreach(array_reverse($msgsh) as $headrs) {

	if ($showdeleted != "true") {
		if ($headrs->deleted) continue;
	} else {
		if ($headrs->deleted) $delstyle = " text-decoration:line-through;opacity:.35; "; else $delstyle = "";
	}

	$mail_headerinfo = @imap_headerinfo($mbox, $headrs->msgno);
	$mail_senddate = $mail_headerinfo->MailDate;

	$mailstruct = imap_fetchstructure($mbox, $headrs->msgno);
	$selectBoxDisplay = "";
	$att = "";
	$contentParts = count($mailstruct->parts);
	if ($contentParts >= 2) {

		for ($i=2;$i<=$contentParts;$i++) {
			$att[$i-2] = imap_bodystruct($mbox,$headrs->msgno,$i);
		}
		for ($k=0;$k<sizeof($att);$k++) {

			$p = $att[$k];
			if (count($p->dparameters)>0){
				foreach ($p->dparameters as $dparam){
					if ((strtoupper($dparam->attribute)=='NAME') ||(strtoupper($dparam->attribute)=='FILENAME')) $selectBoxDisplay[$k]=$dparam->value;
				}
			}
			//if no filename found
			if ($filename==''){
				// if there are any parameters present in this part
				if (count($p->parameters)>0){
					foreach ($p->parameters as $param){
						if ((strtoupper($param->attribute)=='NAME') ||(strtoupper($param->attribute)=='FILENAME')) $selectBoxDisplay[$k]=$param->value;
					}
				}
			}


		}
		if ($p->encoding==3)$selectBoxDisplay[$k]=imap_base64($selectBoxDisplay[$k]);
		if ($p->encoding==4)$selectBoxDisplay[$k]=imap_qprint($selectBoxDisplay[$k]);
	}



	$sentcheck = imap_utf7_decode($imapfolder);
	$sentcheck = preg_replace("/{(.*)}INBOX/","",$sentcheck);
	$sentcheck = preg_replace("/\//","",$sentcheck,1);
	if ($sentcheck == "Sent") {  $subj = $headrs->to; } else {
		$subj = $headrs->from;
	}
	$replstr = "";
	if (($headrs->answered) && (!$headrs->seen)) {
		$replstr = " url(./iui/dot.png), url(./iui/repl.png); ";
	}

	if (($headrs->answered) && ($headrs->seen)) {
		$replstr = "url(./iui/repl.png); ";
	}

	if ((!$headrs->answered) && (!$headrs->seen)) {
		$replstr = "url(./iui/dot.png); ";
	}



	$attpadd = "";
	$attimg = "";

	if (sizeof($selectBoxDisplay) > 1) {

		$attpadd = "padding-left:35px;";
		$attimg = "<img src=\"./iui/att.png\" style=\"position:absolute;left:15px;top:10px;\">";
	}



	$seenstr  = "background-image: ".$replstr." background-repeat:no-repeat;background-position:2px 12px;";
	$colorstr = "";
	if (!$headrs->seen) { $colorstr = "color:#194fdb;";}

	$msize = $headrs->size/1024;

	if ($msize > 1024) { $msize = round($msize / 1024,1) . " MB"; } else  { $msize = round($msize,1) . " kB"; }

	$datestr = date("d.m.y",strtotime($mail_senddate));
	setlocale (LC_TIME, 'de_DE');
	if (date("z")-date("z",strtotime($mail_senddate)) < 6) $datestr = strftime("%a %H:%M",strtotime($mail_senddate));
	if (date("d.m.y") == date("d.m.y",strtotime($mail_senddate))) $datestr = date("H:i",strtotime($mail_senddate));

	$subj = str_replace("'","",$subj);
	$subj = str_replace("\"","",$subj);

	print "<li name=\"msgli\" id=\"msg".utf8_encode($headrs->msgno)."\" style=\" ".$delstyle.$seenstr.$colorstr."\"><div name=\"msglistentry\" style=\"position:absolute;left:8px;top:16px;display:none;z-index:999\" ><a href=\"delmsg.php?acc=".$accnr."&folder=".$imapfolder."&msgid=".$headrs->msgno."\" type = \"delmsg\"><img src=\"./iui/stop.png\"  ></a></div><a onClick=\"JavaScript:document.getElementById('msg".$headrs->msgno."').style.color='black';document.getElementById('rb').style.display='none';showDelIcons(false);showdelicons=false;\" href=\"details.php?acc=".$accnr."&folder=".$imapfolder."&msgid=".$headrs->msgno."\" style=\"padding-left:25px;padding-right:45px;".$attpadd."\">".imap_utf8(_decodeHeader($subj))."<div align=\"right\" style=\"color:#194fdb;font-size:14px;font-weight:bold;position:absolute;right:10px;top:3px;\">".$datestr."</div><div style=\"font-size:11px;position:absolute;right:10px;bottom:3px;\">".$msize."</div>
	<div style=\"font-weight:normal;font-size:14px;padding-right:25px;\">".imap_utf8(_decodeHeader($headrs->subject))."</div>
	".$attimg."
	</a></li>\n";

}


imap_close($mbox);

if (($end-25)>0 && !$loadprev)
{

			  ?>
			  <li id="nextLoader"><a onclick = "removeOldListentries('<?php echo  $MC->Nmsgs;?>','<?php echo $end ?>','<?php echo $imapfolder ?>','<?php echo $offset ?>');" href="folderlist.php?acc=<?php echo $accnr; ?>&folder=
<?php 


echo $imapfolder."&offset=".$start;

?>
" target="_replace" type="listupdate">25 weitere Nachrichten laden...</a></li>
			 
			 <?php
}
?>
<a class="button" id="delmark" href="#"  target="_replaceother" style="position:fixed;right:5px;">Edit</a>
<?php

if ($offset == 0) { echo "</ul>"; }



			 ?>
