<?php
class tables {
	public $tables = array();
	
	function __construct() {
		
	}
	
	function getTableClientCount($tableID) {
		$objClients = 0;
		$timeout = time() - 300;
		foreach($this->tables[$tableID]["clients"] as $i=> &$objClient) {
			if($this->tables[$tableID]["clients"][$i]) {
				$objClients++;
			}
		}
		return $objClients;
	}
	
	function getTable($tableID) {
		if(!isset($this->tables[$tableID])) {
			$this->tables[$tableID] = array("clients" => array(), "max" => 2);
		}
		return $this->tables[$tableID];
	}
	
	function joinTable($tableID, $objClient) { 
		if(!isset($this->tables[$tableID])) {
			return;
		}
		if($this->getTableClientCount($tableID) >= $this->tables[$tableID]["max"]) {
			$objClient->sendError(211);
			return false;
		}
		$this->tables[$tableID]["clients"][] = $objClient;
		return $this->getTableClientCount($tableID);
	}
	
	function resetTable($tableID) {
		if(!isset($this->tables[$tableID])) {
			return;
		}
		unset($this->tables[$tableID]);
		$this->getTable($tableID);
	}
	
}

class findfour {
	
	public $seats = array();
	
	function __construct() {
		for($i = 200; $i <= 207; $i++) {
			$this->seats[$i] = $this->getNewMap();
		}
	}
	
	function checkGrid($seat, $x, $y) {
		if(!isset($this->seats[$seat])) {
			return;
		}
		$mySeat = $this->seats[$seat];
		$map = $mySeat;
		$mapFull = true;
		
		if($this->check($seat, $x, $y)) {
			if($this->seats[$seat][$x][$y] == 1) {
				return array("msg" => "WIN_1");
			} else {
				return array("msg" => "WIN_2");
			}
		}

		for($i = 0; $i < 6; $i++) {
			for($j = 0; $j < 7; $j++) {
				if($map[$i][$j] == 0) {
					$mapFull = false;
				}
			}
		}
		if($mapFull) {
			return array("msg" => "DRAW");
		}
		return array("msg" => "ALL_GOOD");
	}
	
	function check($seat, $x, $y) {
		
		if ($this->horizontalCheck($seat, $x, $y) || $this->verticalCheck($seat, $x, $y)) {
			return true;
		} else {
			return false;
		}
		
	}
	
	function horizontalCheck($seat, $x, $y) {
		$player = $this->seats[$seat][$x][$y];
		$count = 0;
		
		// count towards left of coin
		for ($i = $y; $i >= 0; $i--) {
			if ($this->seats[$seat][$x][$i] !== $player) {
				break;
			}
			$count++;
		}
		
		// count towards right of coin
		for ($i = $y + 1; $i < 7; $i++) {
			if ($this->seats[$seat][$x][$i] !== $player) {
				break;
			}
			$count++;
		}
		if($count >= 4) {
			return true;
		}
		return false;
	}
	
	function verticalCheck($seat, $x, $y) {
		if ($x >= 3) { // if current piece is less than 4 pieces from bottom, skip check
			return false;
		}
		
		$array = $this->seats[$seat];
		$player = $array[$x][$y];
		
		for ($i = 7; $i <= 9; $i++) {
			if ($array[$i][$y] !== $player) {
				return false;
			}
		}
		return true;
		
	}
	
	function getNewMap() {
		$map = array();
		for($i = 0; $i < 6; $i++) {
			for($j = 0; $j < 7; $j++) {
				$map[$i][$j] = 0;
			}
		}
		return $map;
	}
	
	function resetSeat($seat) {
		if(!isset($this->seats[$seat])) {
			return;
		}
		unset($this->seats[$seat]);
		$this->seats[$seat] = $this->getNewMap();
		return $this->seats[$seat];
	}
	
}

class waddles {
	
	public $waddles = array();
	public $matches = array();
	
	function __construct () {
		for($i = 100; $i <= 104; $i++) {
			$this->waddles[$i] = $this->getNewWaddleObject($i);
		}
		for($i = 200; $i <= 204; $i++) {
			$this->waddles[$i] = $this->getNewWaddleObject($i);
		}
	}
	
