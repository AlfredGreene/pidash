<?php
$accent = "#3ebc84";



$p = 0.75;
if (isset($_GET["p"])) {
    $p = $_GET["p"];
}

if ($p > 1) {
    $p = 1;
}

function hex2rgb($h) {
    $h = str_replace("#", "", $h);
    if (strlen($h) == 3) {
        $h = $h[0] . $h[0] . $h[1] . $h[1] . $h[2] . $h[2];
    }
    $r = substr($h, 0, 2);
    $g = substr($h, 2, 2);
    $b = substr($h, 4, 2);
    return array(hexdec($r), hexdec($g), hexdec($b));
}

$w = 700;
$h = $w / 2;
$i = imagecreatetruecolor($w, $h); #1000, 500);

$accent = hex2rgb($accent);
$accent = imagecolorallocate($i, $accent[0], $accent[1], $accent[2]);
$white = imagecolorallocate($i, 255, 255, 255);
$gray = imagecolorallocate($i, 242, 242, 242);

imagefill($i, 0, 0, $white);
imagefilledellipse($i, $w /2 , $h / 2, $w / 2, $w / 2, $gray); #500, 250, 500, 500, $gray);

if ($p > 0) {
    imagefilledarc($i, $w / 2, $h / 2 - 1, $h, $h, 270, (int)270 - 360*(1 - $p), $accent, IMG_ARC_PIE);
}

imagefilledellipse($i, $w / 2, $h / 2, $h * 0.8, $h * 0.8, $white);

header("Content-type: image/jpeg");
imagejpeg($i);
imagedestroy($i);
?>