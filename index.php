<?php

include "functions.php";

date_default_timezone_set('Australia/Brisbane');

$commands = array(
	"set_tv" => array("db" => false, "rargs" => array("state"), "func" => "ext_set_tv"),
	"get_tv" => array("db" => false, "rargs" => null, "func" => "ext_get_tv")
);

//print_r($commands);

if (strstr($_SERVER['REMOTE_ADDR'],"10.1.1.") === false) {
	http_response_code(401);
	echo json_encode(array("error" => "401", "status" => "NOT AUTHORISED!"));
	return;
}

if (isset($_REQUEST['cmd'])) {
	$cmd = $_REQUEST['cmd'];
}
else {
	$cmd = "set_tv";
}

if ($cmd === false) {
	echo "Command unknown\n";
	echo "Valid commands:\n";
	foreach (array_keys($commands) as $c) {
		echo "$c (".count($commands['rargs'])." args)\n";
	}
	return 1;
}

$cmdinfo = $commands[$cmd];
$args = $_REQUEST;

if ( validate_command ( $cmd, $commands, $args ) !== true ) {
	echo "Command requires arguements:\n";
	foreach ($cmdinfo['rargs'] as $arg) {
		echo "$arg\n";
	}
	return 1;
}

unset($args['cmd']);

echo json_encode(call_user_func($cmdinfo['func'], $args));


?>
