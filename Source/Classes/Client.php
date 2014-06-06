<?php
class Client extends ClientBase{
	public $identified = false;
	public $extRoomID = -1;
	public $intRoomID = -1;
	public $intClientID;
	public $intID;
	public $intX = 0;
	public $intY = 0;
	public $intFrame = 1;
	public $strName = "";
	public $isMuted = false;
	public $time = NULL;
	public $lastItemTime = 0;
	public $recentitems = 0;
	public $crumbsDone = 9001;
	public $crumbsCache = array();
	public $multiplayer = array();
	public $puffleCrumbsDone = 9001;
	public $PuffleCrumbsCache = array();
	protected $defaults = array(
		'email' => "",
		'registerIP' => "",
		'registertime' => 0,
		'color' => 1,
		'head'	=> 0,
		'face'	=> 0,
		'neck'	=> 0,
		'body'	=> 0,
		'hands'	=> 0,
		'feet'	=> 0,
		'pin'	=> 0,
		'photo'	=> 0,
		'items'	=> array(1),
		'coins'	=> 10000,
		'credits' => 1000,
		'isModerator'	=>	0,
		'isBanned_'	=> 0,
		'buddies' => array(),
		'ignore' => array(),
		'stamps' => array(),
		'stampColor' => 1,
		'stampHighlight' => 1,
		'stampPattern' => -1,
		'stampIcon' => 1,
		'mood' => "Welcome!",
		'igloo' => 1,
		'music' => 0,
		'floor' => 0,
		'furniture' => array(),
		'postcards' => array(),
		'igloos'    => array(1), 
		'redemptions' => array(),
		'isEPF'    => true,
		'isJailed' => false,
		'postcardTime' => 0,
		'PMTime' => 0,
		'SQLTime' => 0,
		'badges' => '1',
		'roomT' => 0,
		'roomFurniture' => "",
		'puffles' => array(),
		'kills' => 0,
		'cards' => array(),
		'ninja' => array(
						'rank' => 0,
						'percent' => 0,
						'fireRank' => 0,
						'firePercent' => 0,
						'waterRank' => 0,
						'waterPercent' => 0
					),
		'namecolor' => '', 
		'nameglow' => '', 
		'bubblecolor' => '', 
		'ringcolor' => '', 
		'sizex' => '100', 
		'sizey' => '100', 
		'alpha' => '', 
		'blend' => '', 
		'bubbleglow' => '', 
		'ringglow' => '', 
		'rotation' => '', 
		'penguinglow' => '',
		'statuscolor' => '0xFFFFFF',
		'statusglow' => '',
		'snowglow' => '',
		'bubbletext' => '',
		'speed' => 1,
		'tracetracker' => 0,
		'MedalsTotal' => 5000,
		'MedalsUnused' => 500,
		'health' => '15',
		'sprite' => '0',
		'qrank' => '0',
		'qtype' => '0',
		'sbpoints' => 0,
		'totalpoints' => 0,
		'lives' => 5,
	);
	
	
	function c($p, $set = NULL){
		if($set !== NULL){
			$this->setPlayerCrumb($p, $set);
			return $this->p[$p] = $set;
		}
		$this->p[$p] = $this->getPlayerCrumb($p);
		return $this->p[$p];
		$this->resetDetails();
	}
	
	function addCard ($intID) {
		$a   = $this->c("cards");
		$a[] = $intID;
		$a   = array_values($a);
		$this->c("cards", $a);
	}
	
	function getSprite() {
		$objCrumbs = $this->getPlayerCrumbs();
		$sprite = $objCrumbs["sprite"];
		return $sprite;
	}
	
	function getPlayerCrumb($p, $intID = null){
		if($intID === null){
			$intID = $this->intID;
		}
		if($p == "id"){
			return $intID;
		}
		$a = $this->getPlayerCrumbs();
		if(key_exists($p, $a))
			return $a[$p];
		else
			return NULL;
	}
		
	function sendToID($arrData, $intID){
		usleep(0);
		if(key_exists($intID, $this->parent->objClientsByID)){
			$objClient = $this->parent->objClientsByID[$intID];
			$objClient->sendData($arrData);
		}
	}
	
