<?php

function listdir($dir) {
    if (substr($dir, -1) != "/") {
        $dir .= "/";
    }
    
	$d = opendir($dir);
	$o = array("dirs"=>array(), "files"=>array());
    
	while (($j = readdir($d)) !== false) {
		if ($j != ".") {
            if (is_dir($dir . $j)) {
                $o["dirs"][] = $j;
            } else {
                $o["files"][] = $j;
            }
		}
	}
	closedir($d);
    sort($o["dirs"]);
    sort($o["files"]);
	return $o;
}

function walkdir($d) {
	if (substr($d, -1) != "/") {
		$d .= "/";
	}
	$o = array();
	foreach (listdir($d) as $k) {
		$j = $d . $k;
		if (is_dir($j)) {
			$o = array_merge($o, walkdir($j));
		} else {
			$o[] = $j;
		}
	}
	return $o;
}

?>