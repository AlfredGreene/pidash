<?php
require("../../assets/php/include/loginhandler.php");
require("../../assets/php/modules/omxplayer.php");
require("../../assets/php/modules/io.php");

$nsfw = isset($_GET["nsfw"]) or isset($_POST["nsfw"]);
$libs = getLibs($nsfw);

$url = "";
if (isset($_GET["play"])) {
    $url = $_GET["play"];
}

if (isset($_POST["p"])) {
    require("../../assets/php/modules/phpquery.php");
    $url = $_POST["p"];
    foreach ($libs as $l) {
		if ($l->isFromHere($url)) {
			$url = $l->extract($url);
			break;
		}
	}
    $a = isset($_POST["local"]) ? "local" : "hdmi";
	shell_exec("export DISPLAY=0.0 && sudo -u pi screen -S omxplayer -d -m omxplayer -o $a -b \"$url\"");
    header("Location: index.php");
}

if (isset($_GET["stop"])) {
	shell_exec("sudo -u pi screen -S omxplayer -X quit");
	header("Location:index.php");
} else if(isset($_GET["pause"])) {
	shell_exec("sudo -u pi screen -S omxplayer -X stuff \"p\"");
	header("Location:index.php");
} else if(isset($_GET["prev"])) {
	shell_exec("sudo -u pi screen -S omxplayer -X stuff \"^[[D\"");
	header("Location:index.php");
} else if(isset($_GET["next"])) {
	shell_exec("sudo -u pi screen -S omxplayer -X stuff \"^[[C\"");
	header("Location:index.php");
}

?><!doctype html>
<html>
    <head>
        <title>Player - Raspberry Pi</title>
        <link rel="stylesheet" href="../../assets/css/style.css">
        <link rel="icon" href="../../assets/images/favicon.ico">
        <meta name="viewport" content="width=device-width, user-scalable=no">
    </head>
    <body>
<?php $page = 1; include("../../assets/php/include/menu.php"); ?>
        
        <div class="container content">
<?php

$d = "0";

if (count(getScreens()) == 1) {
    $i = trim(shell_exec("sudo -u pi ../../assets/bash/omxcontrol.sh status"));
    if ($i != "") {
        $i = explode("\n", $i);
        $d = array_pop(explode(" ", $i[0]));
        $p = array_pop(explode(" ", $i[1]));
        $p = $p / (float)$d * 100;
        $s = array_pop(explode(" ", $i[2]));
    } else {
        $d = 999999999999;
        $p = 0;
        $s = "false";
    }
    
    if ($s == "false") {
        $s = "Pause";
    } else {
        $s = "Resume";
    }
    echo '
            <div class="block">
                <h2>Video Controls</h2>
                
                <div class="slider">
                    <div class="progressbar">
                        <div class="progress" style="width: ' . $p . '%;" id="progress"></div>
                    </div>
                    <div class="dragger" id="drag" style="left: ' . $p . '%;">
                    </div>
                </div>
                
                <br>
                <br>
                
                <div class="row">
                    <div class="col-25">
                        <a href="?stop" class="btn fullwidth">Stop</a>
                        <br>
                        <br>
                    </div>
                    <div class="col-25">
                        <a href="?pause" class="btn btn-accent fullwidth">' . $s . '</a>
                        <br>
                        <br>
                    </div>
                    <div class="col-25">
                        <a href="?prev" class="btn fullwidth">-30 Seconds</a>
                        <br>
                        <br>
                    </div>
                    <div class="col-25">
                        <a href="?next" class="btn fullwidth">+30 Seconds</a>
                        <br>
                        <br>
                    </div>
                </div>
            </div>';
} else {
    
    $lib = 0;
    $q = '';
    if (isset($_POST["q"])) {
        $lib = $_POST["lib"];
        $q = $_POST["q"];
        require("../../assets/php/modules/phpquery.php");
        $search_results = $libs[$lib]->search($q);
    }
    
    if ($nsfw) {
        $nsfw = '
                            <input type="hidden" name="nsfw" value="enabled">';
    } else {
        $nsfw = '';
    }
    
    echo '
            <div class="row">
                <div class="col-50">
                    <div class="block">
                        <h2>Play URL</h2>
                        <form method="post" class="row">' . $nsfw . '
                            <div class="col-50">
                                <input type="text" name="p" value="' . $url . '" class="fullwidth" placeholder="URL..." required>
                                <br>
                                <br>
                            </div>
                            <div class="col-25">
                                <label>
                                    <input type="checkbox" name="local" value="val">
                                    Local Audio
                                </label>
                                <br>
                                <br>
                            </div>
                            <div class="col-25">
                                <input type="submit" class="btn btn-accent fullwidth" value="Play">
                                <br>
                                <br>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-50">
                    <div class="block">
                        <h2>Search</h2>
                        <form method="post" class="row">' . $nsfw . '
                            <div class="col-50">
                                <input type="text" name="q" value="' . $q . '" class="fullwidth" placeholder="Query...">
                                <br>
                                <br>
                            </div>
                            <div class="col-25">
                                <select name="lib" class="fullwidth">';
    foreach ($libs as $k=>$l) {
        $s = '';
        if ($k == $lib) {
            $s = ' selected';
        }
        echo '
                                    <option value="' . $k . '"' . $s . '>' . $l->getDisplayName() . '</option>';
    }
    echo '
                                </select>
                                <br>
                                <br>
                            </div>
                            <div class="col-25">
                                <input type="submit" class="btn btn-accent fullwidth" value="Search">
                                <br>
                                <br>
                            </div>
                        </form>
                    </div>
                </div>
            </div>';
}

if (isset($search_results)) {
    echo '
            <div class="block fullwidth">
                <h2>Results</h2>';
    
    $i = 0;
    foreach ($search_results as $url=>$information) {
        $u = urlencode($url);
        
        if ($nsfw) {
            $u .= "&nsfw";
        }
        
        if ($i == 0) {
            echo '
                <div class="row">';
        }
        echo '
                <div class="col-50">
                <a href="?play=' . $u . '" class="row">
                    <div class="col-50">
                        ' . utf8_decode($information[2]) . '
                    </div>
                    <div class="col-50">
                        ' . utf8_decode($information[0]) . '
                        <br>
                        <span class="muted">' . utf8_decode($information[1]) . '</span>
                    </div>
                </a>
                <br>
                <br>
                </div>';
        $i++;
        if ($i == 2) {
            $i = 0;
            echo '
                </div>';
        }
    }
    echo '
            </div>';
}

?>
        </div>
        <script type="text/javascript">var videolength = <?php
echo $d;

?>;</script>
        <script type="text/javascript" src="../../assets/js/slider.min.js"></script>
    </body>
</html>