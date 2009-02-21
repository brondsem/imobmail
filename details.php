<?php
/*
 +-----------------------------------------------------------------------+
 | details.php                                                           |
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
$showhtmlbody = isset($_GET["showhtml"]);
$noul = isset($_GET["noul"]);
$mbox = imap_open($serverarg.$imapfolder, $serveruser, $serverpw)
or die("can't connect: " . imap_last_error());

imap_setflag_full($mbox, $msgno, "\\Seen");


$showhtml = $_COOKIE['tmailprefshowhtml'];
$linksclickable = ($_COOKIE['tmailprefsmakeclickable'] == false) ? "true" : $_COOKIE['tmailprefsmakeclickable'];
$transformsmilies = ($_COOKIE['tmailprefsgraphsmil'] == false) ? "true" : $_COOKIE['tmailprefsgraphsmil'];


$mailHeader = @imap_headerinfo($mbox, $msgno);

$from = make_email_string($mailHeader->from);
//$from = str_replace("<","&lt;",$from);
$to = utf8_encode(make_email_string($mailHeader->to));
$subject = strip_tags(imap_utf8(_decodeHeader($mailHeader->subject)));


$date = date("D, d.m.Y, H:i",strtotime($mailHeader->MailDate));


function make_email_string($input) {

	$mailarr = Array();
	if ($input == "") return;
	foreach ($input as $fields) {
		$name = trim(_decodeHeader($fields->personal));
		$name = str_replace("\"","",$name);
		$name = str_replace("'","",$name);

		$mailbox = $fields->mailbox;
		$host = $fields->host;

		$emailaddr = $mailbox.'@'.$host;

		if ($name == "") $name = $emailaddr;

		if ($name == "") continue;



		array_push($mailarr, "<a onclick=\"JavaScript:document.getElementById('smto').value = '".$emailaddr."';\" href=\"#sendmail\" style=\"display:inline;background:transparent;padding:1px;margin:1px;text-shadow: rgba(0, 0, 0, 0.4) 0px 0px 10px;\">".$name."</a>");
	}
	return implode(", ",$mailarr);

}



function get_email_adresses($input) {

	$mailarr = Array();
	if ($input == "") return;
	foreach ($input as $fields) {

		$mailbox = $fields->mailbox;
		$host = $fields->host;
		$emailaddr = $mailbox.'@'.$host;

		array_push($mailarr, $emailaddr);
	}

	return implode(", ",$mailarr);


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

function get_mime_type(&$structure) {
	$primary_mime_type = array("TEXT", "MULTIPART","MESSAGE", "APPLICATION", "AUDIO","IMAGE", "VIDEO", "OTHER");
	if($structure->subtype) {
		return $primary_mime_type[(int) $structure->type] . '/' .$structure->subtype;
	}
	return "TEXT/PLAIN";
}
function get_part($stream, $msg_number, $mime_type, $structure = false,$part_number    = false) {

	if(!$structure) {
		$structure = imap_fetchstructure($stream, $msg_number);
	}
	if($structure) {
		if($mime_type == get_mime_type($structure)) {
			if(!$part_number) {
				$part_number = "1";
			}
			$text = imap_fetchbody($stream, $msg_number, $part_number);
			if($structure->encoding == 3) {
				return imap_base64($text);
			} else if($structure->encoding == 4) {
				return quoted_printable_decode($text);
			} else if($structure->encoding == 1) {
				return imap_utf8($text);
			} else if($structure->encoding == 0) {
				return $text;
			} else {
				return imap_utf8($text);
			}
		}

		if($structure->type == 1) /* multipart */ {
			while(list($index, $sub_structure) = each($structure->parts)) {
				if($part_number) {
					$prefix = $part_number . '.';
				}
				$data = get_part($stream, $msg_number, $mime_type, $sub_structure,$prefix .    ($index + 1));
				if($data) {
					return $data;
				}
			} // END OF WHILE
		} // END OF MULTIPART
	} // END OF STRUTURE
	return false;
} // END OF FUNCTION


// GET TEXT BODY
$dataTxt = get_part($mbox, $msgno, "TEXT/PLAIN");

// GET HTML BODY
$dataHtml = get_part($mbox, $msgno, "TEXT/HTML");

