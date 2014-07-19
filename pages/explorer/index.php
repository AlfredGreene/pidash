<?php
require("../../assets/php/include/loginhandler.php");
require("../../assets/php/modules/io.php");

$dir = "/";
if (isset($_GET["d"])) {
    $dir = base64_decode($_GET["d"]);
    if (substr($dir, -1) != "/") {
        $dir .= "/";
    }
}

?><!doctype html>
<html>
    <head>
        <title>Filebrowser - Raspberry Pi</title>
        <link rel="stylesheet" href="../../assets/css/style.css">
        <link rel="icon" href="../../assets/images/favicon.ico">
        <meta name="viewport" content="width=device-width, user-scalable=no">
    </head>
    <body>
<?php $page = 4; include("../../assets/php/include/menu.php"); ?>
        
        <div class="container content">
            <div class="block fullwidth">
                <h2 class="cut-text">Index of <b class="accent"><?php echo $dir; ?></b></h2>
<?php

$d = listdir($dir);

foreach ($d["dirs"] as $_) {
    $l = $dir . $_;
    if ($l == "/..") {
        continue;
    } else if ($_ == "..") {
        $k = explode("/", $dir);
        array_pop($k);
        array_pop($k);
        $l = implode("/", $k);
    }
    
    $url = base64_encode($l);
    
    echo '
                <a class="row" href="?d=' . $url . '">
                    <div class="col-50">
                        <img src="../../assets/images/folder-icon.png" height="20px">
                        <b>' . $_ . '</b>
                    </div>
                </a>';
}

$k = array('B','kB','MB','GB');
foreach ($d["files"] as $_) {
    $l = $dir . $_;
    $url = base64_encode($l);
    $b = sprintf("%u", filesize($l));
    $s = (int)(strlen($b) / 3);
    $s = round($b / pow(1024, $s), 2) . $k[$s];
    
    $d = date("F d Y, H:i:s", filemtime($l));
    
    echo '
                <a class="row" href="getfile.php?f=' . $url . '">
                    <div class="col-50">
                        <img src="../../assets/images/file-icon.png" height="20px">
                        ' . $_ . '
                    </div>
                    <div class="col-25">
                        ' . $s . '
                    </div>
                    <div class="col-25">
                        ' . $d . '
                    </div>
                </a>';
}

?>
            </div>
        </div>
    </body>
</html>