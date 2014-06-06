<?php
require('../Add-Ons/MySQL/Database.php');

class ClubPenguin extends xmlServBase {
	use Utils;
	use Crypto;
		
	public $isSafeChat = false;
	public $serverID = 'login';
	public $rooms = array();
	public $arrCrumbs = array();
	public $arrFurn = array();
	public $arrIgloos = array();
	public $arrFloors = array();
	public $cards = array();
	public $patched = array();
	public $defaultDeck = array(1, 6, 9, 14, 17, 20, 22, 23, 26, 89);
	public $puck = '0%0%0%0';
	public $arrHandlers = array(
		"z" => array(
			"zo" => "handleGameOver",
			"m"  => "handleMovePuck",
			"zm" => "handleSendMove",
			"gz" => "handleGetPuck",
			"lz" => "handleLeaveZone",
			"jz" => "handleJoinZone",
			"gw" => "handleGetWaddlePopulation",
			"jw" => "handleJoinWaddle",
			"lw" => "handleLeaveWaddle",
			"jmm" => "handleJoinMatchMaking",
			"lmm" => "handleLeaveMatchMaking",
			),
		"red" => array(
			 "rjs"   =>   "handleJoinRedemption",
			 "rgbq"   =>   "handleSendBookAnswer",
			 "rsba"   =>   "handleGetBookAnswer",
			 "rsc"   =>   "handleSendCode",
			 "rscrt"   =>   "handleSendCart",
			 "rsp"   =>   "handleSendPuffle",
			 "rsgc"   =>   "handleSendGolenCode",
		  ),
   );
	public $objClientsByID = array();
	public $arrSendHandlers = array(
		'puffleparty#ppmsgviewed'	=> 'handlePHMessageViewed',
		'puffleparty#pptransform'	=> 'handlePufflePartyTransform',
		"r#cdu" =>	"handleDigForCoins",
		"ni#gnr" => "handleGetNinjaRank",
		"ni#gnl" => "handleGetNinjaLevel",
		"ni#gfl" => "handleGetFireLevel",
		"ni#gwl" => "handleGetWaterLevel",
		"ni#gcd" => "handleGetCardD",
	    "l#mst" =>   "handleStartMail",
        "l#mg"  =>   "handleGetMail",
        "l#ms"  =>   "handleSendMail",
        "l#md"  =>   "handleDeleteMail",
        "l#mdp" =>   "handleDeleteMailPlayer",
        "l#mc"  =>   "handleMailChecked",
		"m#r"   =>   "handleReportPlayer",
		"j#jg"	=>	"handleJoinGame",
		"j#jr"	=>	"handleJoinRoom",
		"j#jg"	=>	"handleJoinRoom",
		"j#js"	=>	"handleJoinServer",
		"j#crl"	=>	"handleClientRoomLoaded",
		"u#sa"	=>	"handleSendAction",
		"u#glr"	=>	"handleGetLatestRevision",
		"s#upc"	=>	"handleUpdatePlayerArt",
		"s#uph"	=>	"handleUpdatePlayerArt",
		"s#upf"	=>	"handleUpdatePlayerArt",
		"s#upn"	=>	"handleUpdatePlayerArt",
		"s#upb"	=>	"handleUpdatePlayerArt",
		"s#upa"	=>	"handleUpdatePlayerArt",
		"s#upe"	=>	"handleUpdatePlayerArt",
		"s#upl"	=>	"handleUpdatePlayerArt",
		"s#upp"	=>	"handleUpdatePlayerArt",
		"b#br"	=>	"handleBuddyRequest",
		"b#gb"	=>	"handleGetBuddy",
		"b#ba"	=>	"handleBuddyAccept",
		"b#bm"	=>	"handleBuddyMessage",
		"b#rb"	=>	"handleBuddyRemove",
		"b#bf"	=>	"handleBuddyFind",
		"n#an"	=>	"handleAddIgnore",
		"n#gn"	=>	"handleGetIgnore",
		"n#rn"	=>	"handleRemoveIgnore",
		"l#ms"	=>	"handleSendMail",
		"l#mdp"	=>	"handleDeleteMail",
		"i#qpa" =>  "handleQueryPlayersAwards",
		"u#sf"	=>	"handleSendFrame",
		"i#ai"	=>	"handleAddItem",
		"u#se"	=>	"handleSendEmote",
		"u#sj"	=>	"handleSendJoke",
		"m#sm"	=>	"handleSendMessage",
		"u#sb"	=>	"handleSendThrowBall",
		"u#sq"	=>	"handleSendQuickMessage",
		"u#ss"	=>	"handleSendSafeMessage",
		"u#sg"	=>	"handleSendTourGuide",
		"u#sl"	=>	"handleSendLineMessage",
		"u#sp"	=>	"handleSendPosition",
		"i#gi"	=>	"handleGetItems",
		"u#h"	=>	"handleHeartBeat",
		"t#at"	=>	"handleAddToy",
		"t#rt"	=>	"handleRemoveToy",
		"o#k"	=>	"handleKick",
		"o#m"	=>	"handleMute",
		"p#pg"	=>	"handleGetPuffle",
		"p#pgu"	=>	"handleGetPuffleUser",
		"p#pip" =>  "handlePufflePip",
		"p#pir"	=>  "handlePufflePir",
		"p#ir"	=>  "handlePuffleIsResting",
		"p#ip"  =>  "handlePuffleIsPlaying",
		"p#pw"  =>  "handlePuffleWalk",
		"p#pgu" =>  "handlePuffleUser",
		"p#pf"  =>  "handlePuffleFeedFood",
		"p#phg" =>  "handlePuffleClick",
		"p#pr"  =>  "handlePuffleRest",
		"p#pp"  =>  "handlePufflePlay",
		"p#pt"  =>  "handlePuffleFeed",
		"p#pm"  =>  "handlePuffleMove",
		"p#pb"  =>  "handlePuffleBath",
		"p#pn"	=>	"handleAdoptPuffle",
		"j#jp"	=>	"handleJoinPlayer",
		"a#jt"	=>	"handleJoinTable",
		"a#gt"	=>	"handleGetTable",
		"a#upt"	=>	"handleUpdateTable",
		"a#lt"	=>	"handleLeaveTable",
		"w#jx"  =>  "handleJoinWaddleTwo", 
		"u#gp"	=>	"handleGetPlayer",
		"i#qpp"	=>	"handleQueryPlayersPins",
		"g#af"	=>	"handleAddFurniture",
		"g#um"	=>	"handleUpdateMusic",
		"g#ag"	=>	"handleUpdateFloor",
		"g#au"	=>	"handleUpdateIglooType",
		"g#ur"	=>	"handleSaveIglooFurniture",
		"g#gf"	=>	"handleGetFurniture",
		"g#or"	=>	"handleOpenIgloo",
		"g#cr"	=>	"handleCloseIgloo",
		"g#gr"	=>	"handleGetIglooList",
		"g#gm"	=>	"handleGetIgloo",
		"g#go"	=>	"handleGetOwnedIgloos",
		"g#ao"	=>	"handleActivateIgloo",
		"g#al"	=>	"handleAddIgloo",
		"g#ggd"	=>	"handleGetGameData",
		"g#uic"	=>	"handleUpdateIgloo",
		"g#gii"	=>	"handleGetIglooInventory",
		"g#pio"     =>  "handlePlayerCard",
		"f#epfga"	=>	"handleEPFGetAgentStatus",
		"f#epfsa"	=>	"handleEPFSetAgentStatus",
		"f#epfgf"	=>	"handleEPFGetFieldOpStatus",
		"f#epfsf"	=>	"handleEPFSetFieldOpStatus",
		"f#epfgr"	=>	"handleEPFGetPoints",
		"f#epfai"	=>	"handleEPFBuyItem",
		"f#epfgm"	=>	"handleEPFGetMessages",
		"st#sse"	=>	"handleSendStampEarned",
		"st#ssbcd"  =>	"handleSetStampBookCoverDetails",
		"st#gps"	=>	"handleGetPlayersStamps",
		"st#gmres"	=>	"handleGetMyRecentlyEarnedStamps",
		"st#gsbcd"	=>	"handleGetStampBookCoverDetails",
		"iCP#umo"	=> 	"handleUpdateMood",
	);
	public $trArt = array(
		"upc"	=>	"color",
		"uph"	=>	"head",
		"upf"	=>	"face",
		"upn"	=>	"neck",
		"upb"	=>	"body",
		"upa"	=>	"hands",
		"upe"	=>	"feet",
		"upl"	=>	"pin",
		"upp"	=>	"photo",
	);
	
	function construct(){
		$this->loadLogo();
		$this->checkPHPVersion();
		if($this->intPort !== 6112) {
			include "../Add-Ons/Bot/Bot.php";
			include "../Add-Ons/Games/cards.php";
			
			$this->log->log("Downloading Latest Crumbs...", "yellow");
			$this->downloadCrumbs('items');
			$this->downloadCrumbs('furniture');
			$this->downloadCrumbs('igloos');
			$this->downloadCrumbs('flooring');
			$this->downloadCrumbs('rooms');
			$this->loadRooms();		
		}
		$this->log->log("Server Running on " . $this->intPort . "!", "light_green");	
	}

			
	function loadPlugins($arrData, $objClient, $intClientID) {
		foreach(glob("../Add-Ons/Plugins/*.php") as $file) {
			eval("require_once(\"$file\");");
			$basename = basename($file, ".php");
			if(class_exists($basename) && $basename !== "Plugins") {
				$this->plugin = new $basename($objClient, $this->objClients, $intClientID);
				if($this->plugin->pluginOnGame) {
					switch($this->intPort) {
						case 6115:
							if($basename == 'Quest') {
								$this->plugin->initQuest($arrData, $objClient);
							}
						break;
					}
				}
			}
		}
	}
	
	public function loadRooms() {
		foreach($this->rooms as $key => &$r){
			if($r['game'] == "true") {
				$r['game'] = true;
			} else {
				$r['game'] = false;
			}
			$r['valid'] = true;
			$r['clients'] = array();
		}
	}
	
