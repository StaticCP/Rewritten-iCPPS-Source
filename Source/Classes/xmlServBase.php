<?php
class xmlServBase {	
	public $strIP;
	public $intPort;
	public $mainSocket = NULL;
	public $log;
	public $success = false;
	public $objClients;
	public $config;
	public $arrXMLHandlers = array(
			'sys' => array(
				'verChk' => "handleVerChk",
				'login' => "handleLogin",
				'rndK'	=> "handleRndK"
			)
		);
	public $arrXMLTypeHandlers = array();
	public $arrSendHandlers = array();
	public $arrHandlers = array();
	public $connectedIPs = array();
	
	function __construct($config, $isLogin = false){
		define("ATTRIB", "@attributes");
		$this->arrXMLTypeHandlers = array_merge((Array)$this->arrXMLHandlers, (Array)$this->arrXMLTypeHandlers);
		$this->strIP = $config["ADDRESS"];
		$this->intPort = $config["PORT"];
		$this->log = new Logger;
		$this->objClients = array();
		$this->config = $config;
		$this->construct();
		$this->success = $this->initialise();
		$this->isLogin = $isLogin;
	}
	
	public function initialise(){
		$this->mainSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("Could not create socket");
		socket_set_option($this->mainSocket, SOL_SOCKET, SO_REUSEADDR, 1);
		socket_set_nonblock($this->mainSocket);
		socket_bind($this->mainSocket, $this->config["ADDRESS"], $this->config["PORT"]) or die("Could not bind to port");
		socket_listen($this->mainSocket, 5);
		return true;
	}
		
	function run($objClients = NULL) {
		if($objClients == NULL){
			$objClients = $this->objClients;
		}
		$this->objClients = $objClients;
		unset($objClients);
		updateStatus($this->serverID, Client::$num);
		$intStatus = 0;
		while($intStatus == 0) {
			usleep(0);
			$intStatus = $this->listenToClients();
		}
		if($this->config["DEBUG"])
			echo "Fatal error: $intStatus\n";
		$this->log->error("listenToClients() failed, error: $intStatus");
		return $intStatus;
	}

	function sendData($arrData, $sock, $flags = MSG_EOR){
		$arrData .= chr(0);
		if($this->config["DEBUG"])
			$this->log->log("[SENT]: $arrData", "cyan");
		$len = socket_send($sock, $arrData, strlen($arrData), $flags);
		return $len ? true : false;
	}
	
	public function listenToClients() {
		$arrSockets = $this->getSockets();
		$arrWrite = null;
		$arrExcept = null;
		$intSockets = socket_select($arrSockets, $arrWrite, $arrExcept, 0);
		if ($intSockets < 0) {
			return 0;
		}
		if(in_array($this->mainSocket, $arrSockets)){
			$this->addClient();
			unset($arrSockets[0]);
		}
		foreach($arrSockets as $resSock){
			@socket_recv($resSock, $strData, 8192, 0);
			if($strData === null){
				$this->removeClientBySock($resSock);
				continue;
			}
			$intClientID = $this->getClientIdFromSock($resSock);
			$arrData = explode(chr(0), $strData);
			foreach($arrData as $strData) {
				$this->handleRawData($strData, $intClientID);
			}
		}
		return 0;
	}

	function handleRawData($arrData, $intClientID) {
		$strSubstring = substr($arrData, 0, 1);
		if ($strSubstring === '<'){
			try {
				$this->handleLoginData($arrData, $intClientID);
			} catch (XmlException $xmlExc) {
				$this->log->log($xmlExc->getMessage(), "red");
			}
		} elseif ($strSubstring === "%") {
			try {
				$this->handleXtData($arrData, $intClientID);
			} catch (XtException $XtExc) {
				$this->log->log($XtExc->getMessage(), "red");  
			}
		}
	}
	
	function handleLoginData($strData, $intClientID) {
		if (is_string($strData) && !empty($strData)) {
			if($this->config["DEBUG"])
				$this->log->log("[RECEIVED]: $strData", "light_cyan");
			$this->parseData($strData, $intClientID);
		} else {
			$this->removeClient($intClientID);
			throw new XmlException("Failed Undefined Data");
		}
	}
	