	function getNewWaddleObject($intID) {
		$max = 2;
		switch($intID) {
			case 100:
				$max = 4;
			break;
			case 101;
				$max = 3;
			break;
		}
		return array("id" => $intID, "clients" => array(), "max" => $max, "requests" => 0);
	}
	
	function getWaddle($intID) {
		if(isset($this->waddles[$intID])) {
			return $this->waddles[$intID];
		}
	}
	
	function getWaddleString($intID) {
		$waddle = $this->getWaddle($intID);
		if(!isset($waddle)) {
			return;
		}
		$strWaddle = $intID . "|";
		for ($i = 0; $i < $waddle["max"]-1; $i++) {
			if(!isset($waddle["clients"][$i])) {
				$strWaddle .= ",";
				continue;
			}
			$cli = $waddle["clients"][$i];
			if(isset($cli->sock)) {
				$strWaddle .= $cli->strName . ",";
			}
		}
		return $strWaddle;
	}
	
	function joinWaddle($intID, $objClient) {
		$waddle = $this->getWaddle($intID);
		if(!isset($waddle) || !isset($objClient)) {
			return;
		}
		$isReady = false;
		$this->waddles[$intID]["clients"][] = $objClient;
		if(count($this->waddles[$intID]["clients"]) >= $waddle["max"] || count($this->waddles[$intID]["clients"]) == $waddle["max"]) {
			$isReady = true;
		}
		return array("seat" => count($this->waddles[$intID]["clients"])-1, "isReady" => $isReady);
	}
	
	function leaveWaddle($intID, $objClient) {
		if(!isset($this->waddles[$intID]) || !isset($objClient)) {
			return;
		}
		foreach($this->waddles[$intID]["clients"] as $i=> &$user) {
			if($user->intID == $objClient->intID) {
				unset($this->waddles[$intID]["clients"][$i]);
			}
		}
	}
	
	function refreshPlayers ($intID) {
		$waddle = $this->getWaddle($intID);
		foreach ($waddle["clients"] as $i=> &$objClient) {
			if (!is_object($objClient->sock)) {
				unset($waddle['clients'][$i]);
			}
		}
	}
	
	function getWaddleCount($intID) {
		$waddle = $this->getWaddle($intID);
		if(!isset($waddle)) {
			return;
		}
		$num = 0;
		$timeout = time() - 300;
		foreach ($waddle["clients"] as $i=> &$objClient) {
			if($objClient->time == NULL && ($objClient->time > $timeout)) {
				$num++;
			}
		}
		return $num;
	}
	
	function getUpdateString($intID) {
		$waddle = $this->getWaddle($intID);
		if(!isset($waddle)) {
			return;
		}
		$str = $waddle["max"] . "%";
		foreach ($this->waddles[$intID]["clients"] as $i=> &$objClient) {
			$str .= $objClient->strName . "|" . $objClient->c("color") . "|" . $objClient->c("hands") . "|" . strtolower($objClient->strName) . "%"; 
		}
		$waddle["requests"]++;
		if($waddle["requests"] >= $waddle["max"]) {
			$this->onClearOut($intID);
		}
		return $str;
	}
	
	function resetWaddle($intID) {
		$waddle = $this->getWaddle($intID);
		if(!isset($waddle)) {
			return;
		}
		$this->waddles[$intID] = $this->getNewWaddleObject($intID);
	}
	
	function onClearOut($intID) {
		foreach ($this->waddles[$intID] as $i => &$objClient) {
			$objClient->sendData("%xt%uw%-1%{$intID}%" . $objClient->multiplayer["seatID"] . "%");
		}
		$this->resetWaddle($intID);
	}
	
}

class jitsu {
	
	public $waitingUsers = array();
	public $matches = array();
	
	private $parent;
	
	function __construct () {
		
	}
	
	function imReady ($matchID) {
		if(!isset($this->matches[$matchID])) {
			return;
		}
		$this->matches[$matchID]["isReady"] = true;
	}
	
