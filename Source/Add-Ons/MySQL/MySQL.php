<?php
if(!defined("DBCONF")) {
	define("DBCONF", true);
	include('../Configuration/MySQL.php');
}
define("DB_INCLUDED", true);
define("BAD_USER", false);
define("VALID_USER", true);

$connection = false;

function getDB(){
	global $connection;
	if( @$connection && is_object($connection))
		return $connection;
	$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME) or die("Connection error : " . $connection->connect_error);
	@$connection->autocommit(false);
	return $connection;
}

function DBCommit(){
	$connection = getDB();
	return $connection->commit();
}
function getPuffle($strName){
	$puffles = getValue($strName, "puffles");
	return $puffles;
}
function getData($query, $m = "default"){
	$mysqli = getDB();
	$result = $mysqli->query($query);
	if(!is_object($result)){
		return false;
	}
	$row = $result->fetch_assoc();
	if($m == "single"){
		if(is_object($result))
			$result->close();
		return $row;
	}
	$a = array($row);
	while($d2 = $result->fetch_assoc()){
		$a[] = $d2;
	}
	if(is_object($result))
		$result->close();
	return $a;
}

function getValue($username, $field){
    $gval = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $arrData = mysqli_query($gval, "SELECT * FROM accs WHERE name='$username'");
	while($info = mysqli_fetch_array($arrData)){
	    return $info[$field];
	}
	mysqli_free_result($arrData);
	mysqli_close($gval);
}
function getIglooValues($intID, $field){
    $gval = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $arrData = mysqli_query($gval, "SELECT * FROM igloos WHERE id='$intID'");
	while($info = mysqli_fetch_array($arrData)){
	    return $info[$field];
	}
	mysqli_free_result($arrData);
	mysqli_close($gval);
}
function setIglooValues($intID, $field, $whatever){
    $con = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

	mysqli_query($con,"UPDATE igloos SET $field=$whatever WHERE id='$intID'");
	mysqli_close($con);
}
function getValueByID($intID, $field){
    $gvali = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $arrData = mysqli_query($gvali, "SELECT * FROM accs WHERE id='$intID'");
	while($info = mysqli_fetch_array($arrData)){
		return $info[$field];
	}
	mysqli_free_result($arrData);
	mysqli_close($gvali);
}

function isIPBanned($ip){
    $ipcon = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $check1 = mysqli_query($ipcon, "SELECT * FROM ipbans WHERE ip='$ip'");
	$check2 = mysqli_num_rows($check1);
	if($check2 == 0){
		return false;
		mysqli_close($ipcon);
	}
    $arrData = mysqli_query($ipcon, "SELECT * FROM ipbans WHERE ip='$ip'"); 
	while($info = mysqli_fetch_array($arrData)){
		$time = $info['expiration'];
		$curtime = strtotime("now");
		if($curtime > $time && $time != 0){
			return false;
		} else {
			return true;
		}
    }
	mysqli_free_result($check1);
	mysqli_free_result($arrData);
	mysqli_close($ipcon);
}

function IPBan($ip, $person, $time){
    $ipbcon = mysql_connect(DB_HOST, DB_USER, DB_PASS);
	mysql_select_db(DB_NAME, $ipbcon);
    $check1 = mysql_query("SELECT * FROM ipbans WHERE ip='$ip'");
	$check2 = mysql_num_rows($check1);
	if($check2 != 0){
		mysql_query("DELETE FROM ipbans WHERE ip='$ip'");
		mysql_query("INSERT INTO ipbans (ip, person, expiration) VALUES ('$ip', '$person', '$time')");
	} else {
		mysql_query("INSERT INTO ipbans (ip, person, expiration) VALUES ('$ip', '$person', '$time')");
    }
	mysql_free_result($check1);
	mysql_close($ipbcon);
}