/*if ($dataHtml != "") {
$msgBody = $dataTxt;
if ($msgBody == "") {
$msgBody = $dataHtml;
} else  { $dataHtml = ""; $msgBody = htmlentities("htmlentities!!   ".$msgBody); }
$mailformat = "html";
} else {
$msgBody = ereg_replace("\n","<br>",$dataTxt);
$mailformat = "text";
}*/

if ($showhtml != "true") {

	$msgBody = $dataTxt;
	$mailformat = "text";
	if ($dataTxt == "") {
		$msgBody = $dataHtml;

		$mailformat = "html";
	}

} else {

	$msgBody = $dataHtml;
	$mailformat = "html";
	if ($dataHtml == "") {
		$msgBody = $dataTxt;

		$mailformat = "text";

	}


}
/*  $msgBody = utf8_encode($dataHtml);
$mailformat = "html";
if ($dataHtml == "") {
$msgBody = $dataText;

$mailformat = "text";
} */

// To out put the message body to the user simply print $msgBody like this.

/*   if ($mailformat == "text") {
echo "<html><head><title>Messagebody</title></head><body    bgcolor=\"white\">$msgBody</body></html>";
} else {
echo $msgBody; // It contains all HTML HEADER tags so we don't have to make them.
}*/

function transformHTML($str) {
	if ((strpos($str,"<HTML") < 0) || (strpos($str,"<html")    < 0)) {
		$makeHeader = "<html><head><meta http-equiv=\"Content-Type\"    content=\"text/html; charset=utf-8\"><style type=\"text/css\"> body { -webkit-text-size-adjust: none } </style></head>\n";
		if ((strpos($str,"<BODY") < 0) || (strpos($str,"<body")    < 0)) {
			$makeBody = "\n<body>\n";
			$str = $makeHeader . $makeBody . $str ."\n</body></html>";
		} else {
			$str = $makeHeader . $str ."\n</html>";
		}
	} else {
		$str = "<style type=\"text/css\"> body { -webkit-text-size-adjust: none } </style><meta http-equiv=\"Content-Type\" content=\"text/html;    charset=utf-8\">\n". $str;
	}
	return $str;
}

if ($mailformat == "html") {
	$msgBody = utf8_encode(transformHTML(($dataHtml)));
} else {
	$msgBody = utf8_encode(strip_tags($msgBody));
	// $msgBody = htmlentities($msgBody);


	$smiley_wink = "<img src=\"smilies/emoticon_wink.png\" class=\"smiley\">";
	$smiley_smile = "<img src=\"smilies/emoticon_smile.png\" class=\"smiley\">";
	$smiley_tongue = "<img src=\"smilies/emoticon_tongue_out.png\" class=\"smiley\">";
	$smiley_cry = "<img src=\"smilies/emoticon_cry.png\" class=\"smiley\">";
	$smiley_frown = "<img src=\"smilies/emoticon_frown.png\" class=\"smiley\">";
	$smiley_cool = "<img src=\"smilies/emoticon_cool.png\" class=\"smiley\">";

	if ($transformsmilies == "true") {
		$msgBody = str_replace(";)",$smiley_wink,$msgBody);
		$msgBody = str_replace(";-)",$smiley_wink,$msgBody);
		$msgBody = str_replace(":)",$smiley_smile,$msgBody);
		$msgBody = str_replace(":-)",$smiley_smile,$msgBody);
		$msgBody = str_replace(":-P",$smiley_tongue,$msgBody);
		$msgBody = str_replace(":-p",$smiley_tongue,$msgBody);
		$msgBody = str_replace(":P",$smiley_tongue,$msgBody);
		$msgBody = str_replace(":p",$smiley_tongue,$msgBody);
		$msgBody = str_replace(";(",$smiley_cry,$msgBody);
		$msgBody = str_replace(";-(",$smiley_cry,$msgBody);
		$msgBody = str_replace(":-(",$smiley_frown,$msgBody);
		$msgBody = str_replace(":(",$smiley_frown,$msgBody);
		$msgBody = str_replace("8-)",$smiley_cool,$msgBody);
	}



	if ($linksclickable == "true") {

		$msgBody = preg_replace("/([^\w\/])(\*[a-z0-9\-]+\*)/i","$1<strong>$2</strong>",    $msgBody);
		$msgBody = preg_replace("/([^\w\/])(\_[a-z0-9\-]+\_)/i","$1<u>$2</u>",    $msgBody);
		$msgBody = preg_replace("/([^\w\/])(www\.[a-z0-9\-]+\.[a-z0-9\-]+)/i","$1http://$2",    $msgBody);

		$msgBody = preg_replace("/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i","<A    TARGET=\"_blank\" HREF=\"$1\">$1</A>", $msgBody);
		$msgBody = preg_replace("/([\w-?&;#~=\.\/]+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?))/i","<A     ONCLICK=\"JavaScript:document.getElementById('smto').value = '$1';\" HREF=\"#sendmail\">$1</A>",$msgBody);
	}

	$msgBody = nl2br($msgBody);
}