	public function adoptPuffle($type, $strName) {
		$strPuffles = getData('SELECT puffles FROM accs WHERE ID="' . $this->intID . '"');
		$strPuffles = $strPuffles[0]['puffles'];
		$intID = rand(1, 100);
		$puffleString = "{$intID}|{$strName}|{$type}|100|100|100,";
		$coins = $this->delCoins(800);
		$this->sendData("%xt%pn%-1%" . $coins . "%" . $puffleString . "%");
		$puff = $strPuffles . "%" . $puffleString;
		$type2 = 750 + $type;
		setData("UPDATE accs SET puffles=CONCAT(puffles, '{$puff}') WHERE ID='" . $this->intID . "';");
		$this->sendData(makeXt("ms", $this->intRoomID, $this->c("coins"), 1));
		$this->sendToID('%xt%mr%-1%sys%0%111% '  . $strName . '%' . time() . '%14%', $this->intID);
	}
	
	function GetPostcards(){
		$postcards = array_reverse($this->c("postcards"));
		$strCards = "";
		foreach($postcards as $uID => $pData){
			$strCards .= "%{$pData['strFrom']}|{$pData['intFrom']}|{$pData['intType']}|{$pData['strMsg']}|{$pData['intDate']}|{$pData['intUniqueID']}";
		}
		return substr($strCards, 1);
	}
	
	function joinRoom($intRoomID, $intClientID) {
		$this->parent->handleJoinRoom(array(4 => $intRoomID, 0, 0), "", $intClientID);
	}
	
	function updateIgloo($intIglooID){
		if(!is_numeric($intIglooID)) return;
		if($this->extRoomID == $this->intID + 1000){
			if(!is_numeric($intIglooID)){
				return;
			}
			$coins = $this->parent->arrIgloos[$intIglooID]["cost"];
			$a = $this->c("igloos");
			if(!in_array($intIglooID, $a)){
				$a[] = $intIglooID;
				$a = array_values($a);
				$this->c("igloos", $a);
				if($coins !== NULL){
					$current = $this->c("coins");
					if($current >= $coins){
						$current = $current - $coins;
						$current = $this->c("coins", $current);
					} else {
						$this->sendError();
					}
				} else {
					$coins = $this->c("coins");
				}
				$this->sendData(makeXt("au", $this->intRoomID, $intIglooID, $current));
			}
		}
	}
	
	function updateMusic($intMusicID){
		if(!is_numeric($intMusicID)) return;
		$this->c("music", $intMusicID);
	}

	function updateFloor($item){
		if(!is_numeric($item)) return;
		if($this->extRoomID == $this->intID + 1000){
			if(!is_numeric($item)){
				return;
			}
			$coins = $this->parent->arrFloors[$item]["cost"];
			if(!is_numeric($item)) return;
			$a = $this->c("floor");
			$this->c("floor", $item);
			if($coins !== NULL){
				$current = $this->c("coins");
				if($current >= $coins) {
					$current = $current - $coins;
					$current = $this->c("coins", $current);
				} else {
					$this->sendError();
				}
			} else {
				$coins = $this->c("coins");
			}
			$this->sendData(makeXt("ag", $this->intRoomID, $item, $current));
		}
	}
	
	function updateMood($strMood){
		$this->c("mood", $strMood);
		$this->sendData("%xt%umo%". $this->intID . "%". $strMood ."%");
	}
	
	
	function getBuddyList() {
		$b = $this->c("buddies");
		$s = "";
		foreach ($b as $buddy) {
			if (validID($buddy)) {
				$s .= "$buddy|" . getName($buddy) . "|";
				if ($this->parent->isOnline($buddy)) {
					$this->parent->objClientsByID[$buddy]->sendData(makeXt("bon%-1", $this->intID));
					$s .= "1%";
				} else {
					$s .= "0%";
				}
			}
		}
		return $s;
	}
	
