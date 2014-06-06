<?php
class Logger{
	private static $instance;
	private $file;
	
	public static function getInstance(){
		if(!isset(self::$instance))
			self::$instance = new Logger();
		return self::$instance;
	}

	function __construct($file = false){
		if(!isset(self::$instance)){
			self::$instance = $this;
		}
		$this->file = $file;
		$this->foreground_colors['black'] = '0;30';
		$this->foreground_colors['dark_gray'] = '1;30';
		$this->foreground_colors['blue'] = '0;34';
		$this->foreground_colors['light_blue'] = '1;34';
		$this->foreground_colors['green'] = '0;32';
		$this->foreground_colors['light_green'] = '1;32';
		$this->foreground_colors['cyan'] = '0;36';
		$this->foreground_colors['light_cyan'] = '1;36';
		$this->foreground_colors['red'] = '0;31';
		$this->foreground_colors['light_red'] = '1;31';
		$this->foreground_colors['purple'] = '0;35';
		$this->foreground_colors['light_purple'] = '1;35';
		$this->foreground_colors['brown'] = '0;33';
		$this->foreground_colors['yellow'] = '1;33';
		$this->foreground_colors['light_gray'] = '0;37';
		$this->foreground_colors['white'] = '1;37';

		$this->background_colors['black'] = '40';
		$this->background_colors['red'] = '41';
		$this->background_colors['green'] = '42';
		$this->background_colors['yellow'] = '43';
		$this->background_colors['blue'] = '44';
		$this->background_colors['magenta'] = '45';
		$this->background_colors['cyan'] = '46';
		$this->background_colors['light_gray'] = '47';
	}

	function error($message){
		if($this->file){
			$this->logToFile($message);
		}
		echo $message;
		$this->logToIRC($message);
	}

	function logToIRC($message){
		$message = $message . "\n";
	}
	
	public function getColoredString($strTexting, $foreground_color = null, $background_color = null) {
		$colored_string = "";
		if (isset($this->foreground_colors[$foreground_color])) {
			$colored_string .= "\033[" . $this->foreground_colors[$foreground_color] . "m";
		}
		if (isset($this->background_colors[$background_color])) {
			$colored_string .= "\033[" . $this->background_colors[$background_color] . "m";
		}
		$colored_string .=  $strTexting . "\033[0m";
		return $colored_string;
	}
	
	public function getForegroundColors() {
		return array_keys($this->foreground_colors);
	}
	
	public function randColor() {
		$foregroundColor = $this->getForegroundColors();
		return $foregroundColor[array_rand($foregroundColor)];
	}
	
	public function getBackgroundColors() {
		return array_keys($this->background_colors);
	}
	
	function log($message, $color = null){
		if($this->file) {
			$this->logToFile($message);
		} else {
			if($color == null) {
				echo $message.  "\n";
			} else {
				echo $this->getColoredString($message, $color, "black") . "\n";
			}
		}
	}

	function screen($message){
		echo "".$message."\n";
	}

	function logToFile($message){
		$fh = fopen($this->strName, "a");
		fwrite($fh, "".$message."\n") or die("Could not write to log file!");
		fclose($fh);
	}

	function date(){
		return date('H:i:s');
	}
	
}

class console {
	var	$mode = 1;
	function setmode($mode){
		$this->mode = $mode;
	}

	function colourize($strTextText, $colour = null){
		if($this->mode == 1 || $this->mode == 2){
			$strTextarr = str_split($strText);
			foreach($strTextarr as $char){
				global $pclconfig;
				if($pclconfig["Graphics"]["effects"] == 1){
					if(@$colour == null){
						if($char != " ") {
							$char = "\033[07;01;3" . rand(1, 6) . "m" . $char;
						}else{
							$char = "\033[00;4" . "9" . "m" . $char;
						}
					} else {
						if($char != " ")
							$char = "\033[42;3" . $colour . "m" . $char;
						else
							$char = "\033[00m" . $char . "\033[00m";
					}
					if($this->mode == 2){
						echo $char;
						pusleep($this->wait);
					}
				} else{
					echo $char;
					pusleep($this->wait);
				}
			}
			$strText = implode($strTextarr, "");
			if($this->mode != 2){
				if($pclconfig["Graphics"]["effects"] == 1)
				$strText .= "\033[00;39;49m";
				return $strText;
			}
			if($pclconfig["Graphics"]["effects"] == 1)
			echo "\033[00;39;49m";
		} else {
			$strTextarr = str_split($strText);
			foreach($strTextarr as &$char){
				$char = "\033[07;01;3" . rand(1, 6) . "m" . $char;
		}
			$strText = implode($strTextarr, "");
			$strText .= "\033[00;39;49m";
			echo $strText;
		}
	}

	function stribet($inputstr, $delimiterLeft, $delimiterRight) { 
		$posLeft = stripos($inputstr, $delimiterLeft) + strlen($delimiterLeft);
		$posRight = stripos($inputstr, $delimiterRight, $posLeft);
		return substr($inputstr, $posLeft, $posRight - $posLeft);
	}

	function movecursor($line, $column){
		echo "\033[{$line};{$column}H";
	}

	function erasescreen(){
		echo "\033[2J";
	}
	function hidecursor(){
		echo "\033[?25l";
	}
	function showcursor(){
		echo "\033[?25h";
	}
	function saveposition(){
		echo "\033[s";
	}
	function restoreposition(){
		echo "\033[u";
	}
}
?>
