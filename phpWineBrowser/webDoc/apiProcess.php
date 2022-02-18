<?
header("Content-Type: text/html; charset=UTF-8");
/*
$config = parse_ini_file("config.ini",true);
foreach($config['ssh'] as $key=>$val) ${$key}=$val;


foreach($config['db'] as $key=>$val) $info[$key]=$val;

$soluVer = "multi";			# 다국어 버전
$sqlFile = "./tmpDonw.sql";	# 임시파일명
if(is_file($sqlFile)) unlink($sqlFile);			# 임시파일 삭제

# 경로
$localSoluDir = $_POST['masterdir'];
if(!is_dir($localSoluDir)) msgback("마스터DIR 경로 정확하지 않습니다.");
if(substr($localSoluDir,-1) != "\\") $localSoluDir .="\\";

#최종패치일
$patchDate = $_POST['patchdate'];
if(empty($patchDate)) msgback("사이트 설치일(최종패치날짜)가 입력되지 않았습니다.");
if(!preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $patchDate)) msgback("사이트 설치일(최종패치날짜)가 정확하지 않습니다.");

#솔루션 타입
$soluType = $_POST['solutype'];
if(empty($soluType)) msgback("솔루션타입이 입력되지 않습니다.");
if(empty($config['Type'][$soluType])) msgback("솔루션타입이 정확하지 않습니다.");
*/
# 라이브러리 호출
include("common.lib.php");
$commonlib = new commonlib();

# 세팅 값 저장
foreach($_POST as $key => $val) $configData .= "$key=\"".$commonlib->encrypt($val, "AES-128-ECB","123456789ABCDEFG")."\"\r\n";
file_put_contents("setting.ini", $configData);

# 로그 저장
$date = date('Y-m-d');
file_put_contents("{$date}.log", date('[Y-m-d H:i:s] =>').print_r($_POST,1)."\n\n",FILE_APPEND);


#스키마 동기화
$schema = $_POST['schema'];

#보조함수
function msgback($msg){
	exit("
	<script>
	alert('$msg');
	history.back();
	</script>
	");
}



$home = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
#echo "HOME : $home<br><br>";
//phpinfo();
DEFINE(__KEY__,$_POST['encKey']);
DEFINE(__IV__,$_POST['encIV']);
DEFINE(__CIPHER__,$_POST['crypttype']);
DEFINE(__BinType__,$_POST['encBinType']);

function callUrl($url,$data=array(),$option=array()){		// curl 이용	
	//$headers = array('Content-Type:application/json', 'Authorization:key='.$server_key);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	if(!empty($option['method'])) curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $option['method']);	
	if(!empty($option['header'])) curl_setopt($ch, CURLOPT_HTTPHEADER, $option['header']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_VERBOSE, true);
	//curl_setopt ($ch, CURLOPT_COOKIE, $cookies);
	//curl_setopt ($ch, CURLOPT_REFERER, $referer_url);
	if(!empty($option['includeHeader'])) curl_setopt ($ch, CURLOPT_HEADER, 1); //헤더값을 가져오기위해 사용합니다. 쿠키를 가져오려고요.
	//curl_setopt ($curlsession, CURLOPT_USERAGENT, \"Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)\");

	if(!empty($data)){
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		
	}		
	if(substr($url,0,5) == "https"){
		curl_setopt($ch, CURLOPT_SSLVERSION,3); // SSL 버젼 (https 접속시에 필요)
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // 인증서 체크같은데 true 시 안되는 경우가 많다.		
	}
	else {
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);			
	}
	$result = curl_exec($ch);
	$resultCode = curl_errno($ch);
	$resultMsg = curl_error($ch);
	if ($result === FALSE) exit("err:<xmp>".print_r($resultCode,1)."</xmp>");
	curl_close($ch);
	return $result;

}	

if(1){
	$url = $_POST['hostname'];
	$plaintext = $_POST['postData'];
	$postType = $_POST['postType'];
	$option['method'] = $_POST['method'];

	foreach(json_decode($_POST['addHeader'],1) as $key=>$val) $option['header'][] = "{$key}:{$val}";
	#$option['header'] = ;
	#$option['header'][] = "apiOrigin:gwmart-sub2";
	#exit("<xmp>".print_r($option['header'],1)."</xmp>");

	$key = __KEY__;
	$iv =  __IV__;
	$commonlib->binEnc = $_POST['encBinType'];

	if($postType =="plainPost"){
		$ciphertext = json_decode($plaintext,1);

		foreach($ciphertext as $k =>$v) {
			if(is_array($v)) $data[$k] = json_encode($v,JSON_UNESCAPED_UNICODE);
			else $data[$k] = $v;
		}

		$result = callUrl($url,$data,$option);
	}
	else {	#dataJson
		if(__CIPHER__ != "text") $ciphertext =openssl_encrypt($plaintext, __CIPHER__, $key, 0, $iv);
		else $ciphertext=$plaintext;
		#echo "* __CIPHER__ => [".__CIPHER__."]<br>";
		#echo "* ciphertext => [$ciphertext]<br>";
		#echo "* ciphertext HEX => [".bin2hex(base64_decode($ciphertext))."]<br>";
		#echo "<hr>";

		if(__BinType__ == "base64" || __CIPHER__ == "text") $data = $ciphertext;
		else $data = bin2hex(base64_decode($ciphertext));
		$result = callUrl($url,$data,$option);
	}

	#echo "<xmp>[header]=>".print_r($data,1)."</xmp>";
	echo "<xmp>[request]=>".print_r($data,1)."</xmp>";
	echo "<xmp>[result]=>$result</xmp>";


	#echo("result=><xmp>".print_r($result,1)."</xmp>");
	#exit($result);
	if(__CIPHER__ == "text") $plaintext =$result;
	else if(__BinType__ == "base64") $plaintext =openssl_decrypt($result, __CIPHER__, $key, 0, $iv);
	else $plaintext = openssl_decrypt(base64_encode(@hex2bin($result)), __CIPHER__, $key, 0, $iv);
	if(empty($plaintext)) exit("decrypt error >>".$result);
	else echo $plaintext;
	#echo("<xmp>".print_r($plaintext,1)."</xmp>");

	#echo("<xmp>".print_r(json_decode($plaintext,1),1)."</xmp>");
	#echo "<button onclick=\"location.href='$home';\">돌아가기</button>";
	exit;
}


