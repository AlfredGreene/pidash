<?php
require("../../assets/php/include/loginhandler.php");

function getScreens() {
	$s = explode("\n", trim(shell_exec("screen -ls | grep download")));
	$t = array();
    
    foreach ($s as $k) {
        $k = trim($k);
        if ($k === "") {
            continue;
        } else {
            $t[] = array_shift(explode("\t", array_pop(explode("download.", $k))));
        }
    }
	
	return $t;
}

function startDownload($u) {
    global $DOWNLOAD_DIR;
    $l = "/tmp/downloads";
    
    if (!file_exists($l)) {
        mkdir($l);
    }
    
    $id = uniqid();
    file_put_contents("$l/$id", basename($u) . ": " . "0");
    exec("screen -d -S download.$id -m python pget.py \"$u\" \"$DOWNLOAD_DIR\" \"$l/$id\"");
}

function killDownload($id) {
    exec("screen -S \"download.$id\" -X quit");
}

function togglePause($id) {
    exec("screen -S \"download.$id\" -X stuff ' '");
}

function getInformation($s) {
    $f = explode("\n", trim(file_get_contents("/tmp/downloads/$s")));
    $n = array_shift(explode(":", $f[0]));
    
    $e = implode("\n", array_slice($f, -2, 2, true));
    $s = strpos($e, "[Paused]") !== false;
    
    $p = array_pop(explode(":", array_pop($f))) . "%";
    return array("file"=>$n, "progress"=>$p, "paused"=>$s);
}

if (isset($_POST["u"])) {
    startDownload($_POST["u"]);
    header("Location: index.php");
} else if (isset($_GET["k"])) {
    killDownload($_GET["k"]);
    header("Location: index.php");
} else if (isset($_GET["p"])) {
    togglePause($_GET["p"]);
    header("Location: index.php");
}

$screens = getScreens();
$info = array();
foreach ($screens as $screen) {
    $info[$screen] = getInformation($screen);
}

?><!doctype html>
<html>
    <head>
        <title>Downloader - Raspberry Pi</title>
        <link rel="stylesheet" href="../../assets/css/style.css">
        <link rel="icon" href="../../assets/images/favicon.ico">
        <meta name="viewport" content="width=device-width, user-scalable=no">
    </head>
    <body>
<?php $page = 3; include("../../assets/php/include/menu.php"); ?>
        
        <div class="container content">
            <div class="block fullwidth">
                <h2>Add new download</h2>
                <form method="post" class="row">
                    <div class="col-75">
                        <input type="text" placeholder="http://example.com/file.zip" name="u" class="fullwidth" required>
                        <br>
                    </div>
                    <div class="col-25">
                        <input type="submit" class="btn btn-accent fullwidth" value="Start">
                        <br>
                        <br>
                    </div>
                </form>
            </div>

<?php

if ($info !== array()) {
    echo '
            <div class="block fullwidth">
                <h2>Active downloads</h2>';

    foreach ($info as $id=>$i) {
        echo '
                <div class="row">
                    <div class="col-75">
                            <i class="cut-text">
                                <a href="?k=' . $id . '">
                                    <i class="cross"></i>
                                </a>
                                <a href="?p=' . $id . '">
                                    <i class="' . ($i["paused"] ? "play" : "pause") . '"></i>
                                </a>
                                ' . $i["file"] . '
                            </i>
                    </div>
                    <div class="col-25">
                        <div class="progressbar">
                            <div class="progress" style="width: ' . $i["progress"] . '"></div>
                        </div>
                    </div>
                </div>';
                
    }
    
    echo '
                <br>
            </div>';
}
?>
        </div>
    </body>
</html>