	function handleXtData($arrData, $intClientID) {
		$objClient = $this->objClients[$intClientID];
		if (is_object($objClient)  && !empty($objClient->strName)) {
			if($this->config["DEBUG"])
				$this->log->log("[RECEIVED]: $arrData", "light_cyan");
			$this->parseRawData($arrData, $intClientID);
		} else {
			$this->removeClient($intClientID);
			throw new XtException("Failed Undefined Bot");
		}
	}
	
	function parseData($str, $intClientID){
		$objClient = $this->objClients[$intClientID];
		if(is_object($objClient)){
			if(stripos($str, "<policy-file-request/>") !== false){ 
				$this->sendPolicyFile($this->objClients[$intClientID]); 
			}
			if($this->config["RAW_STR"] && $str[0] == $this->config["RAW_SEPERATOR"]) {
				return $this->parseRawData($str, $intClientID);
			}
			$xmlar = @simplexml_load_string($str, 'SimpleXMLElement', LIBXML_NOCDATA);
			if(!$xmlar && stripos($str, "<policy-file-request/>")){
				$this->log->error("Malformed packet $str sent by $intClientID");
				return;
			}
			$arrData = @json_decode(@json_encode((array) $xmlar),1);
			
			if (!is_string($arrData) && empty($arrData)) { 
				return -1;
			}
			$t = $arrData[ATTRIB]["t"];
			$called = false;
			if(isset($this->arrXMLTypeHandlers[$t])){
				$a = $arrData["body"][ATTRIB]["action"];
				if(isset($this->arrXMLTypeHandlers[$t][$a])){
					$f = $this->arrXMLTypeHandlers[$t][$a];
					if(method_exists($this, $f)){
						$this->$f($arrData, $str, $intClientID);
						$called = true;
					}
				}
			}
			if(!$called) 
				$this->unknownHandler($arrData, $str, $intClientID);
			return 0;
		} else {
			$this->removeClient($intClientID);
			$this->log->log("FAILED UNDEFINED BOT", "red");
		}
	}
	
	function unknownHandler($d, $arrData, $intClientID) {
		socket_getpeername($this->objClients[$i]->sock, $strIP);
		if (strpos($arrData, 'X-Flash') !== false) {
			$this->log->log("X-Flash exploit detected; removing client. - $strIP");
			IPBan($strIP, strtotime("+6 minutes"));
			$this->removeClient($intClientID);
			return;
		}
		if (strpos($arrData, 'HTTP') !== false) {
			$this->log->log("HTTP request [exploit?] detected; removing client. - $strIP");
			$this->removeClient($intClientID);
			return;
		}
		if (strpos($arrData, 'X-') !== false) {
			$this->log->log("X-* header (HTTP) [exploit?] detected; removing client. - $strIP");
			$this->removeClient($intClientID);
			return;
		}
		if (strpos($arrData, 'Flash:') !== false) {
			$this->log->log("X-Flash exploit detected; removing client. - $strIP");
			IPBan($strIP, strtotime("+6 minutes"));
			$this->removeClient($intClientID);
			return;
		}
		if (strpos($arrData, 'rndK.rndK') !== false) {
			$this->log->log("rndK.rndK* exploit detected; removing client. - $strIP");
			IPBan($strIP, strtotime("+6 minutes"));
			$this->removeClient($intClientID);
			return;
		}
		if (strpos($arrData, '<msg t=\'sys\'><body action=\'rndK\' r=\'-1\'></body></msg>') !== false) {
			$this->log->log("False rndK request exploit detected; removing client. - $strIP");
			IPBan($strIP, strtotime("+6 minutes"));
			$this->removeClient($intClientID);
			return;
		}
		$this->log->log("Received unknown message from , message: $arrData, IP: $strIP");
    }
	
