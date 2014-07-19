<?php


function isJukebox() {
    $s = trim(shell_exec("sudo -u pi screen -ls | grep jukebox"));
    return $s !== "";
}

function startJukebox() {
    @unlink("/tmp/jukebox.log");
    file_put_contents("/tmp/jukebox.log", "This is jukebox. Let's listen to some music\n");
    chmod("/tmp/jukebox.log", 0766);
    
    exec("sudo -u pi DISPLAY=:0 screen -d -S jukebox -m python jukebox.py /tmp/jukebox.log");
    header("Location: index.php");
}

function pauseJukebox() {
    exec("sudo -u pi screen -S jukebox -X stuff \" \"");
}

function killJukebox() {
    exec("sudo -u pi screen -S jukebox -X stuff \"q\"");
    header("Location: index.php");
}

function nextJukebox() {
    exec("sudo -u pi screen -S jukebox -X stuff \"n\"");
    header("Location: index.php");
}

function prevJukebox() {
    exec("sudo -u pi screen -S jukebox -X stuff \"p\"");
    header("Location: index.php");
}

function shuffleJukebox() {
    exec("sudo -u pi screen -S jukebox -X stuff \"r\"");
    header("Location: index.php");
}

function appendJukebox($a) {
    $p = explode("\n", trim(file_get_contents("playlist.txt")));
    file_put_contents("playlist.txt", str_replace("//", "/", base64_decode($_GET["a"])) . "\n", FILE_APPEND);
    if (strlen($p) == 0) {
        nextJukebox();
    }
}

function delJukebox($a) {
    $p = explode("\n", trim(file_get_contents("playlist.txt")));
    $n = false;
    $o = "";
    $a = base64_decode($a);
    foreach ($p as $k) {
        if (strpos($k, $a) !== false) {
            $t = explode(" ", $k);
            if ($t[0] == "active") {
                $n = true;
            }
        } else {
            $o .= $k . "\n";
        }
    }
    
    file_put_contents("playlist.txt", $o);
    
    if ($n) {
        nextJukebox();
    } else {
        header("Location: index.php");
    }
}

function getInformation() {
    exec("sudo -u pi screen -S jukebox -X stuff \"i\"");
    $c = trim(@file_get_contents("/tmp/jukebox.log"));
    
    $c = explode("\n", $c);
    $c = array_pop($c);
    $t = (array)json_decode(base64_decode($c));
    if ($t == array()) {
        return array("title"=>"Fetching data...", "album"=>"", "artist"=>"", "time"=>"0:00", "paused"=>false);
    } else {
        $s = $t["time"] % 60;
        $m = ($t["time"] - $s) / 60;
        if ($s < 10) {
            $s = "0" . $s;
        }
        $t["time"] = "$m:$s";
        return $t;
    }
}

?>