<?php

/*
 +-----------------------------------------------------------------------+
 | addressbook.php                                                       |
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
require_once 'Contact_Vcard_Parse.php';


$parse = new Contact_Vcard_Parse();
$cardinfo = $parse->fromFile($ADDRESSBOOK);


if (isset($_GET['showimg'])) {

	header("Content-type: image/jpeg");
	header("Content-Disposition: inline filename=\"".md5($cardinfo[$_GET['cardnr']]["FN"][0]["value"][0][0]).".jpg\"");

	$pic = $cardinfo[$_GET['cardnr']]["PHOTO"][0]['value'][0][0];
	echo base64_decode($pic);
	exit;
}


if (isset($_GET['showdet'])) {

	$cardno = $_GET['cardno'];
	$nickname = htmlentities($cardinfo[$cardno][FN][0][value][0][0]);
	$orgname = htmlentities($cardinfo[$cardno][ORG][0][value][0][0]);

	if ($orgname == $nickname) $orgname = "";

	$sur_name = htmlentities($cardinfo[$cardno][N][0][value][0][0]);
	$first_name = htmlentities($cardinfo[$cardno][N][0][value][1][0]);

	if ($sur_name == "" || $first_name == "") $sur_name = $nickname;

	$has_pic = ($cardinfo[$cardno][PHOTO][0][value][0][0] != "");

	if ($has_pic) $imgurl = "addressbook.php?showimg&cardnr=".$cardno; else $imgurl = "./iui/nobody.jpg";

	echo <<<END

    <div id="adddetail" title="{$l('Details')}" class="panel">
        
        <h2 style="padding-top:40px;padding-bottom:40px;padding-left:90px;">$first_name $sur_name<br/>$orgname</h2><div  style="position:absolute;top:20px;left:10px;border: 1px solid black;background:#ffffff;-webkit-border-radius:6px;height:75px;width:75px;" valign="absmiddle" align="center"><img src="$imgurl" style="padding-top:2px;left:2px;" heigth=70 width=70></div>
END;

	$mailarr = $cardinfo[$cardno][EMAIL];

	if (count($mailarr)>0) {

		echo <<<END
	 <h2>{$l('eMail')}</h2>
	 <fieldset>
END;

		foreach ($mailarr as $mailinfo) {

			$mailtype = $mailinfo[param][TYPE][1];

			if ($mailtype == "WORK") $mailtype = l("Arbeit");
			if ($mailtype == "HOME") $mailtype = l("Privat");

			$mailadd = $mailinfo[value][0][0];

			echo <<<END
	<div class="row">
		<label>$mailtype</label>
		<div class="addr" valign="middle"><a onclick="JavaScript:document.getElementById('smto').value = '$mailadd';" href="#sendmail">$mailadd</a></div>
	</div>
END;

}

echo <<<END
	</fieldset>
END;

}


$keys_only_array = array_flip(array_keys($cardinfo[$cardno]));


//homepage:
$homepagekeyarr = array();
foreach (array_keys($keys_only_array) as $keys) {
	if (strstr($keys,".URL")) array_push($homepagekeyarr, $keys);
}

if (count($homepagekeyarr)>0) {


	echo <<<END
	 <h2>{$l('Internet')}</h2>
	 <fieldset>
END;
	foreach ($homepagekeyarr as $homepagekey) {
		$homepagearr = $cardinfo[$cardno][$homepagekey];

		if (count($homepagearr)>0) {


			foreach ($homepagearr as $homepage) {



				$url = $homepage[value][0][0];

				echo <<<END
	<div class="row">
		<label>{$l('URL')}</label>
		<div class="addr" valign="middle"><a href="$url" target="_blank">$url</a></div>
	</div>
END;

}
}
}
echo <<<END
	</fieldset>
END;


}




$telarr = $cardinfo[$cardno][TEL];

if (count($telarr)>0) {

	echo <<<END
	 <h2>{$l('Telefon')}</h2>
	 <fieldset>
END;

	foreach ($telarr as $telinfo) {

		$teltype = $telinfo[param][TYPE][0];

		if ($teltype == "WORK") $teltype = l("Arbeit");
		if ($teltype == "HOME") $teltype = l("Privat");
		if ($teltype == "CELL") $teltype = l("Mobil");

		$telnr = htmlentities($telinfo[value][0][0]);

		echo <<<END
	<div class="row">
		<label>$teltype</label>
		<div class="addr" valign="middle"><a href="tel:$telnr" target="_blank">$telnr</a></div>
	</div>
END;

}

echo <<<END
	</fieldset>
END;

}





//address:
$addresskeyarr = array();
foreach (array_keys($keys_only_array) as $keys) {
	if (strstr($keys,".ADR")) array_push($addresskeyarr, $keys);
}

if (count($addresskeyarr)>0) {


	echo <<<END
	 <h2>{$l('Adresse')}</h2>
	 <fieldset>
END;
	foreach ($addresskeyarr as $addresskey) {
		$adressarr = $cardinfo[$cardno][$addresskey];

		if (count($adressarr)>0) {


			foreach ($adressarr as $adress) {


				$addtype = $adress[param][TYPE][0];
				if ($addtype == "WORK") $addtype = l("Arbeit");
				if ($addtype == "HOME") $addtype = l("Privat");

				$add = explode(";",$adress[value][0][0]);
				$street = htmlentities($add[2]);
				$city = htmlentities($add[3]);
				$plz = htmlentities($add[5]);
				$country = htmlentities($add[6]);
				echo <<<END
	<div class="row" style="min-height:80px;height:80px;">
		<label>$addtype</label>
		<div class="addr" valign="middle" style="text-align:left;"><a href="http://maps.google.com/maps?q=$street%20$plz%20$city%20$country" target="_blank">$street<br/>$plz $city<br/>$country</a></div>
	</div>
END;

}
}
}
echo <<<END
	</fieldset>
END;


}



exit;
}

echo <<<END
    <ul id="addresslist" title="{$l('Adressen')}">
END;


$last_char = '';
$cardno = 0;
foreach ($cardinfo as $card) {

	$nickname = $card[FN][0][value][0][0];

	$sur_name = $card[N][0][value][0][0];
	$first_name = $card[N][0][value][1][0];

	$sur_name_starts_with = strtoupper(substr($sur_name,0,1));
	if ($sur_name_starts_with == "") $sur_name_starts_with = strtoupper(substr($nickname,0,1));

	$has_pic = ($card[PHOTO][0][value][0][0] != "");

	if ($has_pic) {
		$pic_code = "<img style=\"padding-top:0px;padding-bottom:0px;margin-top:0px;margin-bottom:0px;\" vspace=0 hspace=0 src=\"addressbook.php?showimg&cardnr=".$cardno."\" height = \"20\" width=\"20\"> ";
	} else $pic_code = "";

	if ($sur_name_starts_with != $last_char) {
		echo "<li class=\"group\">".$sur_name_starts_with."</li>";
		$last_char = $sur_name_starts_with;
	}

	echo "<li  ><a href=\"addressbook.php?showdet&cardno=".$cardno."\">".$pic_code.htmlentities($nickname)."</a></li>";
	$cardno++;
}

echo <<<END
	</ul>
END

?>