function addAttempt($ip){
    $ipacon = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $check1 = mysqli_query($ipacon, "SELECT * FROM monitor WHERE ip='$ip'");
	$check2 = mysqli_num_rows($check1);
	$time = strtotime("now");
	$ntime = strtotime("+10 minutes");
	if($check2 != 0){
		while($info = mysqli_fetch_array($check1)){
			$gtime = $info['time'];
			if($time > $gtime){
				mysqli_query($ipacon,"DELETE FROM monitor WHERE ip='$ip'");
				mysqli_query($ipacon,"INSERT INTO monitor (ip, requests, time) VALUES ('$ip', '1', '$ntime')");
			} else {
				mysqli_query($ipacon, "UPDATE monitor SET requests=requests+1 WHERE ip='$ip'");
			}
		}
	} else {
		mysqli_query($ipacon,"INSERT INTO monitor (ip, requests, time) VALUES ('$ip', '1', '$ntime')");
    }
	mysqli_free_result($check1);
	mysqli_close($ipacon);
}

function exceedRequests($ip){
	$arrData = getData("SELECT * FROM monitor WHERE ip='$ip'");
	$requests = $arrData[0]['requests'];
	if($requests > 10){
		return true;
	} else {
		return false;
	}
}
	

function setData($query){
	$mysqli = getDB();
	$result = $mysqli->query($query);
	$return =  ($result === false) ? false :true;
	if(is_object($result))
		$result->close();
	return $return;
}

function queryPlayersAwards($intID){
   $a = (getData("SELECT crumbs FROM accs WHERE ID=" . dbEscape($intID), "single"));
   if(!$a)
      return BAD_USER;
   $a = unserialize($a['crumbs']);
   if(!is_array($a))
      return BAD_USER;
   $items = $a["items"];
   $awards = "";
   foreach($items as $item){
      if(($item >= 800 and $item < 823) || ($item >= 8000 and $item < 8010)){
         $awards .= "$item|0000000000|0%";
      }
   }
   $awards = substr($awards, 0, strlen($awards) - 1);
   return $awards;
}

function validUser($strName){
	$d = getData("SELECT ID from accs WHERE name = '" . dbEscape($strName) . "'");
	if($d[0] == false || (!is_array($d)) || (count($d) < 1) || $d == false){
		return BAD_USER;
	}
	if(count($d) !== 1){
		return VALID_USER;
	}
	return VALID_USER;
}

function validID($intID){
	$d = getData("SELECT name from accs WHERE ID = '" . dbEscape($intID) . "'");
	if($d[0] === false || (!is_array($d)) || (count($d) < 1)){
		return BAD_USER;
	}
	if(count($d) !== 1){
		$this->logger->error("ERROR: getData call returned multiple usernames for a UNIQUE query. Your database is corrupted!");
		return VALID_USER;
	}
	return VALID_USER;
}

function getId($strName){
	$d = getData("SELECT ID from accs where UCASE(name) = '" . dbEscape(strtoupper($strName)) . "'");
	if($d[0] === false || (!is_array($d)) || (count($d) < 1)){
		return BAD_USER;
	}
	if(count($d) !== 1){
		$this->logger->error("ERROR: getData call returned multiple usernames for a UNIQUE query. Your database is corrupted!");
	}
	return $d[0]['ID'];
}

function getName($intID){
	$d = getData("SELECT name from accs where ID = '" . dbEscape($intID) . "'");
	if($d[0] === false || (!is_array($d)) || (count($d) < 1)){
		return BAD_USER;
	}
	if(count($d) !== 1){
		$this->logger->error("ERROR: getData call returned multiple usernames for a UNIQUE query. Your database is corrupted!");
	}
	return $d[0]['name'];
}

function getRedemptionData($code){
   $d = getData("SELECT * FROM `redemption` WHERE `code` = '". dbEscape($code) . "' LIMIT 1");

   if($d[0] === false || (!is_array($d)) || (count($d) < 1)){
      return NON_EXISTENT;
   }
   return $d[0];
}

function getIglooDetails($intID){
	if(!validID($intID))
		return BAD_USER;
	$a = (getData("SELECT crumbs FROM accs WHERE ID=" . dbEscape($intID), "single"));
	if(!$a)
		return BAD_USER;
	$a = unserialize($a['crumbs']);
	if(!is_array($a))
		return BAD_USER;
	$s = $intID;
	if(!isset($a["igloo"])){
		return $s .= "%0%0%0";
	}
	$s .= "%" . $a["igloo"];
	$s .= "%" . $a["music"];
	$s .= "%" . $a["floor"];
	if(@$a["roomFurniture"]){
		$s .= "%" . $a["roomFurniture"];
		$s = substr($s, 0, strlen($s) - 1);
	}
	return $s;
}