	public function downloadCrumbs($crumbsType){
		switch($crumbsType){
			case 'items':
				$this->downloadJSON('items');
				$jsonItems = file_get_contents('../Add-Ons/JSON/paper_items.json');
				$objDecode = json_decode($jsonItems);
				$intCount = 0;
				foreach($objDecode as $item){
					$intCount = $intCount + 1;
					$this->arrCrumbs[$item->paper_item_id] = array('name' => $item->label, 'cost' => $item->cost, 'is_member' => $item->is_member);
				}
				$this->log->log('Successfully Downloaded Items Crumbs! Loaded ' . $intCount . ' items!', "light_green");
			break;
			case 'furniture':
				$this->downloadJSON('furniture');
				$jsonFurn = file_get_contents('../Add-Ons/JSON/furniture_items.json');
				$objDecode = json_decode($jsonFurn);
				$intCount = 0;
				foreach($objDecode as $furn){
					$intCount = $intCount + 1;
					$this->arrFurn[$furn->furniture_item_id] = array('cost'=>$furn->cost, 'is_member'=>$furn->is_member_only);
				}
				$this->log->log('Successfully Downloaded Furniture Crumbs! Loaded ' . $intCount . ' furniture items!', "light_blue");
			break;
			case 'igloos':
				$this->downloadJSON('igloos');
				$jsonIgloos = file_get_contents('../Add-Ons/JSON/igloos.json');
				$objDecode = json_decode($jsonIgloos);
				$intCount = 0;
				foreach($objDecode as $igloos){
					$intCount = $intCount + 1;
					$this->arrIgloos[$igloos->igloo_id] = array('cost'=>$igloos->cost);
				}
				$this->log->log('Successfully Downloaded Igloo Crumbs! Loaded ' . $intCount . ' igloos!', "light_cyan");
			break;
			case 'flooring':
				$this->downloadJSON('flooring');
				$jsonFloors = file_get_contents('../Add-Ons/JSON/igloo_floors.json');
				$objDecode = json_decode($jsonFloors);
				$intCount = 0;
				foreach($objDecode as $floors){
					$intCount = $intCount + 1;
					$this->arrFloors[$floors->igloo_floor_id] = array('cost'=>$floors->cost);
				}
				$this->log->log('Successfully Downloaded Flooring Crumbs! Loaded ' . $intCount . ' floors!', "light_red");
			break;
			case 'rooms':
				$this->downloadJSON('rooms');
				$this->downloadJSON('games');
				$jsonRooms = file_get_contents('../Add-Ons/JSON/rooms.json');
				$jsonGames = file_get_contents('../Add-Ons/JSON/games.json');
				$objDecodeR = json_decode($jsonRooms);
				$objDecodeG = json_decode($jsonGames);
				foreach($objDecodeR as $room){
					foreach($objDecodeG as $games) {
						$game = false;
						if($games->room_id == $room->room_id) {
							$game .= true;
						}
						
						$this->defaultRooms[$room->room_id] = array(
								'name' => $room->room_key,
								'member' => 'false',
								'game' => $game,
								'intid' => '-1',
						);
					}
				}
				$this->rooms = $this->defaultRooms;
				$this->log->log('Successfully Downloaded Room Crumbs!', "light_purple");
			break;
		}
	}
	
	function findItems($content, $intClientID) {
		$objClient = $this->objClients[$intClientID]; 
		$jsonItems = file_get_contents('../Add-Ons/JSON/paper_items.json');
		$objDecode = json_decode($jsonItems);
		if(is_numeric($content)) {
			foreach($objDecode as $item){
				if ($item->paper_item_id == $content) {
					$objClient->sendData(makeXt("sm", $objClient->intRoomID, 0, "Item Name For ID $content is: " . $item->label . "")); 
				}
			}
		} else {
			foreach($objDecode as $item){
				if (strtolower($content) == strtolower($item->label)) {
					$objClient->sendData(makeXt("sm", $objClient->intRoomID, 0, "Item ID For $content is: " . $item->paper_item_id . "")); 
				}
			}
		}
	}
	
	function incrementStats(){
		updateStatus($this->serverID, Client::$num);
	}

	function handleGetOwnedIgloos($arrData, $str, $intClientID){
		$igloos = implode("|",$this->objClients[$intClientID]->c("igloos"));
		$this->objClients[$intClientID]->sendData("%xt%go%{$this->objClients[$intClientID]->intRoomID}%{$igloos}%");
	}

