<?php

function getScreens() {
	$_s = trim(shell_exec("sudo -u pi screen -ls"));
	$s = array();
	$t = array();
	
	if (substr($_s, 0, 2) !== "No") {
		$t = explode("\n", $_s);
		array_shift($t);
		array_pop($t);
		foreach ($t as $_) {
			if (strpos($_, "omxplayer") !== false) {
				$_ = explode(".", $_);
				$_ = explode("\t", $_[1]);
				array_push($s, $_[0]);
			}
		}
	}
	return $s;
}

function get_php_classes($file) {
  $classes = array();
  $tokens = token_get_all(file_get_contents($file));
  $count = count($tokens);
  for ($i = 2; $i < $count; $i++) {
    if (   $tokens[$i - 2][0] == T_CLASS
        && $tokens[$i - 1][0] == T_WHITESPACE
        && $tokens[$i][0] == T_STRING) {

        $class_name = $tokens[$i][1];
        $classes[] = $class_name;
    }
  }
  return $classes;
}

function getLibs($nsfw) {
	$o = array();
	
	foreach (preg_grep('/ILibrary\.php$/', glob('libs/*.php'), PREG_GREP_INVERT) as $l) {
		$c = get_php_classes($l);
		include($l);
		$c = $c[0];
		$c = new $c();
		if ((!$c->isNSFW() and !$nsfw) or $nsfw) {
			$o[] = $c;
		}
	}
	
	return $o;
}

?>