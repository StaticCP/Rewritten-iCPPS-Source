<?php
$intID = $objClient->intID;
switch($botmsg) {
	case "hi bot":
	case "hello bot":
	case "hey boy":
		$messages = array("Hello, ". $objClient->strName . "!", "Hi There Penguin!");
		$message = $messages[array_rand($messages)];
		$objClient->sendData(makeXt("sm", $objClient->intRoomID, 0, "$message"));
	break;
	
	case 'timmy':
	case 'chicken':
	case 'timmycp':
		$messages = array("Timmy is amazing!", "Bot don't like Timmy, Timmy likes bot :/");
		$message = $messages[array_rand($messages)];
		$objClient->sendData(makeXt("sm", $objClient->intRoomID, 0, "$message"));
	break;
}
?>
