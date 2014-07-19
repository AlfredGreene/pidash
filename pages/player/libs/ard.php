<?php
require_once("ILibrary.php");

class ARD implements ILibrary {
	function getDisplayName() {
		return "ARD-Mediathek";
	}
	
	function isNSFW() {
		return false;
	}
	
	function isFromHere($url) {
        $url = str_replace("https", "http", $url);
        $url = str_replace("http", "", $url);
        $url = str_replace("://", "", $url);
        $url = str_replace("www.", "", $url);
        
        $probe = "m.ardmediathek.de";
        return strpos($url, $probe) === 0;
	}
	
    
    function getVideos($query, $page="") {
		$o = array();
		$c = file_get_contents("http://m.ardmediathek.de/Suche?sort=r&pageId=13932884&s=" . urlencode($query) . $page);
		$pq = phpQuery::newDocumentHTML($c);
        $elements = $pq[".pagingList ul li a"];
        
        foreach ($elements as $e) {
            $k = pq($e);
            $url = "http://m.ardmediathek.de" . $k->attr("href");
            
            $info = array();
            foreach ($k->find("p") as $i) {
                $info[] = $i->textContent;
            }
            
            $image = $k->find("img")->attr("src");
            if (strpos($image, "scaled") === false) {
                $n = array_shift(explode("/", array_pop(explode("contentblob/", $image))));
                $z = $n - 8;
                $image = str_replace($n, $z, $image);
            } else {
                $image = str_replace("bild-xs", "bild-l", $image);
            }
            
            $o[$url] = array($k->find("h2")->text(), implode(" | ", $info), '<img class="fullwidth" src="http://m.ardmediathek.de' . $image . '" />');
        }
        
        return $o;
    }
    
    function search($query) {
        $pages = 2;
        $o = array();
        for ($i = 0; $i < $pages; $i++) {
            $o = array_merge($o, $this->getVideos($query, "&goto=" . ($i + 1)));
        }
        return $o;
    }
	
	function extract($url) {
		$s = file_get_contents($url);
        $pq = phpQUery::newDocumentHTML($s);
        $v = $pq['video source[data-quality="L"]'];
        
        foreach ($v as $s) {
            $k = $s;
        }
        $k = pq($k)->attr("src");
        
		return trim($k);
	}
}

?>
