<?php
$USE_PASSWORD = false; #use a password for PiDash
$PASSWORD = "b89749505e144b564adfe3ea8fc394aa"; #use the md5.php to create your own. default: raspberry
$MAX_TRIES = 3; #maximum number of false login passwords
$WAIT_FOR_IT = 30; #seconds to reset the $MAX_TRIES

$VPN = false; #show vpn enable/disable button
$VPN_CONFIG = "/home/pi/vpn/raspberry.conf"; #openvpn config file

$JUKEBOX_DIR = "/home/pi/music"; #directory where the music is played from, make sure the permission is at least 0756

$DOWNLOAD_DIR = "/home/pi/download"; #output-dir of the downloads. make sure the dir is accessible for the webserver-user (e.g. www-data)
?>