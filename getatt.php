<?

/*
 +-----------------------------------------------------------------------+
 | getatt.php                                                            |
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
$file = $_GET["att"];

$mbox = imap_open($serverarg.$imapfolder, $serveruser, $serverpw)
or die("can't connect: " . imap_last_error());


$fileContent = imap_fetchbody($mbox,$msgno,$file+2);

$struct =  imap_fetchstructure($mbox,$msgno);

$ar =  $struct->parts;
$p = $ar[$file+1];
if (count($p->dparameters)>0){
	foreach ($p->dparameters as $dparam){
		if ((strtoupper($dparam->attribute)=='NAME') ||(strtoupper($dparam->attribute)=='FILENAME')) $filename=$dparam->value;
	}
}
//if no filename found
if ($filename==''){
	// if there are any parameters present in this part
	if (count($p->parameters)>0){
		foreach ($p->parameters as $param){
			if ((strtoupper($param->attribute)=='NAME') ||(strtoupper($param->attribute)=='FILENAME')) $filename=$param->value;
		}
	}
}


if (count($p->parameters)>0){
	foreach ($p->parameters as $param){
		if ((strtoupper($dparam->attribute)=='CONTENT-TYPE') ||(strtoupper($dparam->attribute)=='CONTENT-TYPE')) $conttype=$param->value;
	}
}

$strFileName = $filename;
$strFileType = strrev(substr(strrev($strFileName),0,4));
downloadFile($strFileType,$strFileName,$fileContent);





function downloadFile($strFileType,$strFileName,$fileContent) {
	$ContentType = "application/octet-stream";

	if ($strFileType == ".asf")
	$ContentType = "video/x-ms-asf";
	if ($strFileType == ".avi")
	$ContentType = "video/avi";
	if ($strFileType == ".doc")
	$ContentType = "application/msword";
	if ($strFileType == ".zip")
	$ContentType = "application/zip";
	if ($strFileType == ".xls")
	$ContentType = "application/vnd.ms-excel";
	if ($strFileType == ".gif")
	$ContentType = "image/gif";
	if ($strFileType == ".jpg" || $strFileType == "jpeg")
	$ContentType = "image/jpeg";
	if ($strFileType == ".png")
	$ContentType = "image/png";
	if ($strFileType == ".wav")
	$ContentType = "audio/wav";
	if ($strFileType == ".mp3")
	$ContentType = "audio/mp3";
	if ($strFileType == ".mov")
	$ContentType = "video/quicktime";
	if ($strFileType == ".mpg" || $strFileType == "mpeg")
	$ContentType = "video/mpeg";
	if ($strFileType == ".rtf")
	$ContentType = "application/rtf";
	if ($strFileType == ".htm" || $strFileType == "html")
	$ContentType = "text/html";
	if ($strFileType == ".xml")
	$ContentType = "text/xml";
	if ($strFileType == ".eml")
	$ContentType = "text/plain";
	if ($strFileType == ".xsl")
	$ContentType = "text/xsl";
	if ($strFileType == ".css")
	$ContentType = "text/css";
	if ($strFileType == ".php")
	$ContentType = "text/php";
	if ($strFileType == ".asp")
	$ContentType = "text/asp";
	if ($strFileType == ".pdf")
	$ContentType = "application/pdf";
	if ($strFileType == ".m4v")
	$ContentType = "video/x-m4v";
	if ( strstr($conttype,'application') == $conttype ) $conttype = $ContentType;


	header ('Content-type: '.$conttype);
	header ('Content-Disposition: attachment; filename="'.basename($strFileName).'"');


	if (substr($ContentType,0,4) == "text") {
		echo imap_qprint($fileContent);
	} else {
		header ("Content-Length: ".mb_strlen(imap_base64($fileContent),'8bit'));
		echo imap_base64($fileContent);
	}

	imap_close($mbox);
}
?>