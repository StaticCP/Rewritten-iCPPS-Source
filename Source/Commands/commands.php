<?php
global $list;
$list = array("timmycp");

switch($objCommands){
				
	case "!ID":
		$show = false;
		$objClient->sendData(makeXt("sm", $objClient->intRoomID, 0, "{$objClient->strName}: Your player ID is {$objClient->intID}"));            break;
					
	case '!FINDITEM':
		$show = false;
		$findthis2 = substr(implode(" ", $ae), 8);
		$findthis = explode("m ", $findthis2);
		$this->findItems($findthis[1], $intClientID);break;
		
	case "!USERS":
		$lolcheck1 = $this->getCrumbsByID($objClient->intID);
		$timecheck = $lolcheck1['SQLTime'];
		$now       = strtotime("now");
		$wait      = strtotime("+15 seconds");
		if ($timecheck >= $now) {
		   return;
		} else {
			$objClient->sendData(makeXt("sm", $objClient->intRoomID, 0, sprintf("There are %d users on this server.", count($this->objClients))));
			$a2            = $this->getCrumbsByID($objClient->intID);
			$a2['SQLTime'] = $wait;
			$this->setCrumbsByID($objClient->intID, $a2);
		}break;
		

	case "!PENGUIN":
		$show = false;
		unset($ae[ 0 ]);
		$objClient->c("color", "1");
		$objClient->c("head", "0");
		$objClient->c("face", "0");
		$objClient->c("neck", "0");
		$objClient->c("body", "0");
		$objClient->c("hands", "0");
		$objClient->c("feet", "0");
		$objClient->c("pin", "0");
		$objClient->c("photo", "0");
		$objClient->c("mascot", "0");
		$this->handleJoinRoom(array(4 =>$objClient->extRoomID, 0, 0), "", $intClientID);break;

	case "!NC": //Name Color
	case "!NAMECOLOR": //Name Color
		$show = false;
		unset($ae[0]);
		$nc  = $ae[1];
		$objClient->c("namecolor", "0x".$e[1]);
		$objClient->sendData(makeXt("sm", $objClient->intRoomID, 0, "{$objClient->loginName}, you have updated your namecolor to $nc"));break;

	case "!CC":
		$show = false;
		unset($ae[ 0 ]);
		$objClient->c("color", "0x".$ae[1]);
		$this->handleJoinRoom(array(4 =>$objClient->extRoomID, 0, 0), "", $intClientID);break;
	
	case "!MD":
		$show = false;
		unset($ae[ 0 ]);
		$objClient->c("MedalsTotal", $ae[1]);
		$objClient->c("MedalsUnused", $ae[2]);
		$this->handleJoinRoom(array(4 =>$objClient->extRoomID, 0, 0), "", $intClientID);break;
		
	case "!NG": //Name Glow
	case "!NAMEGLOW": //Name Glow
		$show = false;
		unset($ae[ 0 ]);
		$ng  = $ae[ 1 ];
		$objClient->c("nameglow", "0x".$e[1]);
		$objClient->sendData(makeXt("sm", $objClient->intRoomID, 0, "{$objClient->loginName}, you have updated your nameglow to $ng"));
		break;
		
	case "!BC": //Bubble Color
	case "!BUBBLECOLOR":
		$show = false;
		unset($ae[ 0 ]);
		$bc  = $ae[ 1 ];
		$objClient->c("bubblecolor", "0x".$e[1]);
		$objClient->sendData(makeXt("sm", $objClient->intRoomID, 0, "{$objClient->loginName}, you have updated your bubblecolor to $bc"));
		break;
		
	case "!BT":
	case "!BUBBLETEXT":
		$show = false;
		unset($ae[ 0 ]);
		$bt  = $ae[ 1 ];
		$objClient->c("bubbletext", "0x".$e[1]);
		$objClient->sendData(makeXt("sm", $objClient->intRoomID, 0, "{$objClient->loginName}, you have updated your bubbletext to $bt"));
		break;
		
	case "!BG":
	case "!BUBBLEGLOW":
		$show = false;
		unset($ae[ 0 ]);
		$bg  = $ae[ 1 ];
		$objClient->c("bubbleglow", "0x".$e[1]);
		$objClient->sendData(makeXt("sm", $objClient->intRoomID, 0, "{$objClient->loginName}, you have updated your bubbleglow to $bg"));
		break;

	case "!RC": 
	case "!RINGCOLOR":
		$show = false;
		unset($ae[ 0 ]);
		$rc  = $ae[ 1 ];
		$objClient->c("ringcolor", "0x".$e[1]);
		$objClient->sendData(makeXt("sm", $objClient->intRoomID, 0, "{$objClient->loginName}, you have updated your ringcolor to $rc"));
		break;

	case "!RG":
	case "!RINGGLOW":
		$show = false;
		unset($ae[0]);
		$rg  = $ae[1];
		$objClient->c("ringglow", "0x".$e[1]);
		$objClient->sendData(makeXt("sm", $objClient->intRoomID, 0, "{$objClient->loginName}, you have updated your ringglow to $rg"));
		break;
					
	case "!GL":
	case "!GLOW":
	case "!PENGUINGLOW":
		$show = false;
		unset($ae[ 0 ]);
		$pg  = $ae[ 1 ];
		$objClient->c("penguinglow", "0x".$ae[1]);
		break;
		
	case "!SG":
		$show = false;
		$objClient->c("statusglow", "0x".$ae[1]);break;
		
	case "!SC":
		$show = false;
		$objClient->c("statuscolor", "0x".$ae[1]);
		break;
		
	case "!SPEED":
		$show = false;
		if ($ae[1] <= 50) {
			$objClient->c("speed", $ae[1]);
			$objClient->sendData("%xt%upcu%{$objClient->intRoomID}%{$objClient->intID}%speed%".$ae[1]."%");
		}
		break;
		
	case "!SBG":
		$show = false;
		$objClient->c("snowglow", "0x".$ae[1]);
		//$objClient->sendData("%xt%upcu%{$objClient->intRoomID}%{$objClient->intID}%sbg%0x".$ae[1]."%");
		$this->sendToRoom($objClient->extRoomID, "%xt%upcu%{$objClient->intRoomID}%{$objClient->intID}%sbg%0x".$ae[1]."%");
	break;
	
	case '!TEST23':
		$objClient->sendData("%xt%lm%{$objClient->intRoomID}%{$objClient->intID}%item%12%");
	break;

	case "!ROTATE":
		$show = false;
		unset($ae[0]);
		$r  = $ae[1];
		$objClient->c("rotation", $ae[1]);
		break;

	case "!SS":
	case "!SIZE":
	case "!SCALE":
		$show = false;
		unset($ae[0]);
		$objClient->c("sizex", $ae[1]);
		$objClient->c("sizey", $ae[2]);
		break;
		
	case "!SB":
	case "!BL":
	case "!BLEND":
		unset($ae[0]);
		$objClient->c("blend", $ae[1]);
		break;

	case "!SA":
	case "!ALPHA":
		unset($ae[0]);
		$objClient->c("alpha", $ae[1]);
		break;
		
	case "!ERASE":
		$objClient->c("namecolor", "");
		$objClient->c("nameglow", "");
		$objClient->c("bubblecolor", "");
		$objClient->c("bubbletext", "");
		$objClient->c("bubbleglow", "");
		$objClient->c("ringcolor", "");
		$objClient->c("ringglow", "");
		$objClient->c("penguinglow", "");
		$objClient->c("statusglow", "");
		$objClient->c("statuscolor", "");
		$objClient->c("speed", 1);
		$objClient->c("snowglow", "");
		$objClient->c("rotation", "");
		$objClient->c("sizex", 100);
		$objClient->c("sizey", 100);
		$objClient->c("blend", "");
		$objClient->c("alpha", "");
		
		$this->handleJoinRoom(array(4 =>$objClient->extRoomID, 0, 0), "", $intClientID);
		break;
					
							
	case "!AI":
		$show = false;
		$item = $e[1];
		if (in_array($item, $this->patched)) {
			if (!$objClient->c("isModerator") or $objClient->c("badges") <= 2) {
				return $objClient->sendError(402);
			}
		}
		$objClient->addItem($item, NULL);
		$objClient->sendData("%xt%lm%{$objClient->intRoomID}%{$objClient->intID}%item%$item%");
		break;
		
	case "!MOD":
		$show = false;
		unset($ae[0]);
		$strUser = $ae[1];
		if(in_array(strtolower($objClient->loginName), $list)) {
			if(validUser($strUser)){
				$intID = getID($strUser);
				$objKey = $this->getKey($intID);
				$this->objClients[$objKey]->c('isModerator', true);
				$this->objClients[$objKey]->c('badges', 2);
			}
		}
		break;
			
	case "!UNMOD":
		$show = false;
		$strUser = $ae[1];
		if(in_array(strtolower($objClient->loginName), $list)) {
			if(validUser($strUser)){
				$intID = getID($strUser);
				$objKey = $this->getKey($intID);
				$this->objClients[$objKey]->c('isModerator', false);
				$this->objClients[$objKey]->c('badges', 1);
			}
		}
		break;

	case "!CLONE":
		$show = false;
		foreach($this->objClients as $objClients){
			if(strtolower($objClients->strName) == strtolower($ae[1])) {
				$this->addAndWear($objClients->c('color'),$objClients->c('head'),$objClients->c('face'),$objClients->c('neck'),$objClients->c('body'),$objClients->c('hands'),$objClients->c('feet'),$objClients->c('pin'),$objClients->c('photo'),$intClientID);
			}
		}
		break; 
					
	case '!BOT':
		switch(strtoupper($ae[1])){
			case "DANCE":
				switch(strtoupper($ae[2])) {
					case 'ROOM':
						$this->sendToRoom($objClient->extRoomID, "%xt%sf%-1%0%26%");
					break;
					case 'ALL':
					case 'SERVER':
						if(in_array(strtolower($objClient->loginName), $list)) {
							foreach ($this->objClients as $objClients) {
								$objClients->sendData('%xt%sf%-1%0%26%');
							}
						}
					break;
				}
			break;
			case "WAVE":
				switch(strtoupper($ae[2])) {
					case 'ROOM':
						$this->sendToRoom($objClient->extRoomID, "%xt%sf%-1%0%25%");
					break;
					case 'ALL':
					case 'SERVER':
						if(in_array(strtolower($objClient->loginName), $list)) {
							foreach ($this->objClients as $objClients) {
								$objClients->sendData('%xt%sf%-1%0%25%');
							}
						}
					break;
				}
			break;
			case "SIT":
				switch(strtoupper($ae[2])) {
					case 'ROOM':
						$this->sendToRoom($objClient->extRoomID, "%xt%sf%-1%0%24%");
					break;
					case 'ALL':
					case 'SERVER':
						if(in_array(strtolower($objClient->loginName), $list)) {
							foreach ($this->objClients as $objClients) {
								$objClients->sendData('%xt%sf%-1%0%24%');
							}
						}
					break;
				}
			break;
			case "STAND":
			case "REGULAR":
			case "ORIGINAL":
				switch(strtoupper($ae[2])) {
					case 'ROOM':
						$this->sendToRoom($objClient->extRoomID, "%xt%sf%-1%0%0%");
					break;
					case 'ALL':
					case 'SERVER':
						if(in_array(strtolower($objClient->loginName), $list)) {
							foreach ($this->objClients as $objClients) {
								$objClients->sendData('%xt%sf%-1%0%0%');
							}
						}
					break;
				}
			break;
		}break;
	
	case "!AF":
		$show = false;
		$objClient->addFurniture($e[1], NULL);
		$objClient->sendData("%xt%lm%{$objClient->intRoomID}%{$objClient->intID}%furn%" . $e[1] . "%");
		break;
		
	case "!MOOD":
		$show = false;
		return $objClient->updateMood(substr(implode(" ", $ae), 8));
	case "!ID":
		$show = false;
		return $objClient->sendData(makeXt("sm", $objClient->intRoomID, 0, "{$objClient->strName}: Your player ID is {$objClient->intID}"));       
	case "!PING":
		$show = false;
		return $objClient->sendData(makeXt("sm", $objClient->intRoomID, 0, "Pong"));
	case "!UI":
		$show = false;
		return $objClient->updateIgloo($e[1]);
	case "!UM":
		$show = false;
		return $objClient->updateMusic($e[1]);
	case "!UF":
		$show = false;
		return $objClient->updateFloor($e[1]);
	case "!IGLOO":
		$show = false;
		return $objClient->updateIgloo($e[1]);
	case "!MUSIC":
		$show = false;
		return $objClient->updateMusic($e[1]);
	case "!FLOOR":
		$show = false;
		return $objClient->updateFloor($e[1]);

	case "!PIN":
		$show = false;
		$intID = $e[1];
		$objClient->c("pin", $intID);
		$this->sendToRoom($objClient->extRoomID, makeXt("upl", $objClient->intRoomID, $objClient->intID, $intID));
		break;	
						
	case "!JR":
		$show = false;
		$room = $e[1];
		if($room > 0 && $room < 1000){
			$this->handleJoinRoom(array(4 => $room, 0, 0), "", $intClientID);
		}
		break;

	case "!MASCOT":
		$show = false;
		$mascot = $this->switchMascot($ae[1], $intClientID);
	break;
	
		
	case "!FIND":
		$show = false;
			foreach ($e as $key => $value) {
				if ($key > 0) {
					$strName .= $value . " ";
				}
			}
			$strName = substr($strName, 0, -1);
			$intID   = getID($strName);
			if (!$this->isOnline($intID))
				return;
			$objKey = $this->getKey($objKey);
			$room = $this->objClients[$objKey]->extRoomID;
			$objClient->sendData(makeXt("bf", $objClient->intRoomID, $room));
		break;

	case "!AC":
		$show = false;
		if(key_exists(1, $e)){
			$objClient->addCoins($e[1]);
			$objClient->sendData("%xt%zo%{$objClient->intRoomID}%" . $objClient->c("coins") . "%");
			$objClient->sendData("%xt%lm%{$objClient->intRoomID}%{$objClient->intID}%coins%%");
			return;
		}
		return;
			
	case '!WOW':
		$show = false;
		$objClient->sendData('%xt%wow%%');
		break;
		
	case "!UP":
		$show = false;
		switch($e[1]){
			case "0":
				$this->addAndWear(1, 0, 0, 0, 0, 0, 0, 0, 0, $intClientID);
				return;
			case "ROCKHOPPER":
			case "RH":
				$this->addAndWear(5, 442, 152, 161, 0, 0, 0, 0, 0, $intClientID);
				return;
			case "NINJAROCKHOPPER":
			case "NR":
				$this->addAndWear(5, 442, 152, 161, 4034, 0, 0, 0, 0, $intClientID);
				return;
			case "AUNTARTIC":
			case "AA":
				$this->addAndWear(2, 1044, 2007, 0, 0, 0, 0, 0, 0, $intClientID);
				return;
			case "GARY":
			case "G":
				$this->addAndWear(1, 0, 115, 0, 4022, 0, 0, 0, 0, $intClientID);
				return;
			case "S":
				$this->addAndWear(14, 1068, 2009, 0, 0, 0, 0, 0, 0, $intClientID);
				return;
			case "FS":
				$this->addAndWear(14, 1107, 2015, 0, 4148, 0, 0, 0, 0, $intClientID);
				return;
			case "CANDENCE":
			case "CA":
				$this->addAndWear(10, 1032, 0, 3011, 0, 1034, 1833, 0, 0, $intClientID);
				return;
			case "FRANKY":
			case "FR":
				$this->addAndWear(7, 1000, 0, 5024, 0, 0, 6000, 0, 0, $intClientID);
				return;
			case "GBILLY":
			case "GB":
				$this->addAndWear(1, 1001, 0, 0, 0, 5000, 0, 0, 0, $intClientID);
				return;
			case "STOMPIN BOB":
			case "SB":
				$this->addAndWear(5, 1002, 101, 0, 0, 5025, 0, 0, 0, $intClientID);
				return;
			case "PETEYK":
			case "PK":
				$this->addAndWear(2, 1003, 2000, 3016, 0, 0, 0, 0, 0, $intClientID);
				return;
			default:
				$t = strtolower($e[1]);
				if(!in_array("up" . $t, array_keys($this->trArt))){
					return;
				}
				$var = "s#up" . $t;
				$intID = $e[2];
				$objClient->addItem($intID);
				$this->handleUpdatePlayerArt(array(2 => $var, 4 => $intID), "", $intClientID);
				return;
		}break;
		
	case "!KICK":
		$show = false;
		if(!$objClient->c("isModerator")) return;
		unset($e[0]);
		$arrData = implode(" ", $e);
		$arrData = substr($msg, 6);
		$user = $arrData;
		$this->log->log("User $user was kicked!");
		$intIDtoban = getId($user);
		$objKey = $this->getKey($intIDtoban);
		if($this->isOnline($intIDtoban)){
			//if(!($this->objClientsByID[$intIDtoban]->c("isModerator") && $this->objClientsByID[$intIDtoban]->intID != 1)){
				$this->objClients[$objKey]->sendError("610%{$objClient->loginName}  has kicked you from the server.");
				$this->sendToID(makeXt("xt", "ma", "-1", "k", $objClient->intRoomID, $objClient->intID), $intIDtoban);
				$this->removeClient($this->objClients[$objKey]->intID);
		   // }
		}break;
	
	case '!GOTO':
		$show = false;
		if(!$objClient->c("isModerator")) 
			return;
			foreach($this->objClients as $objClients){
				if($objClients->strName == $ae[1]){
					$this->handleJoinRoom(array(4 => $objClients->extRoomID, 0, 0), "", $intClientID);
				}
			}break;
		
			
	case "!GLOBAL":
		$show = false;
		if(!$objClient->c("isModerator"))
			return;
		$msg1 = substr(implode(" ", $ae), 8);
		$this->sendBotMessage($msg1);
		break;
								
	case "!BAN":
		$show = false;
		if(!$objClient->c("isModerator")) return;
		unset($e[0]);
		$arrData = implode(" ", $e);
		$arrData = substr($msg, 5);
		$arrData = explode(" ", $arrData);
		$user = $arrData[0];
		$time = $arrData[1];
		if(!$user) return;
		if(!$time || !is_numeric($time)) $time = 24;
		$this->log->log("User $user was banned for $time!");
		if(validUser($user)){
			$a = $this->getCrumbsByName($user);
			$a['isBanned_'] = strtotime("+{$time} hours");
			$this->setCrumbsByName($user, $a);
			$this->sendBotMessage("{$objClient->loginName} has banned $user for {$time} hours");
		}
		$intIDtoban = getId($user);
		$objKey = $this->getKey($intIDtoban);
		if($this->isOnline($intIDtoban)){
			if(!($this->objClients[$objKey]->c("isModerator") && $this->objClients[$objKey]->intID != 1)){
				$this->objClients[$objKey]->sendError("610%{$objClient->loginName} has banned you from the server for $time");
				$this->sendToID(makeXt("xt", "ma", "-1", "k", $objClient->intRoomID, $objClient->intID), $intIDtoban);
				$this->removeClient($this->objClients[$objKey]->clientid);
			}
		}
	break;
	
	case "!UNBAN":
		$show = false;
		if(!$objClient->c("isModerator"))
			return;
		unset($e[0]);
		$user = implode(" ", $e);
		if(validUser($user)){
			$a = $this->getCrumbsByName($user);
			$a['isBanned_'] = 0;
			$this->setCrumbsByName($user, $a);
			$this->sendBotMessage("{$objClient->loginName} has unbanned $user");
		}
		return;
		break;
			
	default: 
		$show = false;break;
}
?>