$body = $msgBody;

if ($mailformat == "html" && !$showhtmlbody) {

	$body = "<iframe id=\"htmliframe$imapfolder$msgno\" name = \"htmliframe$imapfolder$msgno\" src=\"details.php?showhtml&acc=".$accnr."&folder=$imapfolder&msgid=$msgno\" style=\"width:100%;border: 1px solid black;\">&nbsp;</iframe>";

}

if (strtolower((substr($subject,0,3))) == "re:" || strtolower((substr($subject,0,3))) == "aw:") {
	$cleansubject = substr($subject,4,strlen($subject));
} else  $cleansubject = $subject;
$cleansubject = str_replace("\"","",$cleansubject);
$cleansubject = str_replace("'","",$cleansubject);

//$from = str_replace("\"","",$from);
//$from = str_replace("'","",$from);

if ($mailHeader->Deleted == 'D') {
	$delstyle = " text-decoration: line-through; ";
} else {
	$delstyle = "";
}

if (!$showhtmlbody) {

	$cleanmailfrom = str_replace("\"","",$mailHeader->fromaddress);
	$cleanmailfrom = str_replace("'","",$cleanmailfrom);

	if (!$noul) {
		echo "<ul id=\"msg\" title=\"$subject\">";
		$hidesub = " display:none; ";
	}

	echo <<<END
<li id="subjli" style="$hidesub padding-top:5px;padding-bottom:5px;font-size:15px;text-align:center;">$subject</li>
<li style="$delstyle padding-top:4px;padding-bottom:4px;font-size:14px;">Von: $from</li>
<li style="$delstyle padding-top:3px;padding-bottom:3px;font-size:13px;font-weight:normal;">An: $to</li>
END;

	$cc = utf8_encode(_decodeHeader(make_email_string($mailHeader->cc)));

	if ($cc != "") {


		echo <<<END
<li style="padding-top:3px;padding-bottom:3px;font-size:13px;font-weight:normal;">CC: $cc</li>
END;

}


echo <<<END
<li style="padding-top:3px;padding-bottom:3px;font-size:13px;font-weight:normal;border-bottom: 1px solid #000000;">$date<img id="attimg" src="./iui/att.png" style="position:absolute;right:1px;bottom:1px;display:none;"></li>
<li style="resize:both;padding:1px;margin:4px;border: 1px solid #e8e8e8;"><div style="padding:2px;margin:0px;overflow:auto;width:99%;font-family:monospace; font-size:13px; font-weight:normal;min-height:220px;" id="msgbox">

$body


</div></li>
END;
} else {

	$body = str_replace('<a ',"<a target=\"_blank\" ",$body);
	$body = str_replace('<A ',"<a target=\"_blank\" ",$body);

	echo $body;

}




$struct = imap_fetchstructure($mbox,$msgno);
$contentParts = count($struct->parts);