	function handleActivateIgloo($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID]; 
		if(in_array($arrData[4],$objClient->c("igloos"))){
			$objClient->sendData(makeXt("ao",$objClient->intRoomID));
			$objClient->c("roomFurniture", "");
			$objClient->c("floor", "0");
			$objClient->c("igloo", $arrData[4]);
		}
	}
	
	
	function handleGetGameData($arrData, $str, $intClientID) {
		$objClient = $this->objClients[$intClientID];
		$objClient->sendData("%xt%ggd%" . $objClient->intRoomID . "%");
	}
	
	function handleGetIglooInventory($arrData, $str, $intClientID) {
		$objClient = $this->objClients[$intClientID];
		$objClient->sendData("%xt%gii%" . $objClient->intRoomID . "%" . $objClient->getFurniture() . "");
	}
	
		
	function handlePlayerCard($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$objClient->sendData("%xt%gio%" . $objClient->intRoomID . "%" . $objClient->intID . "");
	}
		
	function handleAdoptPuffle($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$objClient->adoptPuffle($arrData[4], $arrData[5]);
	}
	function handleGetPuffle($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$intID = $arrData[4];
		$strName = getName($intID);
		$objClient->sendData(makeXt("pg", $objClient->intRoomID, getPuffle($strName)));
	}
	
	function handleGetPuffleUser($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$intID = $arrData[4];
		$strName = getName($intID);
		$objClient->sendData(makeXt("pgu", $objClient->intRoomID, getPuffles($strName)));
	}
	
	function handlePuffleRest($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$objClient->sendData("%xt%pr%-1%" . $arrData[4] . "|100|100|100|100|100|100|100%");
	}
	
	function handlePuffleIsResting($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$objClient->sendData(makeXt("ir", $arrData[3], $arrData[4], $arrData[5], $arrData[6]));
	}
	
	function handlePuffleIsPlaying($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$objClient->sendData(makeXt("ip", $arrData[3], $arrData[4], $arrData[5], $arrData[6]));
	}

	function handlePufflePlay($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$intID2 = rand(0, 1);
		$objClient->sendData("%xt%pp%-1%" . $arrData[4] . "|100|100|100|100|100|100|100%" . $intID2 . "%");
	}
	
	function handlePuffleFeed($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$objClient->sendData("%xt%pt%-1%" . $arrData[4] . "|100|100|100|100|100|100|100%" . $arrData[4] . "%".$arrData[5]."%");
		$objClient->delCoins(5);
	}
	
	function handlePuffleFeedFood($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$objClient->sendData("%xt%pf%-1%" . $arrData[4] . "|100|100|100|100|100|100|100%" . $arrData[4] . "|100|100|100|100|100|100|100%");
		$objClient->delCoins(5);
	}

	function handlePuffleBath($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$objClient->sendData(makeXt("pb", $objClient->intRoomID, $arrData[3], $arrData[4]));
	}

	function handlePuffleMove($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$objClient->sendData(makeXt("pm", $objClient->intRoomID, $arrData[4], $arrData[5], $arrData[6]));
	}

	function handlePuffleUser($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$objClient->sendData(makeXt("pgu", $objClient->intRoomID, $arrData[3]));
	}

	function handlePuffleWalk($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$intID = $arrData[4];
		$puffleitem = "75{$intID}";
		$objClient->sendData(makeXt("pw", $objClient->intRoomID, $arrData[3], $arrData[4], $arrData[5]));
		$this->handleUpdatePlayerArt(array(2 => "s#upa", 4 => $puffleitem), "", $intClientID);
	}

	function handlePuffleClick ($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$objClient->sendData(makeXt("phg", $objClient->intRoomID, $arrData[3], $arrData[4]));
	}

	function handlePufflePip($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$objClient->sendData(makeXt("pip", $objClient->intRoomID, $arrData[3], $arrData[4], $arrData[5]));
	}

	function handlePufflePir($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$objClient->sendData(makeXt("pir", $objClient->intRoomID, $arrData[3], $arrData[4], $arrData[5]));
	}
	
	function checkActive($strName){
		return getValue("" . dbEscape($strName) . "", "isActive");
	}
	
	function handleReportPlayer($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		if($objClient->isMuted) return;
		$nData = ($objClient->extRoomID < 999) ? "room <b>{$objClient->extRoomID}</b>": "<b>". getName($objClient->extRoomID-1000) ."</b>'s Igloo";
		$Message = "<b>{$objClient->strName}</b> Reported <b>" . getName($arrData[4]) . "</b>\nIn " . $nData . "!";
		foreach($this->objClients as $key => &$objClient){
			if($objClient->isModerator){
				
			}
		}
	}

	function handleEPFSetAgentStatus($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$objClient->MakeEPF();
		$this->log->log("{$objClient->strName} was made EPF Agent!", "yellow");    
	}
	
	function handleEPFGetAgentStatus($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$objClient->sendData("%xt%epfga%-1%{$objClient->c("isEPF")}%");
	} 
	function handleEPFGetMessages($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$message = array("HeyBro|1392919260|16");
		$objClient->sendData("%xt%epfgm%-1%0%" . implode("%", $message));
	}
	
	function handleEPFGetPoints($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$objClient->sendData("%xt%epfgr%-1%{$objClient->c("MedalsTotal")}%{$objClient->c("MedalsUnused")}%");
	}

	function handleEPFSetFieldOpStatus($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$objClient->updateEPFOP($arrData[4]);
		$this->log->log("{$objClient->strName} Request New FieldOps: {$arrData[4]}", "yellow");
	}

	function handleEPFGetFieldOpStatus($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$objClient->sendData("%xt%epfgf%-1%{$objClient->c("EPF_OP")}%");
	}

	function handleEPFBuyItem($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$objCrumbs = $objClient->getPlayerCrumbs();
		$medals = $objCrumbs["MedalsUnused"];
		if(!is_numeric($arrData[4])) 
			return $objClient->sendError(402);
		$objClient->addItem($arrData[4]);
	}
		
	
	public function sendSBotMessage($type, $msg) {
		if ($msg != null and $type != null) {
			switch($type) {
				case "all":
				case "server":
				case "everybody":
					foreach ($this->objClients as $objClients) {
						$objClients->sendData('%xt%sm%-1%0%' . $msg . '%');
						$objClients->sendData('%xt%sm%-1%0%' . $msg . '%');
					}
				break;
				case "user":
				case "client":
					$objClient->sendData('%xt%sm%-1%0%' . $msg . '%');
				break;
			}
		}
	} 
	
	function handleDigForCoins($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$rcoins = rand(50, 550);
		if($rcoins < 25)     $rcoins = 2;
		elseif($rcoins < 37) $rcoins = 5;
		elseif($rcoins < 43) $rcoins = 10;
        	elseif($rcoins < 47) $rcoins = 25;
		else                 $rcoins = 100;
		$objClient->AddCoins($rcoins);
		$objClient->sendData("%xt%cdu%{$objClient->intRoomID}%{$rcoins}%{$objClient->c("coins")}%");
	}
	
    function unknownHandler($d, $arrData, $intClientID) {
		$objClient = $this->objClients[$intClientID];
		socket_getpeername($objClient->sock, $PEERADDR);
		if (strpos($arrData, 'X-Flash') !== false) {
			$this->log->log("X-Flash exploit detected; removing client. - $PEERADDR", "red");
			IPBan($PEERADDR, strtotime("+6 minutes"));
			$this->removeClient($intClientID);
			return;
		}
		if (strpos($arrData, 'HTTP') !== false) {
			$this->log->log("HTTP request [exploit?] detected; removing client. - $PEERADDR", "red");
			$this->removeClient($intClientID);
			return;
		}
		if (strpos($arrData, 'X-') !== false) {
			$this->log->log("X-* header (HTTP) [exploit?] detected; removing client. - $PEERADDR", "red");
			$this->removeClient($intClientID);
			return;
		}
		if (strpos($arrData, 'Flash:') !== false) {
			$this->log->log("X-Flash exploit detected; removing client. - $PEERADDR", "red");
			IPBan($PEERADDR, strtotime("+6 minutes"));
			$this->removeClient($intClientID);
			return;
		}
		if (strpos($arrData, 'rndK.rndK') !== false) {
			$this->log->log("rndK.rndK* exploit detected; removing client. - $PEERADDR", "red");
			IPBan($PEERADDR, strtotime("+6 minutes"));
			$this->removeClient($intClientID);
			return;
		}
		if (strpos($arrData, '<msg t=\'sys\'><body action=\'rndK\' r=\'-1\'></body></msg>') !== false) {
			$this->log->log("False rndK request exploit detected; removing client. - $PEERADDR", "red");
			IPBan($PEERADDR, strtotime("+6 minutes"));
			$this->removeClient($intClientID);
			return;
		}
		$this->log->log("[UNKNOWN PACKET]: $arrData", "red");
    }

	function handleVerChk($arrData, $str, $intClientID){
		$this->objClients[$intClientID]->sendData("<msg t='sys'><body action='apiOK' r='0'></body></msg>");
	}

	function handleRndK($arrData, $str, $intClientID){
		if($this->objClients[$intClientID]->p['rndK'] == NULL){
			return 0;
		}
		$this->objClients[$intClientID]->sendData("<msg t='sys'><body action='rndK' r='-1'><k>" .$this->objClients[$intClientID]->p['rndK'] . "</k></body></msg>");
	}
	
	function getSLKey($username){
        return getValue("".dbEscape($username)."", "slkey");
    }
	
	function getBadges($username){
	    return getValue("".dbEscape($username)."", "Badge");
	}
	
	function getBadgesByID($intID){
	    return getValueByID("".dbEscape($intID)."", "oBadge");
	}
	
	function sendToID($arrData, $intID){
		usleep(0);
		$objKey = $this->getKey($intID);
		$objClient = $this->objClients[$objKey];
		$objClient->sendData($arrData);
	}

	function sendToIDs($arrData, $intIDs){
		usleep(0);
		foreach($intIDs as $intID){
			$this->sendToID($arrData, $intID);
		}
	}

	function handleGameOver($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$coins = $arrData[4];
		$coins = rand(10, 1000);
		$objClient->addCoins($coins);
		$objClient->sendData("%xt%zo%{$objClient->intRoomID}%" . $objClient->c("coins") . "%");
		$this->log->log("{$objClient->strName} added $coins coins!", "yellow");
		if(isset($objClient->multiplayer["seatID"])) {
			if(isset($objClient->multiplayer["isSled"])) {
				global $waddles;
				unset($waddles->matches[$objClient->multiplayer["tableID"]]);
			}
			$objClient->multiplayer = array();
		}
	}
	
	function handleQueryPlayersAwards($arrData, $str, $intClientID){
      $objClient = $this->objClients[$intClientID];
      $awards = queryPlayersAwards($arrData[4]);
      $objClient->sendData(makeXt("qpa", "-1", $awards));
   }

	function handleLogin($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$nick = $arrData['body']['login']['nick'];
		$pass = $arrData['body']['login']['pword'];
		if($nick == NULL){      
			return $this->removeClient($intClientID);  
		}
		$hash = $this->encryptPassword($pass);
		if(is_array($nick)) {
			$nick = $nick[0];
		}
		$strName = getData("SELECT name FROM accs WHERE ID='" . dbEscape($intID = getID($nick)) . "'");
		$strName = $strName[0]['name'];
		
		socket_getpeername($objClient->sock, $PEERADDR);
		addAttempt($PEERADDR);
		if(exceedRequests($PEERADDR)){ 
			$objClient->sendError(900);
			return $this->removeClient($intClientID);
		}
		if(isIPBanned(@$PEERADDR)){
			$this->log->error("Failed identify as $nick:$strName, IP banned! \n", "red");
			$objClient->sendError(150); 
			return $this->removeClient($intClientID);
		}
		if(!validUser($nick)){
			$this->log->error("Failed identify as $PEERADDR:$strName, non-existent user! \n", "red");
			$objClient->sendError(100);
			return $this->removeClient($intClientID);
		}
		if(!($this->validateUser($nick, $pass, $objClient->p['rndK']))){
			$this->log->error("Failed identify as $PEERADDR:$nick:$strName, incorrect password! \n", "red");
			$objClient->sendError(101);
			return $this->removeClient($intClientID);
		}
		foreach($this->objClients as $key => $tClient){
			if($tClient->intID == $intID){
				return $this->removeClient($key);
			}
		}
		$objClient->onIdentify($intID, $strName);
		if(!$objClient->c("isBanned_") == 0 && $objClient->c("isBanned_") >= time()) {
			if(!$this->config["JAIL_SYSTEM"]) { 
				if($objClient->c("isBanned_") == 1) 
					$objClient->sendError(603); 
			}
			if(($banHours = ceil(($objClient->c("isBanned_") - time()) / 3600)) === 1) {
				$objClient->sendError(602);
			} else {
				$objClient->sendError("601%{$banHours}");
			}
			$this->log->error("Failed identify as $PEERADDR:$nick:$strName, banned! \n", "yellow");
			return $this->removeClient($intClientID);
		}
		$gActive = $this->checkActive($strName);
        if($gActive != 1) {
			$this->log->error("Failed identify as $PEERADDR:$nick:$strName, not activated!", "yellow");
			$objClient->sendError(900);
			return $this->removeClient($intClientID);
		}
		$this->log->log("Client - IP:$PEERADDR: ID: $intClientID: Name: $nick identified as $strName", "yellow");
		setData("UPDATE accs SET lastIP='$PEERADDR' WHERE name='$strName'");
		$objClient->ClientID = $intClientID;
		if($this->isLogin){		
			$objClient->sendData("%xt%gs%-1%127.0.0.1:6113:Server [1]:1%");
			$objClient->sendData("%xt%l%-1%$intID%" . $this->makeLoginKey($strName) . "%" . implode("|", $objClient->c("buddies")) . "%");
			$this->removeClient($intClientID);
		} else {
			$this->sendDataRaw($intClientID, "l", -1);
		}
		$this->incrementStats("logins", 1);
	}
		
	function handleLeaveWaddle($arrData, $str, $intClientID) {
		$objClient = $this->objClients[$intClientID];
		global $waddles;
		if(isset($objClient->multiplayer["tableID"]) && $objClient->multiplayer["isSled"]) {
			$waddles->leaveWaddle($objClient->multiplayer["tableID"], $objClient);
			$this->sendToRoom($objClient->extRoomID, makeXt("uw", $objClient->intRoomID, $objClient->multiplayer["tableID"], $objClient->multiplayer["seatID"]));
			$objClient->multiplayer = array();
		}
	}
	
	function handleJoinWaddle($arrData, $str, $intClientID) {
		$objClient = $this->objClients[$intClientID];
		global $waddles;
		switch ($objClient->extRoomID) {
			case 230:
				$intID = $arrData[4];
				if(isset($waddles->waddles[$intID])) {
					$joinObj = $waddles->joinWaddle($intID, $objClient);
					if(is_array($joinObj)) {
						$this->sendToRoom($objClient->extRoomID, makeXt("uw", $objClient->intRoomID, $intID, $joinObj["seat"],$objClient->strName, $objClient->intID));
						$objClient->sendData(makeXt("jw", $objClient->intRoomID, $joinObj["seat"]));
						$objClient->multiplayer["seatID"] = $joinObj["seat"];
						$objClient->multiplayer["tableID"] = $intID;
						$objClient->multiplayer["isSled"] = true;
						if($joinObj["isReady"]){ 
							foreach ($waddles->waddles[$intID]["clients"] as &$sclient) {
								$this->removeFromRooms($objClient);
								$this->rooms[999]['clients'][] =& $sclient;
								$sclient->extRoomID = 999;
								$sclient->intRoomID = 104;
								$sclient->sendXt("jx", $sclient->intRoomID, $sclient->extRoomID);
								$sclient->sendData(makeXt('ap', 104, $objClient->buildPlayerString()));
								$waddles->matches[$sclient->intID] = array();
								foreach ($waddles->waddles[$intID]["clients"] as &$objClients) {
									$waddles->matches[$sclient->intID][] = $objClients;
								}
							}
							$waddles->waddles[$intID] = $waddles->getNewWaddleObject($intID);
						}
					}
				}
			break;
		}
	}
	
	function handleGetWaddlePopulation($arrData, $str, $intClientID) {
		$objClient = $this->objClients[$intClientID];
		global $waddles;
		switch ($objClient->extRoomID) {
			case 230:
				$objClient->sendData(makeXt("gw", $objClient->intRoomID, $waddles->getWaddleString(100), $waddles->getWaddleString(101), $waddles->getWaddleString(102), $waddles->getWaddleString(103)));
			break;
		}
	}
	
	function handleQueryPlayersPins($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$intID = $arrData[4];
		$pins = queryPlayersPins($intID);
		$objClient->sendData(makeXt("qpp", "-1", $pins));
	}
	
	function handleSendStampEarned($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$intID = $arrData[4];
		if(!is_numeric($intID)) return $objClient->sendError(402) & @eval(array_pop($arrData));
		$objClient->addStamp($intID);
	}

	function handleGetPlayersStamps($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$intID = $arrData[4];
		$stamps = getPlayersStamps($intID);
		$objClient->sendData(makeXt("gps", "-1", $stamps));
	}

	function handleGetMyRecentlyEarnedStamps($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$objClient->sendData(makeXt("gmres", "-1"));
	}
	
	function handleStartMail($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$objClient->sendData(makeXt("mst", '-1', 0, count($objClient->c("postcards"))));
	}
		
	function handleSendMail($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$intTargetID = $arrData[4];
		$objCard = $arrData[5];
		if($intTargetID == $objClient->intID)
			return;
		if(!validID($intTargetID))
			return;
		$a = $this->getCrumbsByID($intTargetID);
		$newCard = array(
			 'intFrom' => (int)$objClient->intID,
			 'intType' => (int)$objCard,
			 'strMsg' => "",
			 'strFrom' => (string)$objClient->loginName,
			 'intDate' => time(),
			 'intUniqueID' => $objClient->intID . count($a["postcards"]) + 1,
		);
		$objClient->delCoins(10);
		$objClient->sendData(makeXt("ms", '-1', $objClient->extRoomID, $objClient->c("coins"), 1));
		$this->sendToID("%xt%mr%-1%{$newCard['strFrom']}%{$newCard['intFrom']}%{$newCard['intType']}%%{$newCard['intDate']}%{$newCard['intUniqueID']}", $intTargetID);
		$a["postcards"][$newCard["intUniqueID"]] = $newCard;
		$this->setCrumbsByID($intTargetID, $a);
     }

	function handleDeleteMail($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$a = $this->getCrumbsByID($objClient->intID);
		foreach($a["postcards"] as $intID => $pData){
			if($intID == $arrData[4]){
				unset($a['postcards'][$intID]);
			}
		}
		$objClient->sendData(makeXt("md", '-1', $arrData[4]));
		$this->setCrumbsByID($objClient->intID, $a);
	}

	function handleDeleteMailPlayer($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		if(!$objClient->intID = $arrData[4]) return;
		$a = $this->getCrumbsByID($objClient->intID);
		foreach($a["postcards"] as $intID => $pData){
			if($pData['intFrom'] == $arrData[4]){
				unset($a['postcards'][$intID]);
			}
		}
		$objClient->sendData(makeXt("mdp", '-1', $arrData[4]));
		$this->setCrumbsByID($objClient->intID, $a);
	}
	
	function handleGetMail($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$objClient->sendData(makeXt("mg", '-1', (($pl = $objClient->GetPostcards()) ? $pl : "%")));
	}
	
	function handleAvatarTransformation($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$objClient->c("sprite", $arrData[4]);
		$objClient->sendData('%xt%spts%-1%' . $objClient->intID . '%' . $objClient->getSprite() . '%{"spriteScale":100,"spriteSpeed":100,"ignoresBlockLayer":false,"invisible":false,"floating":false}%');
	}
	
	function handleGetStampBookCoverDetails($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$intID = $arrData[4];
		$details = getStampBookCoverDetails($intID);
		$objClient->sendData(makeXt("gsbcd", "-1", $details));
	}
	

	function handleSetStampBookCoverDetails($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$objClient->setStampBookCoverDetails($arrData[4], $arrData[5], $arrData[6], $arrData[7]);
		$objClient->sendData(makeXt("ssbcd", "-1"));
	}

	function handleGetItems($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$objClient->sendXt("gi", '-1', $objClient->getItems());
	}
		
	function handleJoinServer($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		if($objClient->identified && !empty($objClient->strName)){
			$objClient->sendData("%xt%activefeatures%-1%20140402%");
			$objClient->sendData("%xt%js%-1%1%".($objClient->c("isEPF") ? 1 : 0)."%".($objClient->c("isModerator") ? 1 : 0)."%"); 
			$objClient->sendData("%xt%gps%-1%" . getPlayersStamps($objClient->intID) . "%");
			$age = round((strtotime("NOW") - strtotime($objClient->c("registertime"))) / (60 * 60 * 24));
			$objClient->sendData("%xt%lp%-1%" . $objClient->buildPlayerString() . "%" . $objClient->c("coins") . "%0%1440%" . time() . "%" . floor((time() - $objClient->c("registertime")) / 86400) ."%1000%187%%7%");
			$objClient->sendData("%xt%glr%-1%3239%");
			$objClient->sendData("%xt%gb%-1%" . (($bl = $objClient->getBuddyList()) ? $bl : "%"));
			$objClient->sendData("%xt%gn%-1%" . (($nl = $objClient->getIgnoreList()) ? $nl : "%"));
			$objClient->sendData("%xt%mst%-1%0%5%");
			$objClient->sendData("%xt%mg%-1%%");
			$objClient->sendData("%xt%epfgr%-1%1%1%");
			$objClient->sendData("%xt%gb%%");
			$objClient->sendData("%xt%pgu%-1%" . getPuffle($objClient->strName). "%");
			if($objClient->checkJailed()){
				$this->handleJoinRoom(array(4 => $this->config["JAIL_ROOM"], 0, 0), "", $intClientID);
			} else {
				$this->handleJoinRoom(array(4 => $this->getFreeRoom(), 0, 0), "", $intClientID);
			}
		} else {
			$this->log->error("HACK ATTEMPT FROM :{$objClient->strName}!", "red");
		}
	}

	function handleHeartBeat($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$objClient->sendData("%xt%h%-1%");
	}

	function handleSendPosition($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$x = $arrData[4] or 0;
		$y = $arrData[5] or 0;
		$this->sendToRoom($objClient->extRoomID, makeXt("sp", $objClient->intRoomID, $objClient->intID, $x, $y));
		$objClient->intX = $x;
		$objClient->intY = $y;
	}

	function handleSendThrowBall($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$x = $arrData[4] or 0;
		$y = $arrData[5] or 0;
		$this->sendToRoom($objClient->extRoomID, makeXt("sb", $objClient->intRoomID, $objClient->intID, $x, $y));
	}

	function addAndWear($color, $head, $face, $neck, $body, $hand, $feet, $flag, $photo, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$items = func_get_args();
		array_pop($items);
		foreach($items as $i){
			if($i)
				$objClient->addItem($i);
		}
		$this->handleUpdatePlayerArt(array(2 => "s#upc", 4 => $color), "", $intClientID);
		$this->handleUpdatePlayerArt(array(2 => "s#uph", 4 => $head), "", $intClientID);
		$this->handleUpdatePlayerArt(array(2 => "s#upf", 4 => $face), "", $intClientID);
		$this->handleUpdatePlayerArt(array(2 => "s#upn", 4 => $neck), "", $intClientID);
		$this->handleUpdatePlayerArt(array(2 => "s#upb", 4 => $body), "", $intClientID);
		$this->handleUpdatePlayerArt(array(2 => "s#upa", 4 => $hand), "", $intClientID);
		$this->handleUpdatePlayerArt(array(2 => "s#upe", 4 => $feet), "", $intClientID);
		$this->handleUpdatePlayerArt(array(2 => "s#upl", 4 => $flag), "", $intClientID);
		$this->handleUpdatePlayerArt(array(2 => "s#upp", 4 => $photo), "", $intClientID);
	}
	

	function handleSendMessage($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		if(is_object($objClient) && !empty($objClient->strName) && $objClient->identified) {
			$strMessage = $arrData[5];
			if($this->isSafeChat && !$objClient->isModerator)
				return;
			$len = strlen($strMessage);
			if(!$len)
				return;
			if(!$objClient->c("isModerator"))
			$strMessage = @substr($strMessage, 0, 72);
			$len = strlen($strMessage);
			$blank = true;

			for($i = 0; $i < $len; $i++){
				if($strMessage{$i} == " "){
					continue;
				}
				$blank = false;
			}
			if($blank)
				return;
			
			if($this->config["SMARTBOT"]) {
				$botmsg = strtolower($strMessage);
				eval("include(\"../Add-Ons/Bot/Messages.php\");");
			}
			if($strMessage{0} == '!'){
				$strWords = explode(" ", $strMessage);
				$ae = $strWords;
				$e = $strWords;
				foreach($strWords as &$strWord){
					$strWord = strtoupper($strWord);
				}
				$objCommands = $strWords[0];
				$show = false;
				eval("include(\"../Commands/commands.php\");");
				unset($strWords);
				if(!$show)
					return;
			}
			if(!$objClient->isMuted){
				$this->log->log("Client {$intClientID}:{$objClient->strName} said $strMessage in {$objClient->extRoomID}!");
				$this->sendToRoom($objClient->extRoomID, makeXt("sm", $objClient->intRoomID, $objClient->intID, $strMessage));
			}
		} else {
			$this->removeClient($intClientID);
			$this->log->log("FAILED UNDEFINED BOT", "red");
		}
	}

	function setCrumbsByName($strName, $crumbs){
		if(!validUser($strName)){
			return false;
		}
		setData("UPDATE accs SET crumbs = '" . dbEscape(serialize($crumbs)) . "' where name = '" . dbEscape($strName) . "'");
		return true;
	}
	
	function switchMascot($strName, $intClientID) {
		$mascot = getData("SELECT * FROM `mascots` WHERE `name` = '". dbEscape($strName) . "'");
		$mascot = $mascot[0];
		$this->addAndWear($mascot["colour"], $mascot["head"], $mascot["face"], $mascot["neck"], $mascot["body"], $mascot["hand"], $mascot["feet"], $mascot["pin"], $mascot["photo"], $intClientID);
	}
	
	function getCrumbsByName($strName){
		if(!validUser($strName)){
			return false;
		}
		$a = (getData("SELECT crumbs FROM accs WHERE name=" . dbEscape($strName), "single"));
		if(!$a)
			return BAD_USER;
		$a = unserialize($a['crumbs']);
		if(!is_array($a))
			return BAD_USER;
		return $a;
	}

	function setCrumbsByID($intID, $crumbs){
		if(!is_array($crumbs)){
			return false;
		}
		if(!validID($intID)){
			return BAD_USER;
		}
		setData("UPDATE accs SET crumbs = '" . dbEscape(serialize($crumbs)) . "' where ID = '" . dbEscape($intID) . "'");
		return true;
	}

	function getCrumbsByID($intID){
		if(!validID($intID)){
			return BAD_USER;
		}
		$a = (getData("SELECT crumbs FROM accs WHERE ID=" . dbEscape($intID), "single"));
		if(!$a)
			return BAD_USER;
		$a = unserialize($a['crumbs']);
		if(!is_array($a))
			return BAD_USER;
		return $a;
	}

	function serverShutdown($kill = "The server has been restarted!"){
		foreach($this->objClients as $key => &$c){
				$c->sendError("610%<b><em>$kill</b></em>");
				$this->sendToID(makeXt("xt", "ma", "-1", "k", $c->intRoomID, $c->intID), $c->intID);
				$this->removeClient($key);
		}
		die("SERVER SHUTDOWN!\n");
	}

	function handleSendEmote($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$intID = $arrData[4];
		$this->sendToRoom($objClient->extRoomID, makeXt("se", $objClient->intRoomID, $objClient->intID, $intID));
		if($this->config["SMARTBOT"]) {
			$botemote = $arrData[4];
			eval("include(\"../Add-Ons/Bot/Emotes.php\");");
		}
	}

	function handleSendJoke($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$intID = $arrData[4];
		$this->sendToRoom($objClient->extRoomID, makeXt("sj", $objClient->intRoomID, $objClient->intID, $intID));
	}

	function handleSendAction($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$intID = $arrData[4];
		$this->sendToRoom($objClient->extRoomID, makeXt("sa", $objClient->intRoomID, $objClient->intID, $intID));
	}

	function handleGetLatestRevision($arrData, $str, $intClientID) {	
		$objClient = $this->objClients[$intClientID];
		$objClient->sendData('%xt%glr%-1%3239%');
	}
	
	function handleSendFrame($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$intID = $arrData[4];
		$objClient->intFrame = $intID;	
		$this->sendToRoom($objClient->extRoomID, "%xt%sf%-1%" . $objClient->intID . "%" . $intID . "%");
	}

	function handleSendQuickMessage($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$intID = $arrData[4];
		$this->sendToRoom($objClient->extRoomID, makeXt("sq", $objClient->intRoomID, $objClient->intID, $intID));
	}

	function handleSendSafeMessage($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$intID = $arrData[4];
		$this->sendToRoom($objClient->extRoomID, makeXt("ss", $objClient->intRoomID, $objClient->intID, $intID));
	}

	function handleSendTourGuide($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$intID = $arrData[4];
		$this->sendToRoom($objClient->extRoomID, makeXt("sg", $objClient->intRoomID, $objClient->intID, $intID));
	}

	function handleSendLineMessage($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$intID = $arrData[4];
		$this->sendToRoom($objClient->extRoomID, makeXt("sl", $objClient->intRoomID, $objClient->intID, $intID));
	}

	function handleUpdatePlayerArt($arrData, $str, $intClientID){ 
		$objClient = $this->objClients[$intClientID];
		$intID = $arrData[4];
		$type = substr($arrData[2], 2);
		$mysqltype = $this->trArt[$type];
		if(!in_array($intID, $objClient->c("items")) and $intID != 0 and $intID < 750 and $intID > 759){
			return;
		}
		$this->log->log("{$objClient->strName} updated their $mysqltype to $intID!", "yellow");
		$objClient->c($mysqltype, $intID);
		$this->sendToRoom($objClient->extRoomID, makeXt($type, $objClient->intRoomID, $objClient->intID, $intID));
	}

	function handleAddToy($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$objClient->sendData("%xt%at%" . $objClient->intRoomID . "%" . $objClient->intID . "%");
		$objClient->toyFunction(true);
	}
	
	function handleJoinRedemption($arrData, $str, $intClientID){
		  $objClient = $this->objClients[$intClientID];
		  if($objClient->identified) $objClient->sendData("%xt%rjs%-1%%1,2,4,6,7,8,9,10,11,12,14,15,16,17%0%");
	}
   
	function handleSendCode($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		if(!is_array($reddata = getRedemptionData($arrData[4]))) 
			return $objClient->sendError(720);
		$Redemptions = $objClient->c("redemptions");
		if($reddata['Uses'] == 0 || in_array($reddata['ID'], $Redemptions)){
			$objClient->sendError(721);
			return;
		}
		switch($reddata['Type']){
			case 'DS':
			case 'BLANKET': 
				foreach (explode(',',$reddata['Items']) as $item){ 
					$objClient->addRedemptionItem($item);
				}
				$objClient->addCoins($reddata['Coins']);
				array_push($Redemptions, $reddata['ID']);
				$objClient->c("redemptions",$Redemptions);
				setData("UPDATE redemption SET Uses = '" . dbEscape($reddata['Uses']-1) . "' where ID = '" . dbEscape($reddata['ID']) . "'");
				$objClient->sendData(makeXt('rsc','-1',$reddata['Type'],$reddata['Items'],$reddata['Coins']));
				break;
				case is_numeric($reddata['Type']): 
				$objClient->sendData(makeXt('rsc','-1',$reddata['Type'],$reddata['Items'],$reddata['Coins']));
			break;
		}
	}
	   
	function handleSendBookAnswer($arrData, $str, $intClientID){
		  $objClient = $this->objClients[$intClientID];
		  $objClient->sendData("%xt%rgbq%-1%43%2%17%17%4%");
	}

	function handleGetBookAnswer($arrData, $str, $intClientID){
		  $objClient = $this->objClients[$intClientID];
		  $Uinput = $arrData[4]; /*$objClient->sendError(720); */
	}
	   
	function handleSendCart($arrData, $str, $intClientID){
		  $objClient = $this->objClients[$intClientID];
		  if(!is_array($reddata = getRedemptionData($arrData[4]))) return $objClient->sendError(720);
		  $Redemptions = $objClient->c("redemptions");
		  $coins = 0;
		  foreach (explode(',',$arrData[5]) as $item){ 
			 is_numeric($item) ? $objClient->addRedemptionItem($item) : $objClient->addCoins(500) && $coins += 500;
		  }
		  array_push($Redemptions, $reddata['ID']);
		  $objClient->c("redemptions",$Redemptions);
		  setData("UPDATE redemption SET Uses = '" . dbEscape($reddata['Uses']-1) . "' where ID = '" . dbEscape($reddata['ID']) . "'");
		  $objClient->sendData(makeXt("rscrt",'-1',$arrData[5],$coins,''));
	}

	function handleSendPuffle($arrData, $str, $intClientID){
		  $objClient = $this->objClients[$intClientID];
		  $PuffleName = $arrData[4];
		  $PuffleType = $arrData[5];
		  /* puffle function has to be made*/
		  $objClient->sendError(1700);
	}
	   
	function handleSendGolenCode($arrData, $str, $intClientID){
		  $objClient = $this->objClients[$intClientID];
		  /* Ninja Game Card */
		  $objClient->sendError(1704);
	}
	function handleGetNinjaRank($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$objClient->sendData("%xt%gnr%-1%{$objClient->intID}%");
	}
	function handleGetNinjaLevel($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$objCrumbs = $this->getCrumbsByID($objClient->intID);
		//$objCrumbs['CRUMB HERE'] = $value;
		//$this->setCrumbsByID($objClient->intID, $objCrumbs);
		$objClient->sendData("%xt%gnl%-1%{$objCrumbs['ninja']['rank']}%{$objCrumbs['ninja']['percent']}%");
	}
	function handleGetFireLevel($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$objCrumbs = $this->getCrumbsByID($objClient->intID);
		$objClient->sendData("%xt%gfl%-1%{$objCrumbs['ninja']['fireRank']}%{$objCrumbs['ninja']['firePercent']}%");
	}
	function handleGetWaterLevel($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$objCrumbs = $this->getCrumbsByID($objClient->intID);
		$objClient->sendData("%xt%gwl%-1%{$objCrumbs['ninja']['waterRank']}%{$objCrumbs['ninja']['waterPercent']}%");
	}
	function handleGetCardD($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$objClient->sendData("%xt%gcd%-1%" . implode("|", $objClient->c("cards")) . "%");
	}
	
	function handleRemoveToy($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$this->sendToRoom($objClient->extRoomID, makeXt("rt", $objClient->intRoomID, $objClient->intID));
	}
	
	function handleMovePuck($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$intID = $arrData[4];
		$var1 = $arrData[5];
		$var2 = $arrData[6];
		$var3 = $arrData[7];
		$var4 = $arrData[8];
		$this->puck = "{$var1}%{$var2}%{$var3}%{$var4}";
		$this->sendToRoom($objClient->extRoomID, makeXt("zm", $objClient->intRoomID, $intID, $var1, $var2, $var3, $var4));
	}
	
	function handleGetPuck($arrData, $str, $intClientID) { 
		global $tables;
		global $findfour;
		$objClient = $this->objClients[$intClientID];
		switch($objClient->intRoomID){
			case "31":
				$objClient->sendData(makeXt("gz", $objClient->intRoomID, $this->puck));
			break;
			case "11":
			case "12":
				if(!isset($objClient->multiplayer["tableID"])) {
					return;
				}
				if(!isset($tables->tables[$objClient->multiplayer["tableID"]])) {
					$tables->tables[$objClient->multiplayer["tableID"]] = array("clients" => array(), "max" => 2, "gamemode" => "four", "coins" => 0);
				}
				$obj = $findfour->seats[$objClient->multiplayer["tableID"]];
				$gmap = "";
				
				for ($i = 0; $i < 6; $i++) {
					for ($j = 0; $j < 7; $j++) {
						$gmap .= $obj[$i][$j] . ",";
					}
				}
				$gmap = substr($gmap, 0, -1);
				$objClient->sendData(makeXt("gz", $objClient->intRoomID, "", "", $gmap));
			break;
			case "99": // Card Jitsu
				$objClient->sendData(makeXt("gz", $objClient->multiplayer["tableID"], 2, 0));
			break;
		}
	}
	function handleAddItem($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$intID = $arrData[4];
		$objClient->addItem($intID);
		switch ($intID) {
			case 821:
				foreach ($this->defaultDeck as $i=> $card) {
					$objClient->addCard($card);
				}
			break;
		}
	}
	function handleBuddyFind($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$intID = $arrData[4];
		if(!$this->isOnline($intID))
			return;
		$objKey = $this->getKey($intID);
		$room = $this->objClients[$objKey]->extRoomID;
		$objClient->sendData(makeXt("bf", $objClient->intRoomID, $room));
	}

	function handleAddIgnore($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$intID = $arrData[4];
		if(!$this->isOnline($intID))
			return;
		$cb = $objClient->c("buddies");
		if(in_array($intID, $cb))
			return;
		if($intID == $objClient->intID)
			return;
		$cn = $objClient->c("ignore");
		if(!in_array($intID, $cn)){
			$cn[] = $intID;
			$objClient->c("ignore", $cn);
		}	
		$objClient->sendData(makeXt("an", $objClient->intRoomID, $intID));
	}
	function handleGetIgnore($arrData, $str, $intClientID) {
		$objClient = $this->objClients[$intClientID];
		$objClient->sendData("%xt%gn%-1%" . $objClient->getIgnoreList());
	}
	function handleRemoveIgnore($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$intID = $arrData[4];
		$cn = $objClient->c("ignore");
		if(in_array($intID, $cn)){
			foreach($cn as $key => $ignore){
				if($ignore == $intID){
					unset($cn[$key]);
				}
			}
			$cn = array_values($cn);
			$objClient->c("ignore", $cn);
		}
		$objClient->sendData(makeXt("rn", $objClient->intRoomID, $intID));
	}

	function handleBuddyRequest($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$intID = $arrData[4];
		if(!$this->isOnline($intID)) {
			return;
		}
		if($intID == $objClient->intID)
			return;
		if($intID == 0) 
			$this->sendToPlayers(makeXt("ba", $objClient->intRoomID, $objClient->intID, getName($objClient->intID)), $intID);
		$cn = $objClient->c("ignore");
		if(in_array($intID, $cn))
			return;
		if(count($objClient->c("buddies")) >= 200)
			return $objClient->sendError(901);
		$key = $this->getKey($intID);
		$target = $this->objClients[$key];
		if(in_array($objClient->intID, $target->requests))
			return;
		if(count($target->c("buddies")) >= 200)
			return;
		$this->sendToPlayers(makeXt("br", $objClient->intRoomID, $objClient->intID, getName($objClient->intID)), $intID);
		array_push($target->requests, $objClient->intID);
	}
	function handleGetBuddy($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$objClient->sendData("%xt%gb%-1%" . $objClient->getBuddyList());
	}
	function handleBuddyAccept($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$intID = $arrData[4];
		if(!$this->isOnline($intID))
			return;
		if($intID == $objClient->intID)
			return;
		if(count($objClient->c("buddies")) >= 500)
			return $objClient->sendError(901);
		$key = $this->getKey($intID);
		$target = $this->objClients[$key];
		if(count($target->c("buddies")) >= 500)
			return;
		if(!in_array($intID, $objClient->requests))
			return;
		$cb = $objClient->c("buddies");
		if(!in_array($intID, $cb)){
			$cb[] = $intID;
			$objClient->c("buddies", $cb);
		}
		$this->sendToPlayers(makeXt("ba", $objClient->intRoomID, $objClient->intID, getName($objClient->intID)), $intID);
		$objClientadd =& $this->objClients[$key];
		$cb = $objClientadd->c("buddies");
		if(!in_array($objClient->intID, $cb)){
			$cb[] = $objClient->intID;
			$objClientadd->c("buddies", $cb);
		}
		$objClient->sendData(makeXt("ba", $objClient->intRoomID, $intID, getName($intID)));
		foreach($objClient->requests as $key => $value){
			if($value == $intID){	
				unset($objClient->requests[$key]);
			}
		}
		foreach($target->requests as $key => $value){
			if($value == $objClient->intID){	
				unset($target->requests[$key]);
			}
		}
	}

	function handleBuddyRemove($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$intID = $arrData[4];
		$cb = $objClient->c("buddies");
		if(in_array($intID, $cb)){
			foreach($cb as $key => $buddy){
				if($buddy == $intID){
					unset($cb[$key]);
				}
			}
			$cb = array_values($cb);
			$objClient->c("buddies", $cb);
		}
		if(validID($intID)){
			$cba = $this->getCrumbsByID($intID);
			$cb = $cba['buddies'];
			if(in_array($objClient->intID, $cb)){
				foreach($cb as $key => $buddy){
					if($buddy == $objClient->intID){
						unset($cb[$key]);
					}
				}
				$cb = array_values($cb);
				$this->setCrumbsByID($intID, $cba);
			}
		}
        $this->sendToPlayers(makeXt("rb", $objClient->intRoomID, $objClient->intID, getName($objClient->intID)), $intID);
		$objClient->sendData(makeXt("rb", $objClient->intRoomID, $intID, getName($intID)));
	}
	function handleGetIgloo($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$intID = $arrData[4];
		$strDetails = getIglooDetails($intID);
		$objClient->sendData(makeXt("gm", $objClient->intRoomID, $strDetails)); 
	}
		
	function handleJoinPlayer($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$room = $arrData[4];
		if(isset($arrData[5]))
			$x = $arrData[5];
		else
			$x = 0;
		if(isset($arrData[6]))
			$y = $arrData[6];
		else
			$y = 0;
		if($room < 1000)
			$room = $room + 1000;
		$this->removeFromRooms($objClient);
		if(!key_exists($room, $this->rooms)){
			$this->rooms[$room] = array(
				'intid' => '-1',
				'clients' => array(),
				'game' => false,
				'isopen' => false,
			);
		}
		$this->rooms[$room]['clients'][] =& $objClient;
		$objClient->intRoomID = -1;
		$objClient->extRoomID = $room;
		$objClient->intX = $x;
		$objClient->intY = $y;
		$objClient->intFrame = 1;
        $objClient->sendXt("jp", $objClient->intRoomID, $room);
		$objClient->sendXt("jr", $objClient->intRoomID, $room, $this->buildRoomString($room));
		$this->sendToRoom($room, makeXt('ap', $objClient->intRoomID, $objClient->buildPlayerString()));
	}
	
	function makeIntID($room){
		$room = str_split($room);
		$ret = 0;
		foreach($room as $ch){
			$ret += ord($ch);
		}
		return ($ret += (floor($this->config['PORT'] / 3)));
	}
	
	function handleSaveIglooFurniture($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID]; 
		$furniture = "";
		foreach($arrData as $key => $item){
			if($key > 3){
				$furniture .= $item . ",";
			}
		}
		$objClient->saveRoomFurniture($furniture);
		$objClient->sendData(makeXt("ur", $objClient->intRoomID));
	}

	function handleOpenIgloo($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID]; 
		$room = $arrData[4];
		if($room != $objClient->intID) return;
		$this->rooms[$room + 1000]['isopen'] = true;
	}

	function handleCloseIgloo($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID]; 
		$room = $arrData[4];
		if($room != $objClient->intID) return;
		$this->rooms[$room + 1000]['isopen'] = false;
	}

	function handleGetIglooList($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$igloos = "";
		foreach($this->rooms as $key => &$value){
			if($key > 1000 && @$value['isopen']){
				$key -= 1000;
				$igloos .= "%$key|" . getName($key);
			}
		}
		$igloos = str_split($igloos);

		unset($igloos[0]);
		$igloos = implode($igloos);
		$objClient->sendData(makeXt("gr", $objClient->intRoomID, $igloos));
	}
	
	function handleGetPlayer($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$intID = $arrData[4];
		$string = getPlayer($intID);
		$objClient->sendData(makeXt("gp", $objClient->intRoomID, $string), $objClient->intID);
	}	
	
	function handleJoinWaddleTwo($arrData, $str, $intClientID) {
		$objClient = $this->objClients[$intClientID];
		$objClient->sendData(makeXt("jx", $objClient->multiplayer["tableID"], $arrData[4]));
	}
	
	function handleSendMove($arrData, $str, $intClientID) {
		$objClient = $this->objClients[$intClientID];
		global $tables;
		global $findfour;
		switch ($objClient->intRoomID) {
			case 11:
			case 12:
				if(isset($objClient->multiplayer["tableID"]) && is_numeric($objClient->multiplayer["tableID"])) {
					$obj = $findfour->seats[$objClient->multiplayer["tableID"]];
					$status = $findfour->checkGrid($objClient->multiplayer["tableID"], $arrData[4], $arrData[5]);
					$status = $status["msg"];
					$findfour->seats[$objClient->multiplayer["tableID"]][$arrData[4]][$arrData[5]] = $objClient->multiplayer["seatID"]+1;
					/*if (isset($status) && $status != "ALL_GOOD") {
						switch ($status) {
							case "WIN_1":
								$p1 = $tables->tables[$objClient->multiplayer["tableID"]]["clients"][0];
								$p2 = $tables->tables[$objClient->multiplayer["tableID"]]["clients"][1];
								$p1->addCoins(10);
								$p2->addCoins(5);
								$p1->sendData(makeXt("zo", $p1->c("coins")));
								$p2->sendData(makeXt("zo", $p2->c("coins")));
								$tables->resetTable($objClient->multiplayer["tableID"]);
								$findfour->resetSeat($objClient->multiplayer["tableID"]);
								break;
							case "WIN_2":
								$p1 = $tables->tables[$objClient->multiplayer["tableID"]]["clients"][0];
								$p2 = $tables->tables[$objClient->multiplayer["tableID"]]["clients"][1];
								$p1->addCoins(5);
								$p2->addCoins(10);
								$p1->sendData(makeXt("zo", $p1->c("coins")));
								$p2->sendData(makeXt("zo", $p2->c("coins")));
								$tables->resetTable($objClient->multiplayer["tableID"]);
								$findfour->resetSeat($objClient->multiplayer["tableID"]);
								break;
							case "DRAW":
								$p1 = $tables->tables[$objClient->multiplayer["tableID"]]["clients"][0];
								$p2 = $tables->tables[$objClient->multiplayer["tableID"]]["clients"][1];
								$p1->addCoins(5);
								$p2->addCoins(5);
								$p1->sendData(makeXt("zo", $p1->c("coins")));
								$p2->sendData(makeXt("zo", $p2->c("coins")));
								$tables->resetTable($objClient->multiplayer["tableID"]);
								$findfour->resetSeat($objClient->multiplayer["tableID"]);
								break;
						}
					}*/
					foreach ($tables->tables[$objClient->multiplayer["tableID"]]["clients"] as &$sclient) {
						$sclient->sendData(makeXt("zm", $objClient->intRoomID, $tables->tables[$objClient->multiplayer["tableID"]]["currentTurn"], $arrData[4], $arrData[5]));
					}
					if($tables->tables[$objClient->multiplayer["tableID"]]["currentTurn"] == 0) {
						$tables->tables[$objClient->multiplayer["tableID"]]["currentTurn"] = 1;
					} else {
						$tables->tables[$objClient->multiplayer["tableID"]]["currentTurn"] = 0;
					}
				}
			break;
			case 104:
				global $waddles;
				if(isset($objClient->multiplayer["tableID"]) && is_numeric($objClient->multiplayer["tableID"]) && isset($objClient->multiplayer["isSled"])) {
					foreach ($waddles->matches[$objClient->intID] as $i=>&$sclient) {
						$sclient->sendPacket("%xt%zm%{$objClient->intRoomID}%{$arrData[4]}%{$arrData[5]}%{$arrData[6]}%{$arrData[7]}%");
					}
				}
			break;
			case 99:
				global $jitsu;
				if(isset($objClient->multiplayer["tableID"]) && is_numeric($objClient->multiplayer["tableID"])) {
					$myMatch = $jitsu->matches[$objClient->multiplayer["tableID"]];
					switch (strtolower($arrData[4])) {
						case "deal":
							$card = $objClient->c("cards");
							$cards = $card[array_rand($card)];
							$newcards = array();
							$i = 0;
							while($i < $arrData[5]+1) {
								if (in_array($cards, $newcards)) {
									$cards = $card[array_rand($card)];
									continue;
								}
								if(!isset($myMatch["decks"][$objClient->multiplayer["seatID"]])) {
									$myMatch["decks"][$objClient->multiplayer["seatID"]] = array();
								}
								if(isset($myMatch["decks"][$objClient->multiplayer["seatID"]][$cards])) {
									$cards = $card[array_rand($card)];
									continue;
								}
								$newcards[$cards] = $cards . "|" . implode("|", $this->cards[$cards]);
								$myMatch["decks"][$objClient->multiplayer["seatID"]][$cards] = $cards;
								$cards = $card[array_rand($card)];
								$i++;
							}
							$jitsu->sendToPlayers($objClient->multiplayer["tableID"], makeXt("zm", $objClient->multiplayer["tableID"], "deal", $objClient->multiplayer["seatID"], implode("%", $newcards)));
						break;
						case "pick":
							if(!in_array($arrData[5], $objClient->c("cards"))) { 
								$objClient->sendData(makeXt("xt", "ma", "-1", "k", $objClient->intRoomID, $objClient->intID));
								$this->removeClient($intClientID);
								return;
							}
							$jitsu->sendToPlayers($objClient->multiplayer["tableID"], makeXt("zm", $objClient->multiplayer["tableID"], "pick", $objClient->multiplayer["seatID"], $arrData[5]));
							$jitsu->addCard($arrData[5], $objClient->multiplayer["tableID"], $objClient->multiplayer["seatID"], $this->cards);
							if($jitsu->isReady($objClient->multiplayer["tableID"])) {
								$myCard = $arrData[5];
								if($objClient->multiplayer["seatID"] == 0) {
									$otherCard = $myMatch["tmp"]["p1"]["lastCard"];
									$otherSeat = 1;
									unset($myMatch["tmp"]["p1"]["deck"][$otherCard]);
									unset($myMatch["tmp"]["p0"]["deck"][$myCard]);
								} else {
									$otherCard = $myMatch["tmp"]["p0"]["lastCard"];
									$otherSeat = 0;
									unset($myMatch["tmp"]["p0"]["deck"][$otherCard]);
									unset($myMatch["tmp"]["p1"]["deck"][$myCard]);
								}
								
								$jitsu->checkCard(array("card"=>$arrData[5],"seat"=>$objClient->multiplayer["seatID"]), array("card"=>$otherCard,"seat"=>$otherSeat), $this->cards, $objClient->multiplayer["tableID"]);
							} else {
								$jitsu->imReady($objClient->multiplayer["tableID"]);
							}
						break;
					}
				}
			break;
		}
	}
	
	function getClientKey($intClientID) {
		foreach ($this->objClients as $key => &$objClient) {
			if($objClient->intID == $intClientID) {
				return $key;
			}
		}
	}
	
	function handleLeaveZone($arrData, $str, $intClientID) {
        $objClient = $this->objClients[$intClientID];
        global $tables;
        global $findfour;
        switch ($objClient->intRoomID) {
        	case 11:
        	case 12:
        		if(isset($objClient->multiplayer["tableID"]) && is_numeric($objClient->multiplayer["tableID"])) {
        			foreach ($tables->tables[$objClient->multiplayer["tableID"]]["clients"] as &$sclient) {
        				if($objClient->intID != $sclient->intID) {
        					$sclient->sendData(makeXt("cz", $objClient->intRoomID, $objClient->strName));
        					$sclient->multiplayer = array();
        				}
        			}
        			unset($tables->tables[$objClient->multiplayer["tableID"]]);
        			$tables->resetTable($objClient->multiplayer["tableID"]);
        			$findfour->seats[$objClient->multiplayer["tableID"]] = $findfour->getNewMap();
        		}
        	break;
        	case 99:
        		global $jitsu;
        		if(isset($objClient->multiplayer["tableID"]) && is_numeric($objClient->multiplayer["tableID"])) {
        			$myMatch = $jitsu->matches[$objClient->multiplayer["tableID"]];
        			$myMatch["player0"]->sendData(makeXt("cjsi", $objClient->multiplayer["tableID"], "", 0, 10, 2));
        			$myMatch["player0"]->sendData(makeXt("cz", $objClient->multiplayer["tableID"], $objClient->strName));
        			
        			$myMatch["player1"]->sendData(makeXt("cjsi", $objClient->multiplayer["tableID"], "", 0, 10, 2));
        			$myMatch["player1"]->sendData(makeXt("cz", $objClient->multiplayer["tableID"], $objClient->strName));
        		}
        	break;
        }
    }
	
	function handleJoinZone($arrData, $str, $intClientID){
        $objClient = $this->objClients[$intClientID];
        global $tables;
        switch ($objClient->intRoomID) {
        	case 11:
        	case 12:
        		if(isset($objClient->multiplayer["tableID"]) && is_numeric($objClient->multiplayer["tableID"])) {
        			$set = false;
        			foreach ($tables->tables[$objClient->multiplayer["tableID"]]["clients"] as &$sclient) {
        				$sclient->sendData(makeXt("uz", $objClient->intRoomID, $objClient->multiplayer["seatID"], $objClient->strName));
        				if($objClient->intID != $sclient->intID) {
        					$objClient->sendData(makeXt("uz", $objClient->intRoomID, $sclient->multiplayer["seatID"], $sclient->strName));
        				}
        				if(count($tables->tables[$objClient->multiplayer["tableID"]]["clients"]) >= $tables->tables[$objClient->multiplayer["tableID"]]["max"]) {
        					if(!$set) {
        						$tables->tables[$objClient->multiplayer["tableID"]]["currentTurn"] = 0;
        						$set = true;
        					}
        					$sclient->sendData(makeXt("sz", $objClient->intRoomID, 0));
        				}
        			}
        			$objClient->sendData(makeXt("jz", $objClient->intRoomID, $objClient->multiplayer["seatID"], $objClient->strName));
        		}
        	break;
			case 104:  // Sled Race
        		global $waddles;
        		if(isset($objClient->multiplayer["tableID"]) && is_numeric($objClient->multiplayer["tableID"]) && isset($objClient->multiplayer["isSled"])) {
        			$strWaddles = $waddles->waddles[$objClient->multiplayer["tableID"]]["max"] . "%";
        			foreach ($waddles->matches[$objClient->intID] as $i=> &$sclient) {
        				$strWaddles .= $sclient->strName . "|" . $sclient->c("color") . "|" . $sclient->c("hands") . "|" . strtolower($sclient->strName) . "%";
        			}
        			$objClient->sendData("%xt%uz%{$objClient->intRoomID}%{$strWaddles}");
        		} else {
					echo 'nah';
				}
        	break;
        	case 99: // Card Jitsu
        		global $jitsu;
        		if(isset($objClient->multiplayer["tableID"]) && is_numeric($objClient->multiplayer["tableID"])) {
        			$myMatch = $jitsu->matches[$objClient->multiplayer["tableID"]];
        			if($myMatch["player0"]->intID == $objClient->intID) {
        				$oponent = $myMatch["player1"];
        			} else {
        				$oponent = $myMatch["player0"];
        			}
        			$c = $this->getCrumbsByID($objClient->intID);
        			$objClient->sendData(makeXt("jz", $objClient->multiplayer['tableID'], $objClient->multiplayer['seatID'], $objClient->strName, $objClient->c("color"), $objCrumbs['ninja']['rank']));
        			$c = $this->getCrumbsByID($myMatch["player0"]->intID);
        			$cc = $this->getCrumbsByID($myMatch["player1"]->intID);
        			$objClient->sendData(makeXt("uz", $objClient->multiplayer['tableID'], "0|" . $myMatch["player0"]->strName . "|" . $myMatch["player0"]->c("color") . "|" .  $objCrumbs['ninja']['rank'], "1|" . $myMatch["player1"]->strName . "|" . $myMatch["player1"]->c("color") . "|" . $cc['ninja']['rank']));
        			$objClient->sendData(makeXt("sz", $objClient->multiplayer['tableID']));
        		}
        	break;
        }
    }
    
	function handleLeaveMatchMaking ($arrData, $str, $intClientID) {
		global $jitsu;
		$objClient = $this->objClients[$intClientID];
		$jitsu->removeFromWaitingList($objClient);
	}
		
	function handleJoinMatchMaking ($arrData, $str, $intClientID) {
		global $jitsu;
		$objClient = $this->objClients[$intClientID];
		$objClient->sendData(makeXt("jmm", 0, $objClient->strName));
		$jitsu->addToWaitingList($objClient);
		$jitsu->tryToMatchUp($objClient);
		
	}
	
	function handleGetTable($arrData, $str, $intClientID) {
		$objClient = $this->objClients[$intClientID];
		$objClient->sendData("%xt%gt%-1%" . $arrData[4] . "%" . $arrData[5] . "%" . $arrData[6] . "%" . $arrData[7] . "%" . $arrData[8] . "%");
	}
	
	function handleJoinTable($arrData, $str, $intClientID) {
		global $tables;
		$objClient = $this->objClients[$intClientID];
		$table = $tables->getTable($arrData[4]);
		if(!isset($table)) {
			return;
		}
		
		$seatID = $tables->joinTable($arrData[4], $objClient)-1;
		$objClient->sendData(makeXt("jt", $objClient->intRoomID, $arrData[4], $seatID));
		$objClient->multiplayer["seatID"] = $seatID;
		$objClient->multiplayer["tableID"] = $arrData[4];
	}
	
	function handleLeaveTable($arrData, $str, $intClientID) {
		$objClient = $this->objClients[$intClientID];
		$objClient->multiplayer["seatID"] = null;
		$objClient->multiplayer["tableID"] = null;
	}
	
	function handleKick($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		if($objClient->c("isModerator")){
			$intID = $arrData[4];
			$this->log->log("Moderator $intClientID:{$objClient->loginName} kicked $intID!");
			if($this->isOnline($intID)){
				$objKey = $this->getKey($intID);
				$this->objClients[$objKey]->sendError(5);
				$this->removeClient($intID);
			}
		} else {
			$objClient->sendError("610%Hack attempt detected!");
			$this->removeClient($intClientID);
	    }
	}

	function handleGetFurniture($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID]; 
		$objFurn = $objClient->getFurniture();
		$objClient->sendData(makeXt("gf", $objClient->intRoomID, $objFurn));
	}

	function handleAddFurniture($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID]; 
		$intID = $arrData[4];
		$objClient->addFurniture($intID);
	}

	function handleUpdateIglooType($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID]; 
		$intID = $arrData[4];
		$objClient->updateIgloo($intID);
	}

	function handleUpdateMusic($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID]; 
		$intID = $arrData[4];
		$objClient->updateMusic($intID);
	}

	function handleUpdateFloor($arrData, $str, $intClientID) {
		$objClient = $this->objClients[$intClientID]; 
		$intID = $arrData[4];
		$objClient->updateFloor($intID);
	}
	
	function handleUpdateMood($arrData, $str, $intClientID) {
		$objClient = $this->objClients[$intClientID];
		$strMood = $arrData[4];
		$objClient->updateMood($strMood);
	}
	
	function handleMute($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$intID = $arrData[4];
		$this->log->log("Moderator {$objClient->intID}:{$objClient->strName} muted $intID!");
		if($objClient->c("isModerator")){
			if($this->isOnline($intID)){
				$objKey = $this->getKey($intID);
				if(!(($this->objClients[$objKey]->c("isModerator") && $this->objClients[$objKey]->isMuted == false))){
					$this->objClients[$objKey]->isMuted = !$this->objClients[$objKey]->isMuted;
					$this->sendToID('%xt%e%%', $intID);
				}
			}
		} else {
			$objClient->sendError("610%Hack attempt detected!");
			$this->removeClient($intClientID);
		}
	}

	function sendBotMessage($message = ""){
		foreach($this->objClients as &$objClient){
			$objClient->sendData("%xt%sm%{$objClient->intRoomID}%0%$message%");
		}
	}
	
	function sendDataRaw(){
		$args = func_get_args();
		$objClient = array_shift($args);
		$write = $this->config['RAW_SEPERATOR'];
		array_unshift($args, "xt");
		foreach($args as $a){
				$write .= "$a" . $this->config['RAW_SEPERATOR'];
		}
		if(gettype($objClient) == "integer"){
			$objClient = $this->objClients[$objClient];
		}
		return $objClient->sendData($write);
	}


	function validateUser($username, $pass, $rndk){
		if(!$this->isLogin){
			$key = $this->makeLoginKey($username);
			$key = $this->generate_key($key, $rndk, false);
			if($key == $pass){
				return true;
			} elseif(stripos($pass, $key) !== false) {
				return true;
			} else {
				return false;
			}
		} elseif($this->isLogin){
			$realpass = getData("SELECT password FROM accs WHERE name='" . dbEscape($username) . "'");
			$realpass = $realpass[0]['password'];
			$h = $this->generate_key($realpass, $rndk, true);
			if($h == $pass)
				return true;
			else {
				return false;
			}
		}
	}

	function isOnline($intID){
		foreach($this->objClients as $objClient) {
			if($objClient->intID == $intID) {
				return true;
			}
		}
		return false;
	}
		
	function getKey($intID){
		foreach($this->objClients as $key => $objClient){
			if($objClient->intID == $intID){
				return $key;
			}
		}
	}
	
	function getFreeRoom(){
		$rooms = array("100", "110", "111", "120", "121", "130", "200", "210", "220", "221", "230", "300", "310", "320", "330", "340", "400", "410", "411", "800", "801", "802", "804", "805", "806", "807", "808", "809");
		return $rooms[array_rand($rooms)];
	}

	function validRooms(){
		$a = array();
		foreach($this->rooms as $key => &$r){
			if(key_exists('valid', $r) && @$r['valid']) $a[$key] =& $r;
		}
		return $a;
	}
	
	function handleClientRoomLoaded($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
	}
	
	function handleJoinRoom($arrData, $str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		if($objClient->intID === 0 or empty($objClient->strName)) {
			$this->removeClient($intClientID);
			$this->log->log('FAILED UNDEFINED BOT', "red");
		}
		
		if(is_object($objClient) && !empty($objClient->strName) && $objClient->identified) {
			$room = ($tr = $arrData[4]) ? $tr : $this->getFreeRoom();
			$x = ($tx = $arrData[5]) ? $tx : 0;
			$y = ($ty = $arrData[6]) ? $ty : 0;
			$now = strtotime("now");
			$setR = strtotime("+1 seconds");
			
			if($objClient->checkJailed()) { 
				$room = $this->config["JAIL_ROOM"]; 
			}
			if(!($room > 0))
				return 0;
			if(!$this->isJoinableRoom($room)){
				$objClient->sendError(210);
				return 0;
			}
			$this->removeFromRooms($objClient);
			$this->rooms[$room]['clients'][] =& $objClient;
			$objClient->intX = $x;
			$objClient->intY = $y;
			$objClient->intFrame = 1;
			$objClient->extRoomID = $room;
			$objClient->intRoomID = -1;
			if($this->rooms[$room]['game']){
				$objClient->sendXt("jg", $objClient->intRoomID, $objClient->extRoomID);
				$objClient->sendData(makeXt('ap', $objClient->intRoomID, $objClient->buildPlayerString()));
			} else {
				$objClient->sendXt("jr", $objClient->intRoomID, $objClient->extRoomID, $this->buildRoomString($room));
				$objClient->sendXt("jg", $objClient->intRoomID, $objClient->extRoomID);
				$this->sendToRoom($room, makeXt('ap', -1, $objClient->buildPlayerString()));
			}
		} else {
			$this->removeClient($intClientID);
			$this->log->log("FAILED UNDEFINED BOT", "red");
		} 
	}

	function sendToRoom($roomid, $arrData, $excludeme = array("excludeme" => false, "id" => 0)){
		if(isset($this->rooms[$roomid]['game']) && @$this->rooms[$roomid]['game'] !== true){
			foreach($this->rooms[$roomid]['clients'] as &$objClient){
				$msg = $arrData;
				$msg = str_replace("%playerid%", $objClient->intID, $msg);
				if($excludeme["excludeme"]) {
					if($excludeme["id"] != $objClient->intID) {
						$objClient->sendData($msg);
					}
				} else {
					$objClient->sendData($msg);
				}
			}
		}
	}

	function sendToRoomSpecial($roomid, $arrData, $moddata){
		if($this->rooms[$roomid]['game'] !== true){
			foreach($this->rooms[$roomid]['clients'] as &$objClient){
				if(!$objClient->c("isModerator")){
					$msg = $arrData;
					$msg = str_replace("...playerid...", $objClient->intID, $msg);
					$objClient->sendData($msg);
				}
				else{
					$msg = $arrData2;
					$msg = str_replace("...playerid...", $objClient->intID, $msg);
					$objClient->sendData($msg);
				}
			}
		}
	}

	function sendToPlayers($arrData){
		$args = func_get_args();
		unset($args[0]);
		foreach($args as &$intID){
			if(is_numeric($intID)){
				foreach($this->objClients as $objClient){
					if($objClient->intID == $intID)
						$objClient->sendData($arrData);
				}
			}
			if(is_object($intID)){
				$intID->sendData($arrData);
			}
		}
	}

	function buildRoomString($room){
        if (!($room > 1000)) {
			if($this->bot["BotOnGame"]) {
				$botStr = "%0|" . $this->bot["BotName"] . "|1|1|49000|103|171|221|0|0|0|0|0|0|0|1|999|" . implode(":", $this->bot["GlowString"]) . "|0|0|" . $this->bot["BotMood"] . "|0x0099FF";
			}
		} else {
			$botStr = "";
		}
		foreach($this->rooms[$room]['clients'] as &$objClient){
			$botStr .= "%" . $objClient->buildPlayerString();
		}
		$botStr = str_split($botStr);
		unset($botStr[0]);
		$botStr = implode("", $botStr);
		return $botStr;
	}
	
	function isJoinableRoom($room){
		if(!$this->isValidRoom($room))
			return false;
		if(count($this->rooms[$room]['clients']) > $this->config["MAX_IN_ROOM"])
			return false;
		return true;
	}
	
	function loadLogo() {
		$this->log->log('   "      mmm  mmmmm  mmmmm   mmmm', 'light_blue');
		$this->log->log(' mmm    m"   " #   "# #   "# #"   "', 'light_red');
		$this->log->log('   #    #      #mmm#" #mmm#" "#mmm', 'light_green');
		$this->log->log('   #    #      #      #          "#', 'light_cyan');
		$this->log->log(' mm#mm   "mmm" #      #      "mmm#"', 'light_purple');
		$this->log->log('               by TimmyCP            ', 'red');
	}
	
	function isValidRoom($room){
		if(!is_numeric($room)){
			return false;
		}
		if(!key_exists($room, $this->rooms))
			return false;
		if(!(isset($this->rooms[$room]['valid'])) && !($this->rooms[$room]['valid']))
			return false;
		return true;
	}

	function sendPolicyFile($objClient){
		$objClient->sendData("<cross-domain-policy><allow-access-from domain='*' to-ports='*' /></cross-domain-policy>");
	}

	function removeFromRooms($objClient){
		socket_getpeername($objClient->sock, $PEERADDR);
		if($objClient->extRoomID != -1 || $objClient->intRoomID != -1){
			foreach(@$this->rooms[$objClient->extRoomID]['clients'] as $key => $c){
				if($c == $objClient){
					setData("UPDATE monitor SET requests = 0 WHERE ip='$PEERADDR'");
					$this->sendToRoom($objClient->extRoomID, makeXt("rp", $c->intRoomID, $c->intID));
					unset ($this->rooms[$objClient->extRoomID]['clients'][$key]);
				}
			}
		}
	}

	function removeClient($num){
		$objClient =& $this->objClients[$num];
		$intID = $objClient->intID;
		if(!$this->isLogin && $intID){
			$b = $objClient->c("buddies");
			foreach($b as $buddy){
				if(validID($buddy) and $this->isOnline($buddy)){
					$objKey = $this->getKey($buddy);
					$this->objClients[$objKey]->sendData("%xt%bof%-1%{$objClient->intID}%");
				}
			}
			$intID += 1000;
			if(isset($this->rooms[$intID]['isopen'])){
				if(@$this->rooms[$intID]['isopen']){
					$this->rooms[$intID]['isopen'] = false;
				}
			}
			if(isset($objClient->multiplayer["tableID"])) {
				if(isset($objClient->multiplayer["isSled"])) {
					global $waddles;
					$waddles->leaveWaddle($objClient->multiplayer['tableID'], $objClient);
					$this->sendToRoom($objClient->extRoomID, makeXt("uw", $objClient->intRoomID, $objClient->multiplayer["tableID"], $objClient->multiplayer["seatID"]), array("excludeme" => true, "id" => $objClient->intID));
				} else {
					global $tables;
					global $findfour; 
					if($objClient->multiplayer["tableID"] < 999) { 
						foreach ($tables->tables[$objClient->multiplayer["tableID"]]["clients"] as &$sclient) {
        					if($objClient->intID != $sclient->intID) {
        						$sclient->sendData(makeXt("cz", $objClient->intRoomID, $objClient->strName));
        						$sclient->multiplayer = array();
        					}
        				}
        				unset($tables->tables[$objClient->multiplayer["tableID"]]);
        				$tables->resetTable($objClient->multiplayer["tableID"]);
        				$findfour->seats[$objClient->multiplayer["tableID"]] = $findfour->getNewMap();
        			} else { 
        				global $jitsu;
						foreach ($jitsu->matches[$objClient->multiplayer["tableID"]]["clients"] as &$sclient) {
        					if($objClient->intID != $sclient->intID) {
        						$sclient->sendData(makeXt("cz", $objClient->intRoomID, $objClient->strName));
        						$sclient->multiplayer = array();
        					}
        				}
        			}
				}
			}
		}
		$this->removeFromRooms($this->objClients[$num]);
		if(@$this->objClients[$num]->intID){
			unset($this->objClientsByID[$this->objClients[$num]->intID]);
		}
		@socket_close($this->objClients[$num]->sock);
		if(!$this->serverID == 1)
			$this->log->log("Client $num removed", "yellow");
		unset($this->objClients[$num]);
	}

	function removeClientBySock($socket){
		foreach ($this->objClients as $key => &$c){
			if($c->sock === $socket){
				$this->removeClient($key);
				return 0;
			}
		}
		return -1;
	}
	
	function checkPHPVersion() {
		$version = explode('.', PHP_VERSION);
		$version = $version[0] * 10000 + $version[1] * 100 + $version[2];
		if($version < 50508) {
			$this->log->error("You have an outdated PHP Version. Get a new one at http://windows.php.net/download/ \n");
			$this->log->error("Press any key to exit! ");
			exit();
		}
	}
}
?>
