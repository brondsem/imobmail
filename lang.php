<?php

include_once('config.php');

$DEFAULT_LANG = 'de';

if (!isset($LANG)) {
    $LANG = $DEFAULT_LANG;
    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $LANG = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2);
    }
}

# security check
if (strlen($LANG) != 2) {
    die("Invalid language '$LANG'");
}

if (file_exists(dirname(__FILE__)."/lang/$LANG.php")) {
    require_once(dirname(__FILE__)."/lang/$LANG.php");
} else {
    require_once(dirname(__FILE__)."/lang/$DEFAULT_LANG.php");
}

# provide a way to call the l() function from a heredoc
$l = 'l';


?>