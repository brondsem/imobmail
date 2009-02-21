<?php

/*
 +-----------------------------------------------------------------------+
 | sendmail.php                                                          |
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


require_once "Net/SMTP.php";
include('config.php');


$from = $SMTP_SENDERADDRESS;
$fromname = $SMTP_SENDERNAME;
$to = $_POST["to"];
$cc = $_POST["cc"];
$subject = $_POST["subj"];
$body = $_POST["textbody"];



$smtp_host_conn = new Net_SMTP($SMTP_SERVER);

$result = $smtp_host_conn->connect($smtp_timeout);
if (PEAR::isError($result))
{
	$smtp_host_conn  = null;
	$smtp_host_conn->rset();
	$smtp_host_conn->disconnect();
	die( "Connection failed: ".$result->getMessage() );

}

$result = $smtp_host_conn->auth($SMTP_USER, $SMTP_PASSWORD, $smtp_auth_type);

if (PEAR::isError($result))
{
	$smtp_host_conn->rset();
	$smtp_host_conn->disconnect();
	die("Authentication failure: ".$result->getMessage());

}

if (PEAR::isError($smtp_host_conn->mailFrom($from))) {
	die("Unable to set sender to <$from>\n");
}


$toadresses = str_replace(","," ",$to);
$toadressesarray = explode(" ",$toadresses);

$ccadresses = str_replace(","," ",$cc);
$ccadressesarray = explode(" ",$ccadresses);

$sendarray = array_merge($toadressesarray,$ccadressesarray);



function empty_arraypart($part) {
	return strpos($part,"@");
}


$fromheader = "From: ".$fromname." <".$from.">";
$subjectheader = "Subject: ".$subject;
$toheader = "To: ".implode(", ",array_filter($toadressesarray,"empty_arraypart"));

if ($cc) {
	$ccheader = "Cc: ".implode(", ",array_filter($ccadressesarray,"empty_arraypart"));
}

$mailerheader = "User-Agent: iMobMail 0.1beta";



$headers = $subjectheader."\n".$fromheader."\n".$toheader."\n".$mailerheader."\n".$ccheader."\n";


/* Address the message to each of the recipients. */
foreach ($sendarray as $to) {
	if (!strpos($to,"@")) continue;
	if (PEAR::isError($res = $smtp_host_conn->rcptTo($to))) {
		die("Unable to add recipient <$to>: " . $res->getMessage() . "\n");
	}
}

$data = $headers."\r\n".$body;
unset($subject,$body);



/* Set the body of the message. */
if (PEAR::isError($smtp_host_conn->data($data))) {
	die("Unable to send data\n");
}



$smtp_host_conn->disconnect();

// echo $smtp_host_conn->getResponse()
			    ?>
    
    <ul id="done" title="Mail" selected="true">
        <li>Mail wurde verschickt!</li>
        </ul>
