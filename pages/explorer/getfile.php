<?php
require("../../assets/php/include/loginhandler.php");

if (isset($_GET["f"])) {
    $f = base64_decode($_GET["f"]);
    if (file_exists($f)) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $ct = finfo_file($finfo, $f);
        finfo_close($finfo);
        header("Content-type: $ct");
        readfile($f);
    }
}
?>