<?php

/*
 * commands:
 * 
CMDSSH="ssh -l pi pi3"
CMDON="echo \"on 0\" | cec-client -d 1 -s"
CMDOFF="echo \"standby 0\" | cec-client -d 1 -s"
CMDMSG="echo \"00:64:00:74:65:73:74:69:6e:67\" | cec-client -d 1 -s"
CMDLIST="cec-client -d 1 -l"
CMDSCAN="echo \"scan\" | cec-client -d 1 -s"
CMDHELP="echo \"h\" | cec-client -d 1 -s"
CMDPOW="echo \"pow 0\" | cec-client -d 1 -s"
*/

$tvstates = array(
	"on" => array("description" => "switched on", "func" => "ext_switch_tv", "param" => "state"),
	"off" => array("description" => "switched off/standby", "func" => "ext_switch_tv", "param" => "state"),
	"unknown" => array("description" => "unknown state, possibly switching modes", "func" => null, "param" => null)
);

function validate_command ( $cmd, $commands, $args ) {
	$cmdinfo = $commands[$cmd];
	$valid = true;
	foreach($cmdinfo['rargs'] as $ra) {
		if (!isset($args[$ra])) 
			$valid=false;
	}
	
	return $valid;
}

function ext_set_tv($args) {
	global $tvstates;
	
	//echo "ext_set_tv() called!\n";
	//print_r($args);
	
	//print_r($tvstates[$args['state']]);
	
	if (isset($tvstates[$args['state']]))
		$func = $tvstates[$args['state']]['func'];
		
	$ret = call_user_func($func, $args);
}

function ext_get_tv($args) {
	$cmd = "echo \"pow 0\" | cec-client -d 1 -s";
	$resp = exec("$cmd", $output, $ret);
	//echo "$ret =>\n";
	//print_r($output);
	
	$powerstate="unknown";
	
	foreach($output as $line) {
		preg_match('/^power status\: (.*)/i',$line,$arr);
		//echo "$line : \n";
		//print_r($arr);
		
		if (isset($arr[1]))
			$powerstate = $arr[1];
	}
	
	if ($powerstate == "standby")
		$powerstate = "off";
	
	return array("error" => 0, "state" => $powerstate);
}

function ext_switch_tv($args) {
	$state = $args['state'];
	
	switch($state) {
		case "on":
			$cmd = "echo \"on 0\" | cec-client -d 1 -s";
			break;
		case "off":
			$cmd = "echo \"standby 0\" | cec-client -d 1 -s";
			break;
		default:
			$cmd = null;
			$state = null;
	}
	
	if ($cmd) {
		//echo $cmd.":\n";
		exec("$cmd", $output, $ret);
		//echo "output\n";
	}
	
	$ret = array("error" => 0, "state" => $state);
	
	return $ret;
}
		


?>
