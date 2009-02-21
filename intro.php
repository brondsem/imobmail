<?php

/*
 +-----------------------------------------------------------------------+
 | intro.php                                                             |
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
<html>
<head>
<title>iMobMail</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=320; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>
<style type="text/css" media="screen">@import "./iui/iui.css";</style>
<script type="application/x-javascript" src="./iui/iui.js"></script>
</head>

<body>
    <div class="toolbar">
       <h1 id="pageTitle" name="pageTitle"></h1>
        <a id="backButton" class="button" href="#"></a>
       <a class="button" id="rb" href="logout.php" target="_top">Logout</a><div id="msgnav"></div></div>
    </div>
    
    <ul id="home" title="Mail" selected="true">
        <li><a href="accounts.php">Nachrichten abrufen</a></li>
        <li id = "sendbut" ><a href="#sendmail">Nachricht verfassen</a></li>
        <li id = "addbut" ><a href="addressbook.php">Adressbuch</a></li>
        <li><a href="#settings">Einstellungen</a></li>

    </ul>

<?php

$showdeleted = $_COOKIE['tmailprefshowdel'];
$showhtml = $_COOKIE['tmailprefshowhtml'];
$linksclickable = ($_COOKIE['tmailprefsmakeclickable'] == false) ? "true" : $_COOKIE['tmailprefsmakeclickable'];
$transformsmilies = ($_COOKIE['tmailprefsgraphsmil'] == false) ? "true" : $_COOKIE['tmailprefsgraphsmil'];

echo <<<END
    <div id="settings" title="Einstellungen" class="panel">
        

        <h2>Oberfl&auml;che</h2>
        <fieldset>
        <div class="row">
                <label>Links anklickbar?</label>
                <div class="toggle" onclick="SetCookie('tmailprefsmakeclickable',this.getAttribute('toggled'));"  toggled="$linksclickable"><span class="thumb"></span><span class="toggleOn">ON</span><span class="toggleOff">OFF</span></div>
            </div>
        <div class="row">
                <label>Graphische Smilies?</label>
                <div class="toggle" onclick="SetCookie('tmailprefsgraphsmil',this.getAttribute('toggled'));"  toggled="$transformsmilies"><span class="thumb"></span><span class="toggleOn">ON</span><span class="toggleOff">OFF</span></div>
            </div>
       <div class="row">
                <label>HTML anzeigen?</label>
                <div class="toggle" onclick="SetCookie('tmailprefshowhtml',this.getAttribute('toggled'));"  toggled="$showhtml"><span class="thumb"></span><span class="toggleOn">ON</span><span class="toggleOff">OFF</span></div>
            </div>
        <div class="row">
                <label>Gel&ouml;schte anzeigen?</label>
                <div class="toggle" onclick="SetCookie('tmailprefshowdel',this.getAttribute('toggled'));"  toggled="$showdeleted"><span class="thumb"></span><span class="toggleOn">ON</span><span class="toggleOff">OFF</span></div>
            </div>
        </fieldset>

    </div>    
END
?>
    
    <form id="sendmail" class="dialog" action="sendmail.php" method="post">
        <fieldset>
            <h1 id="newmailtitle">Neue eMail</h1>
            <a class="button leftButton" type="cancel">Cancel</a>
            <a class="button blueButton" type="submit">Senden</a>
            
            <label>An:</label>
            <input style="width:91%;padding-left:60px;padding-right:0px;" type="text" id="smto" name="to"/>
            <label>CC:</label>
            <input style="width:91%;padding-left:60px;padding-right:0px;" type="text" id="smcc" name="cc"/>
            <label>Betreff:</label>
            <input style="width:91%;padding-left:60px;padding-right:0px;" type="text" id="smsubj" name="subj"/>
            <label>Text:</label>
            <textarea name="textbody" style="width:94%;padding-top:25px;padding-left:5px;margin-left:10px;margin-right:0px;padding-right:0px;" id="smtext"  rows="20"></textarea>
        </fieldset>
    </form>

    
    
    </body>
</html>