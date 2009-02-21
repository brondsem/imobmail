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
        'Adressbuch' => 'Address Book',
    );
    if (isset($translation[$key])) {
        return $translation[$key];
    } else {
        return $key;
    }
    }
?>