<?php
require("assets/php/include/loginhandler.php");

if (isset($_GET["reboot"])) {
	shell_exec("sudo shutdown -r now");
} else if (isset($_GET["halt"])) {
	shell_exec("sudo halt");
} else if (isset($_GET["vpn-enable"])) {
	shell_exec("sudo screen -S vpn -d -m sudo openvpn \"$VPN_CONFIG\"");
	header("Location: index.php?vpn-enabled");
} else if (isset($_GET["vpn-disable"])) {
	shell_exec("sudo screen -S vpn -X stuff \"^C\"");
	header("Location: index.php");
} else if (isset($_GET["vpn-enabled"])) {
    $VPN = false;
}

require("assets/php/modules/pistats.php");
$s = new Stats();
$n = $s->Network();
?>
<!doctype html>
<html>
    <head>
        <title>Dashboard - Raspberry Pi</title>
        <link rel="stylesheet" href="assets/css/style.css">
        <link rel="icon" href="assets/images/favicon.ico">
        <meta name="viewport" content="width=device-width, user-scalable=no">
    </head>
    <body>
<?php $ROOT = "."; $page = 0; include("assets/php/include/menu.php"); ?>
        
        <div class="container content">
            <div class="row">
                <div class="col-50">
                    <div class="block">
                        <h2>Device Status</h2>
                        <br>
<?php
$d = $s->CPU();
$l = $d["load"]["1"];
$r = $s->RAM();
$rl =  100 - round($r["free"] * 100 / $r["total"]);
echo '
                        <div class="row">
                            <div class="col-50">
                                <div class="diagram">
                                    <b class="accent">CPU</b>
                                    <img src="assets/php/modules/diagram.php?p=' . ($l / 100.) . '">
                                    <div class="data">
                                        ' . $l . '%
                                    </div>
                                </div>
                            </div>
                            <div class="col-50">
                                <div class="diagram">
                                    <b class="accent">RAM</b>
                                    <img src="assets/php/modules/diagram.php?p=' . ($rl / 100.) . '">
                                    <div class="data">
                                        ' . $rl . '%
                                    </div>
                                </div>
                            </div>
                        </div>';
?>

                    </div>
                </div>
                <div class="col-50">
                    <div class="block">
                        <h2>Device Management</h2>
                        <br>
<?php
$d = $s->DiskSpace();
$fs = $d["used"];
echo '

                        <div class="row">
                            <div class="col-50">
                                <div class="diagram">
                                    <b class="accent">Filesystem</b>
                                    <img src="assets/php/modules/diagram.php?p=' . ($fs / 100.) . '">
                                    <div class="data">
                                        ' . $fs . '%
                                    </div>
                                </div>
                            </div>
                            <div class="col-50">
                                <b class="accent">Temperature: </b>' . $s->Temperature() . '&deg;C
                                <br>
                                <br>
                                <a class="btn btn-accent fullwidth" href="?halt">Shutdown</a>
                                <br>
                                <br>
                                <a class="btn fullwidth" href="?reboot">Reboot</a>
                            </div>
                        </div>';
?>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-50">
                    <div class="block">
                        <h2 style="display: inline; line-height: 200%;">Networking</h2><?php
$n = $s->Network();

if ($VPN) {
    if (!array_key_exists("tun0", $n)) {
        echo '
                
                        <a href="?vpn-enable" class="btn btn-accent pull-right">Enable VPN</a>';
    } else {
        echo '
                
                        <a href="?vpn-disable" class="btn btn-accent pull-right">Disable VPN</a>';
    }
}
echo '
                        <br>
                        <br>
                        <b class="accent">Hostname </b>' . gethostname() . '
                        <br>
                        <br>
                        <h3>Interfaces</h3>';

foreach ($n as $i=>$d) {
    echo '
                        <div class="row">
                            <div class="col-25">
                                <b class="accent">' . $i . '</b>
                            </div>
                            <div class="col-50">';
    foreach ($d as $k=>$v) {
        echo '
                                <b>' . $k . ': </b>' . $v . '<br>';
    }
    echo '
                            </div>
                        </div>
                        <br>';
}
if (array_key_exists("wlan0", $n)) {
    $w = $s->WiFi();
    echo '
                        <h3>WiFi</h3>
                        <div class="row">
                            <div class="col-25">
                                <b class="accent">ESSID</b>
                            </div>
                            <div class="col-50">
                                ' . $w["essid"] . ' <span class="muted">(' . strtolower($w["ap"]) . ')</span>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-50">
                                <div class="diagram">
                                    <b class="accent">Signal</b>
                                    <img src="assets/php/modules/diagram.php?p=' . ($w["signal"] / 100.) . '">
                                    <div class="data">
                                        ' . $w["signal"] . '%
                                    </div>
                                </div>
                            </div>
                            <div class="col-50">
                                <div class="diagram">
                                    <b class="accent">Quality</b>
                                    <img src="assets/php/modules/diagram.php?p=' . ($w["quality"] / 100.) . '">
                                    <div class="data">
                                        ' . $w["quality"] . '%
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>';
    
}

?>

                    </div>
                </div>
                <div class="col-50">
                    <div class="block">
                        <h2 style="display: inline">Now Displaying</h2> <span class="muted">(may take a while)</span>
                        <br>
                        <br>
                        <img width="100%" src="assets/php/modules/screenshot.php">
                    </div>
                </div>
            </div>
        </div>
        <br>
    </body>
</html>