#exit("<xmp>".print_r($_POST,1)."</xmp>");
?>

<?
/*

[상품카테고리]
http://dapikorcham.firstmall.kr/api_service/?pvkey=pvtP4DaGKgEqQ&service=goods_category&json_unicode=1


[상품등록1]
http://dapikorcham.firstmall.kr/api_service/?pvkey=pvtP4DaGKgEqQ&service=goods_reg&json_unicode=1
{ 
"goods_seq":"", 
"category_code":"0006,00060004", 
"goods_view":"노출", 
"goods_code":"상품기본코드", 
"goods_name":"상품명", 
"summary":"간략설명", 
"keyword":"검색어", 
"option":[
	{"option_title":"색깔,크기","option":"빨강,대","consumer_price":"3000","price":"2000","stock":"500"}
	,{"option_title":"색깔,크기","option":"노랑,중","consumer_price":"3000","price":"2100","stock":"500"}
	,{"option_title":"색깔,크기","option":"노랑,소","consumer_price":"3000","price":"2200","stock":"500"}
	,{"option_title":"색깔,크기","option":"파랑,소","consumer_price":"3000","price":"2300","stock":"500"}
	], 
"image":[
	{"large":"http://www.sample.com/sample.jpg","thumbView":"http://www.sample.com/sample.jpg"}
	,{"large":"http://www.sample.com/sample.jpg","thumbView":"http://www.sample.com/sample.jpg"}
	,{"large":"http://www.sample.com/sample.jpg","thumbView":"http://www.sample.com/sample.jpg"}
	],
"contents":"<div>상품설명</div>", 
"shipping_group_seq":"1"
}

[상품등록2]
http://dapikorcham.firstmall.kr/api_service/?pvkey=pvtP4DaGKgEqQ&service=goods_reg&json_unicode=1
{ 
"goods_seq":"74", 
"image":[
	{"large":"https://t1.daumcdn.net/cfile/tistory/995ADF475B2F349E01","thumbView":"https://t1.daumcdn.net/cfile/tistory/995ADF475B2F349E01"}
	,{"large":"https://i.imgur.com/QAKqrlI.jpg","thumbView":"https://i.imgur.com/QAKqrlI.jpg"}
	,{"large":"https://image.chosun.com/sitedata/image/201804/08/2018040800454_0.jpg","thumbView":"https://image.chosun.com/sitedata/image/201804/08/2018040800454_0.jpg"}
	]
}

[상품등록3]
http://dapikorcham.firstmall.kr/api_service/?pvkey=pvtP4DaGKgEqQ&service=goods_reg&json_unicode=1
{ 
"goods_seq":"74", 
"goods_name":"상품 변경 2 - 재고변경",
"option":[
	{"option_title":"색깔,크기","option":"빨강,대","consumer_price":"3000","price":"2000","stock":"200"}
	,{"option_title":"색깔,크기","option":"노랑,중","consumer_price":"3000","price":"2100","stock":"300"}
	,{"option_title":"색깔,크기","option":"노랑,소","consumer_price":"3000","price":"2200","stock":"400"}
	,{"option_title":"색깔,크기","option":"파랑,대","consumer_price":"3000","price":"2300","stock":"500"}
	]
}



[상품수정1]
http://dapikorcham.firstmall.kr/api_service/?pvkey=pvtP4DaGKgEqQ&service=goods_update&json_unicode=1
{ 
"goods_seq":"74", 
"goods_view":"미노출"
}

[상품수정2]
http://dapikorcham.firstmall.kr/api_service/?pvkey=pvtP4DaGKgEqQ&service=goods_update&json_unicode=1
{
"goods_seq":"73", 
"option":[
	{"option_title":"색깔,크기","option":"빨강,대","consumer_price":"3000","price":"2400","stock":"100"}
	,{"option_title":"색깔,크기","option":"노랑,중","consumer_price":"3000","price":"2500","stock":"100"}
	,{"option_title":"색깔,크기","option":"노랑,소","consumer_price":"3000","price":"2600","stock":"100"}
	,{"option_title":"색깔,크기","option":"파랑,대","consumer_price":"3000","price":"2700","stock":"100"}
	]
}

[상품등록 동기화]
http://sdh2korcham.firstmall.kr/cli/apisyncgoodss

*/
?>