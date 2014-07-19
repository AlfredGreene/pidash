<?php
require_once("ILibrary.php");

class ZDF implements ILibrary {
	function getDisplayName() {
		return "ZDF-Mediathek";
	}
	
	function isNSFW() {
		return false;
	}
	
	function isFromHere($url) {
        $url = str_replace("https", "http", $url);
        $url = str_replace("http", "", $url);
        $url = str_replace("://", "", $url);
        $url = str_replace("www.", "", $url);
        
        $probe = "zdf.de";
        return strpos($url, $probe) === 0;
	}
	
	function _search($query) {
		$o = array();
		$search = file_get_contents("http://www.zdf.de/ZDFmediathek/suche?flash=off&sucheText=" . urlencode($query));
		$search = str_replace("flash=off\"/>", "flash=off\">", $search);
		$pq = phpQuery::newDocumentHTML($search);
		$elements = $pq[".row ul li p a"];
		$i = 0;
		$k = 0;
		$z = "";
		foreach ($elements as $e) {
			if ($i == 0) {
				$z = "http://www.zdf.de" . pq($e)->attr("href") . "&ipad=on";
				$o[$z] = array();
			}
			$j = trim($e->textContent);
			
			if ($j != "") {
				$o[$z][] = $j;
				$i++;
				if ($i == 3) {
					$i = 0; 
					$k++;
				}
			}
		}
		return $o;
	}
	
	function search($query) {
		$o = array();
		$search = file_get_contents("http://www.zdf.de/ZDFmediathek/suche?flash=off&sucheText=" . urlencode($query));
		$search = str_replace("flash=off\"/>", "flash=off\">", $search);
		$pq = phpQuery::newDocumentHTML($search);
		$elements = $pq[".row ul li"];
        
		foreach ($elements as $e) {
            $k = pq($e);
            $i = array();
            foreach ($k->find(".text p") as $p) {
                $i[] = trim($p->textContent);
            }
            
            if (count($i) == 3 and strpos($i[2], "VIDEO") === 0) {
                #continue;
            
                $t = $k->find(".image a");
                $image = $t->find("img")->attr("src");
                $image = str_replace("timg94x65blob", "timg476x268blob", $image);
                $url = $k->find("a")->attr("href");

                $o["http://www.zdf.de" . $url] = array($i[1], $i[0] . " | " . $i[2], '<img class="fullwidth" src="' . $image . '" />');
            }
		}
		return $o;
	}
	
	function extract($url) {
		$s = file_get_contents($url);
		$pq = phpQuery::newDocumentHTML($s);
		$dsl = $pq[".dslChoice li a.play"];
		foreach ($dsl as $d) {
			$best = $d;
		}
		$best = pq($best);
		
		return $best->attr("href");
	}
}

?>
