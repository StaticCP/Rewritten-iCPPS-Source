<?php
trait Crypto {
	function encryptPassword($password, $nMD5 = true){
		$md5 = $nMD5 ? md5($password) : $password;
		return substr($md5, 16, 16) . substr($md5, 0, 16);
    }
	
	public function makeLoginKey($username){
		$username = strtoupper($username);
		$len = strlen($username);
		for($i = 0; $i < $len; $i++){
			if($i %2 == 0){
				$username{$i} = strtolower($username{$i});
			}
		}
		if(!$this->hasSLKey($username)){
			$this->generateSLKey($username);
		}
		$hash = $this->getSLKey($username);
		$key = substr(strtolower($hash) . strtoupper($hash), 15 - $len, 40 - $len);
		return strtolower($key);
	}
	
	function generate_key($Password, $randKey, $isLogin){
		if ($isLogin){
			$Key = strToUpper($this->encryptPassword($Password, false));
			$Key = $Key . $randKey;
			$Key = $Key . "Y(02.>'H}t\":E1";
			$Key = $this->encryptPassword($Key);
			return($Key);
		} else {
			return strtolower($this->encryptPassword($Password.$randKey).$Password);
		}
	}
	
	function generateSLKey($username){
	    $salt = "52@##***8u9598u4231u96uq723y8e2@&#*@!@@)$@(#)35823uqtrfjewa8ud8@*#ufsa889@$@$(@*#()!(_fd345a@*#yf76&*543rewaty8PEN*&&&S9sacoold@#&*@#ude170<3&@*&#chailo54lnotreallE#@#&EHUD&@IEDHI23DHJOQWI76823y!";
		$gPass = getValue("".dbEscape($username)."", "password");
        $random_key = md5(rand(8746, 9999999) . $gPass);
        $complete = sha1($random_key . $salt);
        setData("UPDATE accs SET slkey = '". $complete ."' WHERE name = '". dbEscape($username) ."'");
    }
   
	function hasSLKey($username){
        $arrData = getValue("".dbEscape($username)."", "slkey");
        if($arrData == "" || $arrData == " "){
			return false;
		} else {
			return true;
        }
	}
	
}
?>
