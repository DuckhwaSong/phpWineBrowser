<?
class commonlib {
	function decrypt($srt, $encType,$key,$iv=""){
		$encType = strtoupper($encType);
		switch($encType){
			case "AES-128-ECB":
				$commonCrypt = new commonCrypt();
				$commonCrypt->encMode = strtolower("ECB");
				return $commonCrypt->decrypt(base64_decode($srt),$key,$iv);
				break;
			case "AES-128-CBC":
				$commonCrypt = new commonCrypt();
				return $commonCrypt->decrypt(base64_decode($srt),$key,$iv);
				break;
			case "SEED-CBC":
				break;
		}
	}
	function encrypt($srt, $encType,$key,$iv=""){
		$encType = strtoupper($encType);
		switch($encType){
			case "AES-128-ECB":
				$commonCrypt = new commonCrypt();
				$commonCrypt->encMode = strtolower("ECB");
				return base64_encode($commonCrypt->encrypt($srt,$key,$iv));
				break;
			case "AES-128-CBC":
				$commonCrypt = new commonCrypt();
				return base64_encode($commonCrypt->encrypt($srt,$key,$iv));
				break;
			case "SEED-CBC":
				break;
		}
	}
}
class commonCrypt
{
	var $algorithm = "";

	function __construct($algorithm=""){
		#$this->algorithm = "rijndael-128";
		$this->algorithm = "AES-128-CBC";
		$this->encMode = "cbc";
		$this->binEnc = "base64";
		if($algorithm) $this->algorithm = $algorithm;
		
	}
	function PKCS5Pad($text, $blocksize = 16)
	{
		$pad = $blocksize - (strlen($text) % $blocksize);
		return $text . str_repeat(chr($pad), $pad);
	}
	function PKCS5Unpad($text)
	{
		$pad = ord($text{strlen($text)-1});
		if ($pad > strlen($text)) return $text;
		if (!strspn($text, chr($pad), strlen($text) - $pad)) return $text;
		return substr($text, 0, -1 * $pad);
	}
	function encrypt($str,$key,$iv='')
	{
		#echo "binEnc=><xmp>{$this->binEnc}</xmp>";
		if($this->binEnc == "base64") return @openssl_encrypt($str, $this->algorithm, $key, 0, $iv);
		else if($this->binEnc == "hex") return bin2hex(base64_decode(@openssl_encrypt($str, $this->algorithm, $key, 0, $iv)));
		
		$ciphertext = @mcrypt_encrypt($this->algorithm, $key, $this->PKCS5Pad($str), $this->encMode, $iv);
		return $ciphertext;

		/*$td = mcrypt_module_open($this->algorithm, '', $this->encMode, '');
		@mcrypt_generic_init($td, $key, $iv);
		$encrypted = @mcrypt_generic($td, $this->PKCS5Pad(($str)));
		
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		return ($encrypted);*/
	}
 
	function decrypt($str,$key,$iv='')
	{
		#echo "decrypt=><xmp>$ciphertext</xmp>";
		return openssl_decrypt($str, $this->algorithm, $key, 0, $iv);

		$decrypted = mcrypt_decrypt($this->algorithm, $key, $str , $this->encMode, $iv);
		return ($this->PKCS5Unpad($decrypted));

		/*$td = mcrypt_module_open($this->algorithm, '', $this->encMode, '');
		@mcrypt_generic_init($td, $key, $iv);
		$decrypted = @mdecrypt_generic($td, $code);

		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		return ($this->PKCS5Unpad($decrypted));*/
	}
	
	function AESCBCPKCS5($source_data, $key, $iv, $mode = "enc", $base64 = "yes")
	{
		if($mode=="dec")
		{
			if($base64=="yes") return $this->decrypt($iv,$key,base64_decode($source_data));
			else return $this->decrypt($iv,$key,$source_data);
		}
		else
		{
			if($base64=="yes") return base64_encode($this->encrypt($iv,$key,$source_data));
			else $this->encrypt($iv,$key,$source_data);
		}
	}
}
?>