	function isReady ($matchID) {
		if(!isset($this->matches[$matchID])) {
			return;
		}
		return $this->matches[$matchID]["isReady"];
	}
	
	function addToWaitingList(&$objClient) {
		$this->waitingUsers[] = &$objClient;
	}
	
	function removeFromWaitingList(&$objClient) {
		foreach ($this->waitingUsers as $i => &$user) {
			if($user->intID == $objClient->intID) {
				unset($this->waitingUsers[$i]);
				return;
			}
		}
	}
	
	function tryToMatchUp(&$objClient) {
		if(count($this->waitingUsers) > 1) {
			$player = $this->waitingUsers[array_rand($this->waitingUsers)];
			$player = &$player;
			while($player->intID == $objClient->intID) { // try to get other users instead of yourself
				$player = $this->waitingUsers[array_rand($this->waitingUsers)];
				$player = &$player;
			}
			$matchid = rand(999, 9999999);
			while(isset($this->matches[$matchid])) {
				$matchid = rand(999, 9999999);
			}
			// opencp
			/*
			$objClient->sendPacket("%xt%tmm%0%-1%" . $objClient->username . "%" . $player->username . "%");
			$objClient->sendPacket("%xt%scard%0%998%{$matchid}%2%");
			$objClient->matchID = $matchid;
			$objClient->seatID = 0;
			$player->sendPacket("%xt%tmm%0%-1%" . $player->username . "%" . $objClient->username . "%");
			$player->sendPacket("%xt%scard%0%998%{$matchid}%2%");
			$player->matchID = $matchid;
			$player->seatID = 1; */
			
			$objClient->sendData("%xt%tmm%0%-1%" . $objClient->strName . "%" . $player->strName . "%");
			$player->sendData("%xt%tmm%0%-1%" . $player->strName . "%" . $objClient->strName . "%");
			$objClient->sendData("%xt%scard%0%998%{$matchid}%2%");
			$player->sendData("%xt%scard%0%998%{$matchid}%2%");
			
			$objClient->multiplayer = array();
			$objClient->multiplayer["tableID"] = $matchid;
			$objClient->multiplayer["seatID"] = 0;
			$player->multiplayer = array();
			$player->multiplayer["tableID"] = $matchid;
			$player->multiplayer["seatID"] = 1;
			
			$this->matches[$matchid] = array("player0" => &$objClient, "player1" => &$player, "max" => 2, "isReady" => false, "tmp" => array(), "decks" => array(0 => array(), 1 => array()));
			
			$this->removeFromWaitingList($objClient);
			$this->removeFromWaitingList($player);
		} else {
			// not enough
		}
	}
	
	function playWithSensei(&$objClient) {
		$matchid = rand(1, 9999999);
		while(isset($this->matches[$matchid])) {
			$matchid = rand(1, 9999999);
		}
		//$objClient->sendPacket("%xt%tmm%0%-1%" . $objClient->username . "%Sensei%");
		$objClient->sendPacket("%xt%scard%0%998%{$matchid}%1%");
		$objClient->matchID = $matchid;
		$objClient->seatID = 0;
		
		$this->matches[$matchid] = array("player0" => &$objClient, "player1" => null, "max" => 1, "tmp" => array());
	}
	
	function checkWin ($matchID, &$u1, &$u2, $cards) {
		// checking player 1's cards first
		$fire = $this->matches[$u->matchID]["tmp"]["p" . $u1->seatID]["f"];
		$water = $this->matches[$u->matchID]["tmp"]["p" . $u1->seatID]["w"];
		$snow = $this->matches[$u->matchID]["tmp"]["p" . $u1->seatID]["s"];
		// checking player 2's cards last
	}
	
	function addCard ($cardId, $u, $uz, $cards) {
		$this->matches[$u]["tmp"]["p" . $uz]["lastCard"] = $cardId;
		$this->matches[$u]["tmp"]["p" . $uz][$cards[$cardId]["attribute"]][] = $cardId;
	}
	
	function isPowerCard ($cardId, $cards) {
		if ($cards[$cardId]["power"] != 0) {
			return true;
		}
		return false;
	}
	
