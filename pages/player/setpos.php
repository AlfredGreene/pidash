<?php
require("../../assets/php/include/loginhandler.php");
if (isset($_GET["p"])) {
    $p = $_GET["p"];
    if (is_numeric($p)) {
        $p = (int)$p;
        echo $p;
        echo shell_exec("sudo -u pi ../../assets/bash/omxcontrol.sh setposition $p");
    }
}
?>