	function getIgnoreList() {
		$n = $this->c("ignore");
		$s = "";
		foreach ($n as $ignore) {
			if (validID($ignore))
				$s .= "$ignore|" . getName($ignore) . "%";
		}
		return $s;
	}
	
	function MakeEPF() {
		$this->c("isEPF", true);
	}
	
	function addRedemptionItem($item) {
		if (!is_numeric($item)) {
			$this->sendError(410);
			return;
		}
		$item = trim($item);
		if (!is_numeric($item))
			return;
		settype($item, "integer");
		$a = $this->c("items");
		if (!is_array($a))
			$a = array(
				$this->c("color")
			);
		if (!in_array($item, $a)) {
			$a[] = $item;
			$a   = array_values($a);
			$this->c("items", $a);
		}
	}
	function checkJailed() {
		$a = getData("SELECT crumbs FROM accs WHERE ID=" . dbEscape($this->intID), "single");
		if(!$a)
			return BAD_USER;
		$a = unserialize($a['crumbs']);
		return $a["isJailed"];
	}   
	
	function getItems($array = false) {
		$a = $this->c("items");
		if ($array) {
			return $a;
		}
		if (!in_array($c = $this->c("color"), $a)) {
			$a[] = $c;
			$this->c("items", $a);
		}
		$s = "";
		if (!is_array($a)) {
			$a = array(
				$this->c("color")
			);
		}
		foreach ($a as $i) {
			$s .= "$i%";
		}
		$s .= "%%";
		$s = str_replace("%%%", "", $s);
		return $s;
	}
	
	function isWearing($intID) {
		$clothes = array(
			$this->c("head"),
			$this->c("face"),
			$this->c("neck"),
			$this->c("body"),
			$this->c("hands"),
			$this->c("feet")
		);
		foreach ($clothes as $value) {
			if ($value == $intID) {
				return true;
			}
		}
	}
	
	public function setQrank($level) {
		$this->c("qrank", $level);
	}
	public function setQType($level) {
		$this->c("qtype", $level);
	}
		
	function addStamp($stamp) {
		$a = $this->c("stamps");
		if (!in_array($stamp, $a)) {
			$a[] = $stamp;
			$a   = array_values($a);
			$this->c("stamps", $a);
			$this->sendData(makeXt("sse", "-1", $stamp, $this->c("coins")));
			$this->parent->log->log("{$this->strName} added stamp $stamp!");
		}
	}
	
	function setStampBookCoverDetails($color, $highlight, $pattern, $icon) {
		$args = func_get_args();
		foreach ($args as $arg) {
			if (!is_numeric($arg)) {
				return;
			}
		}
		$this->c("stampColor", $color);
		$this->c("stampHighlight", $highlight);
		$this->c("stampPattern", $pattern);
		$this->c("stampIcon", $icon);
	}
	
	public function sendStamp($s) {
		$this->sendData("%xt%ssb%{$this->intRoomID}%{$s}%");
	}
		
	function addFurniture($item){
		$time = time();
		if(!is_numeric($item)){
			$this->sendError(410);
			return;
		}
		$coins = $this->parent->arrFurn[$item]["cost"];
		$a = $this->c("furniture");
		if(!is_array($a))
			$a = array();
		if(!key_exists($item, $a)){
			$a[$item] = 1;
			$this->c("furniture", $a);
			if($coins !== NULL){
				$coins = -$coins;
				$coins += $this->c("coins");
				$coins = $this->c("coins", $coins);
			} else{
				$coins = $this->c("coins");
			}
			$this->sendData(makeXt("af", $this->intRoomID, $item, $coins));
			$this->parent->log->log("{$this->strName} added furniture $item!");
		} else {
			$a[$item]++;
			$this->c("furniture", $a);
			if($coins !== NULL){
				$coins = -$coins;
				$coins += $this->c("coins");
				$coins = $this->c("coins", $coins);
			} else {
				$coins = $this->c("coins");
			}
			$this->sendData(makeXt("af", $this->intRoomID, $item, $coins));
			$this->parent->log->log("{$this->strName} added item $item!");
		}
	}

