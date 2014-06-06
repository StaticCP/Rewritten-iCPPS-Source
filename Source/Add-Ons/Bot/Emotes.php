<?php
$intID = $objClient->intID;
switch($botemote) {
	case 1:
	case 2:
		$phrases = array("You're really happy, aren't you?", ":) to you too!", "I love smiley faces!", "You're happy? So am I!");
		$message = $phrases[array_rand($phrases)];
		$objClient->sendData(makeXt("sm", $objClient->intRoomID, 0, "$message"));
	break;
}
?>
