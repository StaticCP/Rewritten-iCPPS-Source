<?php
trait Utils {
	public function downloadJSON($type) {
		switch($type) {
			case 'items':
				$url = "http://media1.clubpenguin.com/play/en/web_service/game_configs/paper_items.json";
				$strName = "paper_items";
			break;
			case 'furniture':
				$url = "http://media1.clubpenguin.com/play/en/web_service/game_configs/furniture_items.json";
				$strName = "furniture_items";
			break;
			case 'igloos':
				$url = "http://media1.clubpenguin.com/play/en/web_service/game_configs/igloos.json";
				$strName = "igloos";
			break;
			case 'flooring':
				$url = "http://cdn.clubpenguin.com/play/en/web_service/game_configs/igloo_floors.json";
				$strName = "igloo_floors";
			break;
			case 'rooms':
				$url = "http://media1.clubpenguin.com/play/en/web_service/game_configs/rooms.json";
				$strName = "rooms";
			break;
			case 'games':
				$url = 'http://media1.clubpenguin.com/play/en/web_service/game_configs/games.json';
				$strName = 'games';
		}
		$url  = "$url";
		$path = "../Add-Ons/JSON/$strName.json";
		$fp = fopen($path, 'w');
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_FILE, $fp);
		$arrData = curl_exec($ch);
		curl_close($ch);
		fclose($fp);
	}
	function getContents($strURL) {
		$resCurl = curl_init();
		curl_setopt($resCurl, CURLOPT_URL, $strURL);
		curl_setopt($resCurl, CURLOPT_RETURNTRANSFER, 1);
		$arrData = curl_exec($resCurl);
		curl_close($resCurl);
		return $arrData;
	}
}
?>