	function sendToPlayers ($matchID, $packet) {
		if(isset($this->matches[$matchID]) && isset($matchID)) {
			$this->matches[$matchID]["player0"]->sendData($packet);
			$this->matches[$matchID]["player1"]->sendData($packet);
		}
		return;
	}
		
	function checkByValue($e1, $e2) {
		if($e1 < $e2) {
			return $e2;
		} else if($e1 > $e2) {
			return $e1;
		} else if($e1 == $e2) {
			return 0;
		}
	}
	
	function checkByElement($e1, $e2) {
		if($e1 == "f" && $e2 == "w" || $e1 == "w" && $e2 == "s" || $e1 == "s" && $e2 == "f") { // fire vs water || water vs snow || snow vs fire
			return $e2;
		} else if($e1 == "w" && $e2 == "f" || $e1 == "s" && $e2 == "w" || $e1 == "f" && $e2 == "s") {
			return $e1;
		}
	}
	
	function getPowerCard($card) {
		switch ($card["power"]) {
			case 1:
			case 2:
			case 3:
			case 13:
			case 14:
			case 15:
			case 16:
			case 17:
			case 18:
				return 0;
			break;
			case 4:
			case 5:
			case 6:
			case 7:
			case 8:
			case 9:
			case 10:
			case 11:
			case 12:
			case 19:
			case 20:
			case 21:
			case 22:
			case 23:
			case 24:
				return 1;
			break;
		}
		return 0;
	}
	
	function checkCard ($c, $cc, $cards, $matchID) { // $c == my card | $cc == other card
		$myCard = $objCrumbs["card"];
		$otherCard = $cc["card"];
		echo $myCard;
		if($this->isPowerCard($myCard, $cards)) {
			if ($this->checkByElement($cards[$otherCard]["attribute"], $cards[$myCard]["attribute"]) != $cards[$myCard]["attribute"]) {
				$this->sendToPlayers($matchID, "%xt%zm%{$matchID}%judge%{$cc['seat']}%-1%");
				return;
			}
			$this->sendToPlayers($matchID, "%xt%zm%{$matchID}%power%{$objCrumbs['seat']}%{$cc['seat']}%{$cards[$myCard]['power']}%");
		} elseif ($this->isPowerCard($otherCard, $cards)) {
			if ($this->checkByElement($cards[$otherCard]["attribute"], $cards[$myCard]["attribute"]) != $cards[$otherCard]["attribute"]) {
				$this->sendToPlayers($matchID, "%xt%zm%{$matchID}%judge%{$objCrumbs['seat']}%-1%");
				return;
			}
			$this->sendToPlayers($matchID, "%xt%zm%{$matchID}%power%{$cc['seat']}%{$objCrumbs['seat']}%{$cards[$otherCard]['power']}%");
		}
		if ($cards[$myCard]["attribute"] == $cards[$otherCard]["attribute"]) {
			if ($this->checkByValue($cards[$otherCard]["damage"], $cards[$myCard]["damage"]) == $cards[$myCard]["damage"]) {
				$this->sendToPlayers($matchID, "%xt%zm%{$matchID}%judge%{$objCrumbs['seat']}%-1%");
				return;
			} else {
				if($cards[$otherCard]["damage"] == $cards[$myCard]["damage"]) {
					$this->sendToPlayers($matchID, "%xt%zm%{$matchID}%judge%-1%-1%");
					return;
				}
				$this->sendToPlayers($matchID, "%xt%zm%{$matchID}%judge%{$cc['seat']}%-1%");
				return;
			}
		} else {
			if ($this->checkByElement($cards[$otherCard]["attribute"], $cards[$myCard]["attribute"]) == $cards[$myCard]["attribute"]) {
				$this->sendToPlayers($matchID, "%xt%zm%{$matchID}%judge%{$objCrumbs['seat']}%-1%");
				return;
			} else {
				$this->sendToPlayers($matchID, "%xt%zm%{$matchID}%judge%{$cc['seat']}%-1%");
				return;
			}
		}
	}
	
}

?>
