<?php

function l($key) {
    static $translation = array(
        # index
        'Diese Webapplikation ist fuer mobile Safari-Browser entwickelt. Bei anderen Browsern wie Firefox, Opera, IE etc. kann es zu Fehlfunktionen kommen!'
            => 'This web application is developed for the mobile Safari browser. For other browsers such as Firefox, Opera, IE, etc. it may malfunction!',
        'Seite trotzdem oeffnen?' => 'Continue anyway?',
        'Logindaten falsch!' => 'Login failed!',
        'Senden' => 'Send',
        'Passwort' => 'Password',
        'Bitte authentifizieren Sie sich mit Ihren Account-Daten!' => 'Please Login:',
        'Benutzung auf eigene Gefahr!' => 'Use at your own risk!',
        'Diese Anwendung verwendet adaptierte Bestandteile des'
            => 'This application uses components adapted from',
        'eingeloggt bleiben' => 'Remember me',
        
        # intro
        'Nachrichten abrufen' => 'Read Email',
        'Nachricht verfassen' => 'Compose Email',
        'Adressbuch' => 'Address Book',
        'Einstellungen' => 'Settings',
        
        # settings
        'Oberfl&auml;che' => 'Presentation',
        'Links anklickbar?' => 'Clickable links?',
        'Graphische Smilies?' => 'Graphical smilies?',
        'HTML anzeigen?' => 'Show HTML?',
        'Gel&ouml;schte anzeigen?' => 'Show deleted?',
        
        # compose
        'Neue eMail' => 'New Email',
        'An' => 'To',
        'Betreff' => 'Subject',
        'Mail wurde verschickt!' => 'Mail has been sent!',
        
        # address book
        'Adressen' => 'Addresses',
        'Adresse' => 'Address',
        'Arbeit' => 'Work',
        'Privat' => 'Personal',
        'Mobil' => 'Cell',
        'Telefon' => 'Phone',
        'eMail' => 'Email',
        
        # read
        'Ordner' => 'Folders',
        'Posteingang' => 'Inbox',
        '25 vorige Nachrichten laden...' => 'Previous 25...',
        '25 weitere Nachrichten laden...' => 'Next 25...',
        'Von' => 'From',
        'von' => 'of',
        'Anh&auml;nge' => 'Attachments',
    );
    if (isset($translation[$key])) {
        return $translation[$key];
    } else {
        return $key;
    }
    }
?>