function getPlayer($intID){
	$a = (getData("SELECT crumbs FROM accs WHERE ID=" . dbEscape($intID), "single"));
	if(!$a)
		return BAD_USER;
	$a = unserialize($a['crumbs']);
	if(!is_array($a))
		return BAD_USER;
	$s = $intID;
	$s .= "|" . getName($intID) . "|" . 1;//ENGLISH BITMASK!
	$s .= "|" . $a["color"];
	$s .= "|" . $a["head"];
	$s .= "|" . $a["face"];
	$s .= "|" . $a["neck"];
	$s .= "|" . $a["body"];
	$s .= "|" . $a["hands"];
	$s .= "|" . $a["feet"];
	$s .= "|" . $a["pin"];
	$s .= "|" . $a["photo"];
	$s .= "|" . 0;
	$s .= "|" . 0;
	$s .= "|" . 1;
	$s .= "|" . 1;
	$s .= "|" . (1 ? $a['badges'] * 147 : 16);
	$s .= "|" . 0;
	$s .= "|" . 0;
	$s .= "|" . 0;
	$s .= "|" . $a["mood"] . "|";
	return $s;
}
function dbEscape($s, $link = NULL){
	$mysqli = getDB();
	return $mysqli->real_escape_string($s);
}

function finishDB(){
	global $connection;
	if($connection)
		@mysql_close($connection);
	$connection = false;
}

function makeXt(){
	$a = func_get_args();
	if(!is_array($a))
		return false;
	$send = "%xt%";
	foreach($a as $s){
		$send .= $s . "%";
	}
	return $send;
}

function getPlayersStamps($intID){
	$a = (getData("SELECT crumbs FROM accs WHERE ID=" . dbEscape($intID), "single"));
	if(!$a)
		return BAD_USER;
	$a = unserialize($a['crumbs']);
	if(!is_array($a))
		return BAD_USER;
	$s = $intID;
	if(!count($a["stamps"])){
		return $s;
	}
	$s .= "%";
	foreach($a["stamps"] as $stamp){
		$s .= "$stamp|";
	}
	$s = substr($s, 0, strlen($s) - 1);
	return $s;
}

function queryPlayersPins($intID){
	$a = (getData("SELECT crumbs FROM accs WHERE ID=" . dbEscape($intID), "single"));
	if(!$a)
		return BAD_USER;
	$a = unserialize($a['crumbs']);
	if(!is_array($a))
		return BAD_USER;
	$items = $a["items"];
	$pins = "";
	foreach($items as $item){
		if(($item >= 500 and $item < 650) || ($item >= 7000 and $item < 7100)){ //temporary pin check
			$pins .= "$item|0000000000|0%"; //0000000000 = pin release date in unix time, not sure what third parameter
		}
	}
	$pins = substr($pins, 0, strlen($pins) - 1);
	return $pins;
}

function getStampBookCoverDetails($intID){	
	$Cover = getData("SELECT Cover FROM stamps WHERE id='" .$intID. "';");
	$coverd = $Cover[0]["Cover"];
	return $coverd;
}

function userOnline($intID){
	$a = (getData("SELECT crumbs FROM accs WHERE ID=" . dbEscape($intID), "single"));
	if(!$a)
		return BAD_USER;
	$a = unserialize($a['crumbs']);
	if(!is_array($a))
		return BAD_USER;
	if(isset($a['online']))
		if($a['online'])
			return $a['lastServerID'];
	return false;
}

function updateStatus($intID, $online){
	$query = "INSERT INTO stats VALUES($intID," . dbEscape($online) . "," . ($time = time()) . ") ON duplicate KEY UPDATE population=" . dbEscape($online) . ", ts=$time";
	$res = setData($query);
}

?>
