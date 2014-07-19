<?php
require_once("ILibrary.php");

class ARD implements ILibrary {
	function getDisplayName() {
		return "Example library";
	}

	function isNSFW() {
		return false;
	}

	function isFromHere($url) {
		return false;
	}
	function search($query) {
		return array();
	}

	function extract($url) {
		return $url;
	}
}

?>
