<?php

/*
 +-----------------------------------------------------------------------+
 | accounts.php                                                          |
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


/*
   Change this configuration depending your own need!
   
   DIESE KONFIGURATION VOR EINSATZ VON IMOBMAIL ANPASSEN!!!!
   (nur die Werte zwischen den Hochkommata �ndern, die Variablennamen
    m�ssen unver�ndert bleiben!)
*/

$USERNAME = "demo";						// Zugangsname zur Weboberfl�che
$PASSWORD = "demodemo";					// Passwort zur Weboberfl�che
/*
	W�HLEN SIE F�R DIESE ZUGANGSDATEN _NICHT_ DEN LOGIN ZU IHREM POSTFACH!!!!
	Diese Daten werden unverschl�sselt �ber's Netz gesendet und auf Ihrem
	mobilen Ger�t u.U. in einem Cookie gespeichert.
	NOCHMALS: setzen Sie hier eine _andere_ Kombination als f�r Ihr(e) Postf�cher
	ein!!
*/

/*
	es folgen die Angaben zu den IMAP- bzw. POP3-Postf�chern.
	Jedes Postfach ist durch die verschiedenen Variablen $ACCOUNT_<nr>_XX gekenn-
	zeichnet. Wenn Sie mehr Postf�cher einrichten wollen, so kopieren Sie den Block
	entsprechend und �ndern Sie die Ziffer in der Mitte der Variablen, so dass der
	Wert aufsteigen ist.
	$ACCOUNT_1_ARGS definiert Angaben zu Postfach-Typ und �bertragungsoptionen:
		/imap 		f�r IMAP-Zugriff
		/pop3 		f�r POP3-Zugriff
		/ssl 		f�r SSL-Verbindung zum Mailserver
		/notls 		keine TLS-Verschl�sselung verwenden, falls verf�gbar
		/novalidate-cert 	Zertifikat von TLS/SSL-Verbindungen nicht validieren
							(wichtig bei selbstsignierten SSL-Zertifikaten)
	Die Optionen sind kombinierbar, f�r eine SSL-IMAP-Verbindung ohne Validierung
	des Zertifikats geben Sie als z.B. an: /imap/ssl/novalidate-cert
	Weitere Informationen im Projekt-Wiki unter 
	http://trac.imobmail.org/wiki/KonfigurationAnpassen
*/

$ACCOUNT_1_DESCR = "account1";					// Name des Accounts in der Auflistung
$ACCOUNT_1_SERVER = "mein.mailserver.org";		// Adresse des Servers
$ACCOUNT_1_PORT = "143";						// Portadresse: 
												//	IMAP norm. 143, POP3 norm. 110,
												//	IMAP SSL: 993, POP3 SSL: 993
$ACCOUNT_1_USERNAME = "mainaccount";			// Username am Mailserver
$ACCOUNT_1_PASSWORD = "mainpasswort";			// Passwort am Mailserver
$ACCOUNT_1_ARGS = "/imap/novalidate-cert";		// Optionen (siehe oben bzw. im Wiki)

$ACCOUNT_2_DESCR = "account2";
$ACCOUNT_2_SERVER = "meinzweiter.mailserver.de";
$ACCOUNT_2_PORT = "110";
$ACCOUNT_2_USERNAME = "popusername";
$ACCOUNT_2_PASSWORD = "poppasswort";
$ACCOUNT_2_ARGS = "/pop3/novalidate-cert";


/*
	Angabe zur Adressbuch-Datei: dieses File im VCF-Format wird ge�ffnet und angezeigt,
	wenn Sie das Adressbuch von iMobMail �ffnen.
	Zum Erstellen �ffnen Sie z.B. Contacts in MacOS, markieren die Kontakte, die Sie
	in iMobMail angezeigt bekommen m�chten, klicken rechts auf 'Kontakte als vCard'
	exportieren und laden das erzeugte File zu den iMobMail-Dateien. Hier geben Sie 
	den Namen dieses Files an.
*/
$ADDRESSBOOK = "demo.vcf";


/*
	Angaben zum SMTP-Server
*/

$SMTP_SERVER = "meinsmtp.mailserver.de";		// Hostname des SMTP-Servers
$SMTP_USER = "meinloginname";					// Nutzername des SMTP-Servers
$SMTP_PASSWORD = "meinpasswort";				// Passwort des SMTP-Servers
$SMTP_SENDERNAME = "Hans Mustermann";			// Ihr Name
$SMTP_SENDERADDRESS = "john@doe.foe";			// Ihre eMail-Adresse (Absender)

?>
