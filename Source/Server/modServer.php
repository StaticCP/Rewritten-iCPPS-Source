<?php
error_reporting(E_ALL);
date_default_timezone_set(@date_default_timezone_get());
require_once("../Add-Ons/Games/Multiplayer.php");
$tables = new tables();
$findfour = new findfour();
$waddles = new waddles();
$jitsu = new jitsu();
		
function __autoload ($className){
	$dir = "Add-Ons";
	switch($className) {
		case "Utils":
		case "Crypto":
			$dir = "Add-Ons/Traits";
			break;
		case 'MySQL':
			$dir = 'Add-Ons/MySQL';
			break;
		case 'Exceptions':
		case 'XtException':
		case 'XmlException':
			$dir = 'Add-Ons/Exceptions';
			break;
		case "Client":
		case "Logger":
		case "ClientBase":
		case "ClubPenguin":
		case "xmlServBase":
			$dir = "Classes";
		break;
		default:
			$dir = "Add-Ons";
		break;
	}
    $fileName = "../$dir/".str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
    $status = require($fileName);
    if ($status === false) {
        eval(sprintf('class %s {function __construct(){die("Class %s could not be found in the ' . $dir . ' directory}', $className, $className));
    }
}

(@include "../Configuration/config.php") || halt("Failed to open config.php");
set_time_limit(0);
foreach($config as $key => $c) {
	if($c === null){
		fwrite(STDERR, "Option <$key> has not been set, shutting down.\n");
		exit(1);
	}
}
$error = 'php ../Errors/makeErrorArray.php';
eval("\$error = \"$error\";");
$config['PORT'] = 6114;
$server = new ClubPenguin($config);
$server->serverID = 102;
$server->run();
?>
