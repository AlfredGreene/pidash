<?php
require("../../assets/php/include/loginhandler.php");
require("../../assets/php/modules/io.php");
require("../../assets/php/modules/jukebox.php");

if (isset($_GET["start"])) {
    startJukebox();
} else if (isset($_GET["pause"])) {
    pauseJukebox();
} else if (isset($_GET["stop"])) {
    killJukebox();
} else if (isset($_GET["next"])) {
    nextJukebox();
} else if (isset($_GET["prev"])) {
    prevJukebox();
} else if (isset($_GET["shuffle"])) {
    shuffleJukebox();
} else if (isset($_GET["a"])) {
    appendJukebox($_GET["a"]);
} else if (isset($_GET["del"])) {
    delJukebox($_GET["del"]);
}


$dir = $JUKEBOX_DIR;
if (isset($_GET["d"])) {
    $dir = base64_decode($_GET["d"]);
}
if (substr($dir, -1) != "/") {
    $dir .= "/";
}


$ACTIVE = isJukebox();

?>

<!doctype html>
<html>
    <head>
        <title>Jukebox - Raspberry Pi</title>
        <link rel="stylesheet" href="../../assets/css/style.css">
        <link rel="stylesheet" href="../../assets/css/jukebox.css">
        <link rel="icon" href="../../assets/images/favicon.ico">
        <meta name="viewport" content="width=device-width, user-scalable=no">
    </head>
    <body>
<?php $page = 2; include("../../assets/php/include/menu.php"); ?>
        
        <div class="container content">
            <div class="row">
                <div class="col-50">
                    <div class="block">
                        <h2>Now Playing</h2>
                        
                        <div class="row">
                            <div class="col-50">
                                <div class="circle">
                                    <div class="content">
<?php

if (!$ACTIVE) {
    echo '
                                        <a href="?start">
                                            <i class="play-icon"></i>
                                        </a>';
} else {
    $information = getInformation();
    
    echo '
                                        <a href="?pause">
                                            <i class="' . ($information["paused"] ? "play" : "pause") . '-icon"></i>
                                        </a>
                                        
                                        <span class="muted">
                                            ' . $information["time"] . '
                                        </span>';
}

?>
                                    </div>
                                </div>
                                <br>
                            </div>
                            <div class="col-5">&nbsp;</div>
                            <div class="col-40">
<?php

if ($ACTIVE) {
    echo '
                                <h2 class="accent">' . $information["title"] . '</h2>
                                ' . $information["artist"] . '
                                <br>
                                <span class="muted">' . $information["album"] . '</span>
                                
                                <br>
                                <br>
                                <br>
                                <div class="centered fullwidth">
                                    <a href="?prev">
                                        <i class="prev-icon"></i>
                                    </a>
                                    <a href="?stop">
                                        <i class="stop-icon"></i>
                                    </a>
                                    <a href="?next">
                                        <i class="next-icon"></i>
                                    </a>
                                </div>';
} else {
    echo '
                                <h2 class="accent">Nothing</h2>
                                No Artist
                                <br>
                                <span class="muted">No Album</span>';
}
?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-50">
                    <div class="block">
                        <h2 style="display: inline-block; line-height: 200%;">Playlist</h2>
                        <a href="?shuffle" class="btn btn-accent pull-right">Shuffle</a>
                        
                        <ul class="playlist">
<?php

$p = trim(@file_get_contents("playlist.txt")) or "";
$k = explode("\n", $p);

foreach ($k as $t) {
    if ($t == "") {
        continue;
    }
    $j = explode(" ", $t);
    if (count($j) > 1 and $j[0] == "active") {
        $class = ' active';
        array_shift($j);
        $t = implode(" ", $j);
    } else {
        $class = '';
    }
    $j = basename($t);
    $t = base64_encode($t);
    
    echo  '
                            <li class="cut-text' . $class . '">
                                <a href="?del=' . $t . '"><i class="cross"></i></a>
                                <a href="?play=' .  $t . '">
                                    ' . $j . '
                                </a>
                            </li>';
}

?>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="block">
                <h2>Add to playlist</h2>
<?php

$d = listdir($dir);

foreach ($d["dirs"] as $_) {
    $l = $dir . $_;
    if ($_ == "..") {
        $k = explode("/", $dir);
        array_pop($k);
        array_pop($k);
        $l = implode("/", $k);
    }
    
    if (strlen($l) < strlen($JUKEBOX_DIR)) {
        continue;
    }
    
    $url = base64_encode($l);
    
    echo '
                <a class="fullwidth" href="?d=' . $url . '">
                    <img src="../../assets/images/folder-icon.png" height="20px">
                    <b>' . utf8_decode($_) . '</b>
                </a>
                <br>
                <br>';
}

foreach ($d["files"] as $_) {
    $l = $dir . "/$_";
    $url = base64_encode($l);
    
    echo '
                <a class="fullwidth" href="?a=' . $url . '">
                    <img src="../../assets/images/music-icon.png" height="20px">
                    ' . utf8_decode($_) . '
                </a>
                <br>
                <br>';
}

?>
            </div>
        </div>
    </body>
</html>