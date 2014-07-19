<?php

interface ILibrary {
	function getDisplayName();
	function isNSFW();
	function isFromHere($url);
	function search($query);
	function extract($url);
}

?>
