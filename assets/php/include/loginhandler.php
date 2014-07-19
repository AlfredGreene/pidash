<?php
require_once(dirname(dirname(__FILE__)) . "../../../config.php");

if ($USE_PASSWORD) {
    session_start();
    
    if (isset($_GET["logout"])) {
        unset($_SESSION["login"]);
    }

    if (!isset($_SESSION["login"])) {
        $pidash_root = ".";
        $c = getcwd();

        if (basename(dirname(dirname($c))) == "php") {
            $pidash_root = "../../..";
        } else if (basename(dirname($c)) == "pages") {
            $pidash_root = "../..";
        }

        header("Location: $pidash_root/login.php");
    }
}

?>