if ($contentParts >= 2) {
	for ($i=2;$i<=$contentParts;$i++) {
		$att[$i-2] = imap_bodystruct($mbox,$msgno,$i);
	}
	for ($k=0;$k<sizeof($att);$k++) {
		/*if ($att[$k]->parameters[0]->value == "us-ascii" || $att[$k]->parameters[0]->value    == "US-ASCII") {
		if ($att[$k]->parameters[1]->value != "") {
		$selectBoxDisplay[$k] = imap_utf8($att[$k]->parameters[1]->value);
		}
		} elseif ($att[$k]->parameters[0]->value != "iso-8859-1" &&    $att[$k]->parameters[0]->value != "ISO-8859-1") {
		$selectBoxDisplay[$k] = imap_utf8($att[$k]->parameters[0]->value);
		}*/
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


if (!$showhtmlbody) {

	if (sizeof($selectBoxDisplay) > 0) {
		echo "<li style=\"padding-top:7px;padding-bottom:5px;font-size:15px;border-top:2px solid;\">Anh&auml;nge:";
		for ($j=0;$j<sizeof($selectBoxDisplay);$j++) {
			echo "\n<div style=\"padding-left:22px;padding-top:3px;padding-bottom:3px;color:#194fdb;\"><a href=\"getatt.php?acc=".$accnr."&folder=".$imapfolder."&filename=".$selectBoxDisplay[$j]."&msgid=".$msgno."&att=".$j."\" target=\"_blank\">". $selectBoxDisplay[$j]    ."</a></div>";
		}
		echo "</li>";

		echo "<script type=\"text/javascript\">document.getElementById('attimg').style.display='inline';</script>";

	}
	$MC = imap_check($mbox);
	for ($i=($msgno+1);$i<=($msgno+30);$i++) {
		if ($i>$MC->Nmsgs) {
			break;
		}
		$nextnr = @imap_headerinfo($mbox, $i);

		if ($nextnr->Deleted == "D") {  continue; } else { $nextmsg = $i; break;};


	}

	for ($i=($msgno-1);$i>($msgno-30);$i--) {
		if ($i<=0) break;
		$nextnr = @imap_headerinfo($mbox, $i);

		if ($nextnr->Deleted == "D") {  continue; } else { $prevmsg = $i; break;};

	}


	$msgno = $msgno;


	if ($prevmsg) {

		echo <<<END
<a class="button" id="msgprev" onclick="document.getElementById('pageTitle').innerHTML = '$prevmsg von $MC->Nmsgs';" href="details.php?noul&acc=$accnr&folder=$imapfolder&msgid=$prevmsg" target="_replaceother" replacet = "msg" style="position:fixed;right:45px;width:18px;"><img src="./iui/down.png" style="position:absolute;top:8px;"></a>
END;

} else {
	echo <<<END

<a class="button" target="_none" style="opacity:0.5;position:fixed;right:5px;width:18px;"><img src="./iui/down.png" style="position:absolute;top:8px;"></a>
END;
}


if ($nextmsg) {

	echo <<<END
<a class="button" id="msgnext" onclick="document.getElementById('pageTitle').innerHTML = '$nextmsg von $MC->Nmsgs';" href="details.php?noul&acc=$accnr&folder=$imapfolder&msgid=$nextmsg" target="_replaceother" replacet = "msg" style="position:fixed;right:5px;width:18px;"><img src="./iui/up.png" style="position:absolute;top:8px;"></a>
END;
}  else {

	echo <<<END

<a class="button" target="_none" style="opacity:0.5;position:fixed;right:5px;width:18px;"><img src="./iui/up.png" style="position:absolute;top:8px;"></a>
END;

}

$fromadr = get_email_adresses($mailHeader->from);
$ccadr = @get_email_adresses(array_merge($mailHeader->to,$mailHeader->cc));

if ($mailformat == "html") $iframeid = "htmliframe".$imapfolder.$msgno; else $iframeid = "";

echo <<<END
<li style=" background: url(./iui/toolbar.png) #6d84a2 repeat-x; margin-top:20px;min-height:32px;height:32px;top:324px:"><table width="100%" align="center"><td width="25%" align="center"><a onclick="sendReply('$iframeid','$cleansubject','$fromadr');" href="#sendmail"><img src="./iui/replyico.png"></a></td><td width="25%" align="center"><a onclick="sendReply('$iframeid','$cleansubject','$fromadr','$ccadr');" href="#sendmail"><img src="./iui/replyallico.png"></a></td><td width="25%" align="center"><a onclick="sendFwd('$iframeid','$cleansubject','$fromadr');" href="#sendmail"><img src="./iui/fwdico.png"></a></td><td width="25%" align="center"><a href="delmsg.php?acc=$accnr&folder=$imapfolder&msgid=$msgno" id = "delmsg"><img src="./iui/delico.png"></a></td></table></li>
END;
}
if (!$noul) {
	echo "</ul>";
}




END;

imap_close($mbox);

?>
