<?php
class Quest {
	public $rank;
	public $quest;
	public $pluginOnGame = true;
	
    public function __construct($objClient) {
		$this->client = $objClient;
		$this->rank = $objClient->c("qrank");
		$this->quest = $objClient->c("qtype");
    }
	
	function initQuest($arrData, $objClient) {
		switch($arrData[2]) {
			case "m#sm":
				$this->handleMessage($arrData);
			break;
			case "j#jr":
			case "j#jp":
			case "j#grs":
				$this->start($arrData);
			break;
			case "u#sp":
				$this->start($arrData);
			break;					
		}
	}
	
	function handleMessage($arrData) {
		switch($arrData[5]) {
			case "!qriddle":
				$this->getHint($this->quest, $this->rank);
			break;
		}
	}
	function getHint($quest, $rank) {
		switch($quest) {
			case 0:
			case 1://Level
				switch($rank) {
					case 0: //Rank
						return $this->client->sendData(makeXt("sm", $this->client->intRoomID, 0, "Find the Penguin Run Sign!"));
					break;
					case 1:
						return $this->client->sendData(makeXt("sm", $this->client->intRoomID, 0, "Find the Puffle Snowman!"));
					break;
					case 2:
						return $this->client->sendData(makeXt("sm", $this->client->intRoomID, 0, "Find the 'Make Us Laugh' section!"));
					break;
				}
			break;
			case 2:
				switch($rank) {
					case 0:
						return;
					break;
				}
			break;
		}
	}
		
	function start($arrData) {
		switch($this->quest) {
			case 0:
			case null:
				switch($this->rank) {
					case 0:
						if($this->client->extRoomID == 230) {
							if (($arrData[4] >= 250) && !($arrData[4] > 290)) {
								if (($arrData[5] >= 280) && !($arrData[5] > 320)) {
									$this->client->sendData(makeXt("sm", $this->client->intRoomID, 0, "You found the Penguin Run Sign!"));
									$this->client->setQrank("1");
									sleep(1);
									$this->getHint(1, 1);
								}
							}
						}
					break;
					case 1:
						if($this->client->extRoomID == 801) {
							if (($arrData[4] >= 640) && !($arrData[4] > 710)) {
								if (($arrData[5] >= 360) && !($arrData[5] > 405)) {
									$this->client->sendData(makeXt("sm", $this->client->intRoomID, 0, "You found the Puffle Snowman!"));
									$this->client->setQrank("2");
									sleep(1);
									$this->getHint(1, 2);
								}
							}
						}
					break;
					case 2:
						if($this->client->extRoomID == 110) {
							if (($arrData[4] >= 190) && !($arrData[4] > 210)) {
								if (($arrData[5] >= 130) && !($arrData[5] > 150)) {
									$this->client->sendData(makeXt("sm", $this->client->intRoomID, 0, "Great Job on Finding 'Make Us Laugh'!"));
									sleep(2);
									$this->client->setQrank("3");
									$this->getHint(1, 3);
								}
							}
						}
					break;
				}
			case 2:
				switch($this->rank) {
					case 0:
					case 1:
						if($this->client->extRoomID == 230) {			
							if (($arrData[4] >= 250) && !($arrData[4] > 290)) {
								if (($arrData[5] >= 280) && !($arrData[5] > 320)) {
									$this->client->sendData(makeXt("sm", $this->client->intRoomID, 0, "Congratulations! You have found the Penguin Run Sign. Please Wait while we add more levels"));
								}
							}
						}
					break;
				}
			break;
		}
	}
}