	function getFurniture(){
		$furn = $this->c("furniture");
		$s = "";
		foreach($furn as $key => $val){
			$s .= "%$key|$val";
		}
		$s = substr($s, 1);
		return $s;
	}
		
	function saveRoomFurniture($str){
		$this->c("roomFurniture", $str);
	}
	
	public function toyFunction($boolStuff) {
		if($boolStuff){
			$this->sendData("%xt%at%" . $this->intRoomID . "%" . $this->intID . "%1%1%");
		} else {
			$this->sendData("%xt%rt%" . $this->intRoomID . "%" . $this->intID . "%");
		}
	}
                
	function addItem($item) {
		if(!is_numeric($item)){
			$this->sendError(410);
			return;
		}
		$coins = $this->parent->arrCrumbs[$item]["cost"];
		
		if(in_array($item, $this->parent->patched)) {
			if(!$this->isModerator) 
				return;
		}
		$item = trim($item);
		$time = time();
		$this->recentitems = 0;
		$this->lastItemTime = $time + 1;
		if(!is_numeric($item)) return;
		$a = $this->c("items");
		if(!is_array($a))
			$a = array($this->c("color"));
		if(!in_array($item, $a)){
			$a[] = $item;
			$a = array_values($a);
			$this->c("items", $a);
			if($coins !== NULL){
				$current = $this->c("coins");
				if($current >= $coins){
					$current = $current - $coins;
					$current = $this->c("coins", $current);
				} else {
					$this->sendData('%xt%error%%%<font color=\"#FFFFFF\">Not enough coins!</font>%Okay%sys%');
				}
			} else {
				$coins = $this->c("coins");
			}
			$this->sendData(makeXt("ai", $this->intRoomID, $item, $current));
			$this->parent->log->log("{$this->strName} added item $item!");
		}
	}

	function addCoins($intCoinAmount){
		$intCoins = $this->c("coins") + $intCoinAmount;
		$this->c("coins", $intCoin);
	}

	function delCoins($intCoins){
		$intCurrentCoins = $this->c("coins");
		$intNewCoins = $intCurrentCoins - $intCoins;
		$this->c("coins", $intNewCoins);
	}
	
	function onIdentify($intID, $strName){
		$this->intID = $intID;
		$this->strName = $strName;
		$this->loginName = $strName;
		$this->parent->objClientsByID[$intID] =& $this;
		$this->identified = true;
		$this->requests = array();
		$a = $this->getPlayerCrumbs();
		$diff = false;
		foreach($this->defaults as $k => $d){
			if(!key_exists($k, $a) || $a[$k] === NULL){
				$diff = true;
				$a[$k] = $d;
			}
		}
		if(!$a["isModerator"]){
			foreach($a['items'] as $key => $i){
				if(in_array($i, $this->parent->patched)){
					$diff = true;
					unset($a['items'][$key]);
				}
			}
			if($diff){
				$a['items'] = array_values($a['items']);
			}
			foreach($this->parent->trArt as $arttype){
				if(in_array($a[$arttype], $this->parent->patched)){
					$a[$arttype] = 0;
					$diff = true;
				}
			}
		}
		foreach($a as $k => $v){
			if(!key_exists($k, $this->defaults)){
				$diff = true;
				unset($a[$k]);
			}
		}
		foreach($a['buddies'] as $key => $tb){
			if(!validID($tb)){
				$diff = true;
				unset($a['buddies'][$key]);
			}
		}
		if(!$a['registertime']){
			$a['registertime'] = time();
			$diff = true;
		}
		$this->isModerator = $a['isModerator'];
		if($diff){
			$this->setPlayerCrumbs($a);
		}
	}
	