	function parseRawData($arrData, $intClientID){
		$objClient = $this->objClients[$intClientID];
		$this->objClients[$intClientID]->time = time();
		if(is_object($objClient) && !empty($objClient->strName) && $objClient->identified) {
			$d = explode($this->config["RAW_SEPERATOR"], $arrData);
			if($d[0] === ""){
				unset($d[count($d) - 1]);
				unset($d[0]);
				$d = array_values($d);
			}
			$zone = $d[0]; 
			$called = false;
			if($d[1] == $this->config["SEND_HANDLER"] || !@is_array(@$this->arrHandlers[$d[1]])){
				if($this->config["USE_SEND_HANDLER"]){
					if($d[1] == $this->config["SEND_HANDLER"]){
						if(isset($this->arrSendHandlers[$d[2]]) && @method_exists($this, $f = @$this->arrSendHandlers[$d[2]])){
							$this->$f($d, $arrData, $intClientID);
							$called = true;
							$this->loadPlugins($d, $objClient, $intClientID);
						}
					} else {
						if(@method_exists($this, @$f = @$this->arrHandlers[$d[1]])){
							$this->$f($d, $arrData, $intClientID);
							$called = true;
						}
					}
				}
			} else {
				if(!is_array(@$this->arrHandlers[$d[1]]) && @method_exists($this, $f = @$this->arrHandlers[$d[1]])){
					if($this->config["ALLOW_OTHER_HANDLERS"]) {
						$this->$f($d, $arrData, $intClientID);
						$called = true;
					}
				}
				elseif(isset($this->arrHandlers[$d[1]][$d[2]]) && @method_exists($this, $f = @$this->arrHandlers[$d[1]][$d[2]])){
					$this->$f($d, $arrData, $intClientID);
					$called = true;
				}
			}
			if(!$called){
				$this->unknownHandler($d, $arrData, $intClientID);
			}
		} else {
			$this->removeClient($intClientID);
			$this->log->log("FAILED UNDEFINED BOT", "red");
		}
	}
	
	function getSockets(){
		socket_set_nonblock($this->mainSocket);
		$arrSockets = array();
		$arrSockets[] = $this->mainSocket;
		foreach($this->objClients as $objClient){
			$arrSockets[] = $objClient->sock;
		}
		return $arrSockets;
	}
	
	
	function addClient(){
		if(!is_resource($this->mainSocket)){
			$this->log->log('Invalid client!');
		}
		$intClients = sizeof($this->objClients);
		if($intClients >= $this->config["MAX_CLIENTS"]){
			$this->cleanUp();
			$this->log->log('Client limit exceeded!', 'yellow');
			return -1;
		} else {
			for ($i = 0; $i < $this->config["MAX_CLIENTS"]; $i++) {
				if (!isset($this->objClients[$i])) {
					$this->objClients[$i] = new Client(socket_accept($this->mainSocket), $this, $i);
					socket_getpeername($this->objClients[$i]->sock, $strIP);
					$this->floodProtection($strIP);
					$this->log->log('Client connected from IP: ' . $strIP, "yellow");
					return 0;
				}
			}
		}
	}
	
	function floodProtection($strIP) {
		array_push($this->connectedIPs, $strIP);
		$intConnections = count(array_keys($this->connectedIPs, $strIP));
		if($intConnections >= 20) {
			$this->removeClientByIP($strIP);
			$this->log->log("Flood attempt detected {$strIP}"  , "red");
			exec("iptables -A INPUT -s " . $strIP . " -j DROP");//WORKS ON LINUX VPSes
		}
	}
	
	function getClientIdFromSock($socket){
		foreach ($this->objClients as $key => &$c){
			if($c->sock === $socket){
				return $key;
			}
		}
		return -1;
	}

	function removeClient($num){
		unset($this->connectedIPs[$this->objClients[$num]['ipaddress']]);
		unset($this->objClients[$num]);
	}
	
	function removeClientByIP($strIP){
		foreach ($this->objClients as $key => &$c){
			if($c->ipaddress === $strIP) {
				unset($this->connectedIPs[$this->objClients[$num]['ipaddress']]);
				unset($this->objClients[$num]);
			}
		}
	}

	function removeClientBySock($socket){
		foreach ($this->objClients as $key => &$c){
			if($c->sock === $socket ) {
				unset($this->objClients[$key]);
				socket_close($socket);
				$this->log->log("Client $key removed");
				return 0;
			}
		}
		return -1;
	}
}
?>
