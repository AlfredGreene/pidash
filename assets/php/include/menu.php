<?php
$l = '';
if (isset($_SESSION["login"])) {
    $l = ' data-logout="logout"';
}
echo '
            <div class="navbar" id="menu"' . $l . '>
                <div class="container">';


if (!isset($ROOT)) {
    $ROOT = "../..";
}
echo '
                    <a class="brand" href="' . $ROOT . '">Raspberry Pi</a>
                    <ul>';

$dirs = array(
    "Dashboard"=>array("", "in-mobile"), 
    "Player"=>array("player", ""), 
    "Jukebox"=>array("jukebox", ""), 
    "Downloader"=>array("download", ""), 
    "Filebrowser"=>array("explorer", "")
);
$i = 0;
foreach ($dirs as $n=>$d) {
    if ($i == $page) {
        $d[1] .= " active";
    }
    echo '
                        <li class="' . $d[1] . '">
                            <a href="' . $ROOT . '/' . $d[0] . '">' . $n . '</a>
                        </li>';
    $i += 1;
    if ($i == 1) {
        $ROOT .= "/pages";
    }
}

if ($l != '') {
    echo '
                        <li class="logout">
                            <a href="?logout">Logout</a>
                        </li>';
}

?>

                    </ul>
                    <a href="#menu" class="menu-toggle in-mobile">
                        <i class="chevron"></i>
                    </a>
                    <a href="#close" class="menu-toggle menu-toggle-active in-mobile">
                        <i class="chevron"></i>
                    </a>
                </div>
            </div>