	function buildPlayerString(){
		$objCrumbs = $this->getPlayerCrumbs();
		$s = $this->intID;//0
		$s .= "|" . $this->strName;//1
		$s .= "|" . 1;//2
		$s .= "|" . $objCrumbs["color"];//3
		$s .= "|" . $objCrumbs["head"];//4
		$s .= "|" . $objCrumbs["face"];//5
		$s .= "|" . $objCrumbs["neck"];//6
		$s .= "|" . $objCrumbs["body"];//7
		$s .= "|" . $objCrumbs["hands"];//8
		$s .= "|" . $objCrumbs["feet"];//9
		$s .= "|" . $objCrumbs["pin"];//10
		$s .= "|" . $objCrumbs["photo"];//11
		$s .= "|" . $this->intX;//12
		$s .= "|" . $this->intY;//13
		$s .= "|" . $this->intFrame;//14
		$s .= "|" . 1;//15
		$s .= "|" . (1 ? $this->getBadgeNum($objCrumbs['badges']) : 16);//16
		$s .= "|" . 0;//17 SECRET STUFF HERE REMOVED ;)
		$s .= "|" . 0;//18
		$s .= "|" . 0;//19
		$s .= "|" . $objCrumbs["mood"];//20
		$s .= "|" . $objCrumbs["snowglow"];//21
		$s .= "|" . 1;//21
		return $s;
		break;
	}
	
	function getBadgeNum($rank){
		return ($rank * 147);
	}

	function getPlayerCrumbs(){
		if($this->crumbsDone <= 500){
			++$this->crumbsDone;
			return $this->crumbsCache;
		}
		$a = (getData("SELECT crumbs FROM accs WHERE ID=" . dbEscape($this->intID), "single"));
		if(!$a)
			return BAD_USER;
		$a = unserialize($a['crumbs']);
		if(!is_array($a))
			return BAD_USER;
		$this->crumbsCache = $a;
		$this->crumbsDone = 0;
		return $a;
	}
	
	function getPlayerIDByName($strName){
		$a = (getData("SELECT id FROM accs WHERE name=" . dbEscape($strName), "single"));
		if(!$a)
			return BAD_USER;
		return $a;
	}
	
	function getPlayerCrumbsByName($strName){
		if($this->crumbsDone <= 500){
			++$this->crumbsDone;
			return $this->crumbsCache;
		}
		$a = (getData("SELECT crumbs FROM accs WHERE name=" . dbEscape($strName), "single"));
		if(!$a)
			return BAD_USER;
		$a = unserialize($a['crumbs']);
		if(!is_array($a))
			return BAD_USER;
		$this->crumbsCache = $a;
		$this->crumbsDone = 0;
		return $a;
	}
	
	function getPuffleCrumbs(){
		if($this->crumbsDone <= 500){
			++$this->crumbsDone;
			return $this->crumbsCache;
		}
		$a = (getData("SELECT puffle FROM accs WHERE ID=" . dbEscape($this->intID), "single"));
		if(!$a)
			return BAD_USER;
		$a = unserialize($a['puffle']);
		if(!is_array($a))
			return BAD_USER;
		$this->crumbsCache = $a;
		$this->crumbsDone = 0;
		return $a;
	}
	
	function setPlayerCrumb($p, $s = NULL){
		$a = $this->getPlayerCrumbs();
		$a[$p] = $s;
		return $this->setPlayerCrumbs($a);
	}
	
	function setPlayerCrumbs($a){
		if(is_array($a)){
			$this->crumbsCache = $a;
			setData("UPDATE accs SET crumbs = '" . dbEscape(serialize($a)) . "' where ID = '" . dbEscape($this->intID) . "'");
		}
	}

	public function sendError($e = 410){
		$this->sendData("%xt%e%" . $this->intRoomID . "%$e%");
	}
	
	public function sendFakeError($func_Size, $func_Message, $func_Label, $func_Error) {
		$func_data = func_get_args();
		$func_data[1] = str_replace(array('$strName', '$intID', '$$'), array($this->strName, $this->intID, '$'), $func_Message);
		$this->sendData('%xt%err%-1%' . join('%', $func_data) . '%');
	}
	
	public function sendXt(){
		$a = func_get_args();
		if(!is_array($a))
			return false;
		$send = "%xt%";
		foreach($a as $s){
			$send .= $s . "%";
		}
		return $this->sendData($send);
	}
	
	public function sendPacket($pck){
		$this->sendData("{$pck}");
    }
	
	function __destruct(){
		self::$num--;
		unset($this->parent);
	}
}

?>
