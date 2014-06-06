<?php
class ClientBase{
	static $num = 0;
	static $count = 0;
	public $ipaddress;
	public $properties = array();
	public $p = null;
	public $sock;
	public $parent;
	public $uniqueid = 0;
	public $intX = 0;
	public $intY = 0;
	public $intClientID = 0;
	public $strName = "";

	function __construct($resSock, $objServer, $intClientID){
		self::$num++;
		self::$count++;
		$this->uniqueid = self::$count;
		$this->parent =& $objServer;
		$this->sock = $resSock;
		$this->p =& $this->properties;
		$this->p['rndK'] = $this->makeRndK();
		$this->clientID = $intClientID;
	}
		
	function sendData($arrData){
		$arrData .= chr(0);
		$strLen = strlen($arrData);
		if($this->parent->config["DEBUG"])
			$this->parent->log->log("[SENT]: $arrData", "cyan");
		$len = @socket_send($this->sock, $arrData, $strLen, null);
		return $len ? true : false;
	}

	function makeRndK(){
		$c = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxwz0123456789?~";
		$l = rand(6,14);
		$s = "";
		for(;$l > 0;$l--)
			$s .= $c{rand(0,strlen($c) - 1)};
		return $s;
	}

	function buildClientString($type = "raw", $s = "%"){
		if($type == "xml"){
			return $this->buildXmlPlayer();
		}
		return $this->buildRawPlayer($s);
	}

	function getSortedProperties(){	
	}

	function buildRawPlayer($s){
		return implode($this->getSortedProperties(), $s);
	}

	function getSocket(){
		return $this->sock;
	}
}
?>
