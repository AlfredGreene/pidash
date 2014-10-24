<?php

function strip($s) {
	while (strpos($s, "  ") !== false) {
		$s = str_replace("  ", " ", $s);
	}
	return $s;
}

class Stats {
	
	function CPU() {
		$o = array("model"=>"", "load"=>array("1"=>0, "5"=>0, "15"=>0));
		$load = file_get_contents("/proc/loadavg");
		$cpu = file_get_contents("/proc/cpuinfo");
		
		$loadavg = explode(" ", $load);
		$o["load"]["1"] = ($loadavg[0] + 0)*100;
		$o["load"]["5"] = ($loadavg[1] + 0)*100;
		$o["load"]["15"] = ($loadavg[2] + 0)*100;
		
		$model = explode("model name", $cpu);
		$model = substr(trim($model[1]), 2);
		$model = explode("stepping", $model);
		$model = trim($model[0]);
		$o["model"] = $model;
		
		return $o;
	}
    
    function Temperature() {
        $t = shell_exec("sudo vcgencmd measure_temp");
        return array_shift(explode("'", array_pop(explode("=", $t))));
    }
	
	function DiskSpace() {
		$result= Array();
		foreach (Stats::get_disks() as $disk) { 	
			$total = disk_total_space($disk);
			$free = disk_free_space($disk);
			$f = round($free / (float)($total), 2) * 100;
			$u = 100 - $f;
			$result[]=array("label"=>$disk,"total"=>round($total/1024/1024/1024, 2), "free"=>$f, "used"=>$u);
		}
		return $result;
	}
	
	function RAM() {
		$freem = explode(" ", strip(shell_exec("free -m")));
		$t = $freem[7];
		$u = $freem[14];
		$f = $t - $u;
		$u = round($u / (double)$t * 100, 0);
		return array("total"=>$t, "free"=>$f, "used"=>$u);
	}
	
	function Version() {
		$r = file_get_contents("/etc/os-release");
		$o = explode("PRETTY_NAME=\"", $r);
		$o = explode("\"", $o[1]);
		return $o[0];
	}
	
	function Uptime() {
		$time = explode(" ", file_get_contents("/proc/uptime"));
		$t = time() - $time[0];
		$start = date("d.m.Y - H:i:s", $t);
		return $start;
	}
	
	function Wifi() {
		$i = explode("\n", trim(shell_exec("iwconfig")));
		$o = array("essid"=>"", "ap"=>"", "quality"=>"", "signal"=>"", "noise"=>"");
		
		$t = explode("\"", strip($i[0]));
		$t = explode("\"", $t[1]);
		$o["essid"] = $t[0];
		
		$t = explode(": ", strip($i[1]));
		$o["ap"] = trim($t[1]);
		
		$k = explode(" ", strip($i[5]));
		$t = explode("=", $k[2]);
		$t = explode("/", $t[1]);
		$o["quality"] = $t[0];
		
		$t = explode("=", $k[4]);
		$t = explode("/", $t[1]);
		$o["signal"] = $t[0];
		
		$t = explode("=", $k[6]);
		$t = explode("/", $t[1]);
		$o["noise"] = $t[0];
		
		return $o;
	}
    
    function Network() {
        $o = array();
        $i = explode("\n\n", trim(shell_exec("sudo ifconfig")));
        
        foreach ($i as $k) {
            $n = array_shift(explode(" ", $k));
            if ($n == "lo") {
                continue;
            }
            
            $l = explode("\n", $k);
            $mac = array_pop(explode(" ", trim($l[0])));
            $mac = str_replace("-", ":", strtolower(substr($mac, 0, 17)));
            
            $ips = explode(":", trim($l[1]));
            $ip = explode(" ", $ips[1]);
            
            $extra = array_shift(explode(" ", $ips[2]));
            $mask = array_shift(explode(" ", $ips[3]));
            
            $o[$n] = array("MAC"=>$mac, "IPv4"=>$ip[0], "Mask"=>$mask, $ip[2]=>$extra);
        }
        return $o;
    }
	function get_disks(){
	        $data=`mount | mount | grep -E "sd|/ "`;
	        $disks_line=preg_split("/\\r\\n|\\r|\\n/",$data);
			$disks=Array();
	        foreach($disks_line as $line) {
				$val=explode(' ',$line);
				if (sizeof($val) > 1)
					$disks[]=$val[2];
			}
	        return $disks;
	}
}

?>
