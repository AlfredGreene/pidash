<?php
require("../include/loginhandler.php");

set_time_limit(0);

$n = uniqid();
$t = "/tmp/$n.jpg";
shell_exec("sudo -u pi DISPLAY=:0 ../../c/raspi2png -p \"$t\"");

header("Content-Type: image/png");
header("Content-Length: " . filesize($t));
readfile($t);
shell_exec("sudo rm \"$t\"");
?>