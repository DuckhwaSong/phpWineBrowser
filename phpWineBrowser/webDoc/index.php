<?
# http://sandbox.localhost/apiTester2/

header("Content-Type: text/html; charset=UTF-8");
phpinfo();exit;
#$config = parse_ini_file("config.ini",true);
//echo "<xmp>".print_r($config,1)."</xmp>";


# 라이브러리 호출
include("common.lib.php");
$commonlib = new commonlib();

/*
# test 
$plaintext ="test";
$key = "123456789ABCDEFG";
$iv = "123456789ABCDEFG";

$ciphertext = $commonlib->encrypt($plaintext,"AES-128-CBC",$key,$iv);
echo "1=><xmp>$ciphertext</xmp>";

$ciphertext = openssl_encrypt($plaintext, "AES-128-ECB", $key, 0, $iv);
echo "2=><xmp>$ciphertext</xmp>";

$commonCrypt = new commonCrypt("rijndael-128");
$commonCrypt->encMode = strtolower("ECB");
$ciphertext = $commonCrypt->encrypt($plaintext,$key,$iv);
$ciphertext = base64_encode($ciphertext);
echo "3=><xmp>$ciphertext</xmp>";

$decrypt = $commonCrypt->decrypt(base64_decode($ciphertext),$key,$iv);
echo "decrypt=><xmp>$decrypt</xmp>";
*/

# 세팅 값 불러오기
$setting = parse_ini_file("setting.ini");
#foreach($setting as $key => $val) $setting[$key] = openssl_decrypt($val, "AES-128-ECB","123456789ABCDEFG");
foreach($setting as $key => $val) $setting[$key] = $commonlib->decrypt($val, "AES-128-ECB","123456789ABCDEFG");

//echo "<xmp>".print_r($setting,1)."</xmp>";

$soluTypeArr = $config['Type'];

# 인풋 보조함수
CLASS form{
	static function input($title,$key,$option,$value=""){
		if(!$option['size']) $option['size'] = "30";
		if(0);
		else return "
		<li class='form-line form-line-column form-col-5' data-type='control_textbox' id='id_$key'>
			<label class='form-label form-label-top' id='label_$key' for='input_$key'> $title </label>
			<div id='cid_$key' class='form-input-wide'>
			<input type='text' id='input_$key' name='$key' data-type='input-textbox' class='form-textbox' size='{$option['size']}' value='$value' placeholder='{$option['placeholder']}' data-component='textbox' />
			</div>
		</li>
		";
	}
	static function text($title,$key,$option=array(),$value=""){
		if(!$option['cols']) $option['cols'] = "40";
		if(!$option['rows']) $option['rows'] = "6";
		if(0);
		else return "
		<li class='form-line' data-type='control_textarea' id='id_$key'>
			<label class='form-label form-label-top form-label-auto' id='label_$key' for='input_$key'> $title </label>
			<div id='cid_$key' class='form-input-wide'>
			<textarea id='input_$key'  name='$key' class='form-textarea' cols='{$option['cols']}' rows='{$option['rows']}' data-component='textarea' aria-labelledby='label_$key' placeholder='{$option['placeholder']}'>$value</textarea>
			</div>
		</li>
		";
	}

	static function select($title,$key,$option,$value=""){
		foreach($option['arrOption'] as $verK => $verT) $arrOption .= "<option value='$verK' ".(($verK==$value)?"selected":"").">$verT</option>";
		if(0);
		else return "
		<li class='form-line form-line-column form-col-5' data-type='control_textbox' id='id_$key'>
			<label class='form-label form-label-top' id='label_$key' for='input_$key'> $title </label>
			<div id='cid_$key' class='form-input-wide'>
			  <select class='form-dropdown' id='input_$key' name='$key' style='{$option['style']}width:150px' data-component='dropdown' aria-labelledby='input_$key'>
				$arrOption
			  </select>
			</div>
		  </li>
		";
	}
	static function checkbox($title,$key,$option,$value=""){
		foreach($option['arrOption'] as $verK => $verT) $arrOption .= "<label><input type='checkbox' name='{$key}[]' value='$verK' ".(($value==$verK)?"checked":"").">$verT</label>";
		if(0);
		else return "
		<li class='form-line form-line-column form-col-1' data-type='control_textbox' id='id_$key'>
			<label class='form-label form-label-top' id='label_$key' for='input_$key'> $title </label>
			<div id='cid_$key' class='form-input-wide'>
				<input type='hidden' name='{$key}[]'>
				$arrOption
			</div>
		</li>
		";
	}
	static function radio($title,$key,$option,$value=""){
		foreach($option['arrOption'] as $verK => $verT) $arrOption .= "<label><input type='radio' name='{$key}' value='$verK' ".(($value==$verK)?"checked":"").">$verT</label>";
		if(0);
		else return "
		<li class='form-line form-line-column form-col-1' data-type='control_textbox' id='id_$key'>
			<label class='form-label form-label-top' id='label_$key' for='input_$key'> $title </label>
			<div id='cid_$key' class='form-input-wide'>
				$arrOption
			</div>
		</li>
		";
	}
	static function file($title,$key,$option,$value=""){
		if(0);
		else return "
		<li class='form-line form-line-column form-col-5' data-type='control_textbox' id='id_$key'>
			<div id='cid_$key' class='form-input-wide'>
				<div style='text-align:left' class='form-buttons-wrapper'>
				<input type='file' id='input_$key' name='$key' data-type='input-textbox' class='form-submit-button' data-component='button' data-content='' webkitdirectory>
				</div>
			</div>
		</li>
		";
	}
	static function button($title,$key,$type="button"){
		if(0);
		else return "
		<li class='form-line form-line-column form-col-2' data-type='control_textbox' id='id_$key'>
			<div id='cid_$key' class='form-input-wide'>
				<div style='text-align:left' class='form-buttons-wrapper'>
				<button id='input_$key' name='$key' type='$type' class='form-submit-button' data-component='button' data-content=''>$title</button>
				</div>
			</div>
		</li>
		";
	}
	static function submit($title,$key=""){
		if(0);
		else return "
		<li class='form-line form-line-column form-col-2' data-type='control_textbox' id='id_$key'>
			<div id='cid_$key' class='form-input-wide'>
				<div style='text-align:left' class='form-buttons-wrapper'>
				<button id='input_$key' name='$key' type='submit' class='form-submit-button' data-component='button' data-content=''>$title</button>
				</div>
			</div>
		</li>
		";
	}



}
# 타이틀 설정
$title = "API 테스터";
$titleComment ="API 통신 및 암호화 테스트툴 (나가는 IP: <span id='outIp'>1212</span>) <button type='button' id='logBtn'>log</button>";
              

# 폼설정 
/*$echoForm .= form::input("사이트명","sitename",array("placeholder"=>"abc.firstmall.kr"),$setting['sitename']);
$echoForm .= form::input("마스터DIR","masterdir",array("placeholder"=>"C:\\work\\soliution_master\\"),$setting['masterdir']);
$echoForm .= form::input("설치DIR","installdir",array("placeholder"=>"C:\\work\\installdir\\"),"");
$echoForm .= form::input("설치날짜(최종패치날짜)","patchdate",array("placeholder"=>"2019-05-23","size"=>"20"),$setting['patchdate']);
$echoForm .= form::select("솔루션타입2","solutype",array("style"=>"width:150px","arrOption"=>array("1"=>"1")),$setting['solutype']);

$echoForm .= form::checkbox("솔루션스키마","schema",array("arrOption"=>array("값1"=>"타이틀1","값2"=>"타이틀2")),$setting['schema']);
$echoForm .= form::radio("라디오","radio",array("arrOption"=>array("값1"=>"타이틀1","값2"=>"타이틀2")),$setting['radio']);

$echoForm .= form::file("파일","tmpfile",array("arrOption"=>array("값1"=>"타이틀1","값2"=>"타이틀2")),$setting['tmpfile']);

$echoForm .= form::button("버튼","button");
$echoForm .= form::submit("전송","submit");
*/

$echoForm .= form::input("접속HOST","hostname",array("size"=>"50","placeholder"=>"http://www.hosthostname.com"),$setting['hostname']);
$echoForm .= form::select("암호화 알고리즘","crypttype",array("style"=>"width:150px","arrOption"=>array("text"=>"NO crypt","AES-128-CBC"=>"AES-128-CBC","SEED-CBC"=>"SEED-CBC(128)")),$setting['crypttype']);
$echoForm .= form::select("인코딩 방식","encBinType",array("style"=>"width:150px","arrOption"=>array(""=>"None","hex"=>"hex","base64"=>"base64")),$setting['encBinType']);

$echoForm .= form::input("암호화키","encKey",array(),$setting['encKey']);
$echoForm .= form::input("암호화IV","encIV",array(),$setting['encIV']);

$echoForm .= form::radio("method","method",array("arrOption"=>array("GET"=>"GET","POST"=>"POST","PUT"=>"PUT","PATCH"=>"PATCH","DELETE"=>"DELETE")),$setting['method']);
$echoForm .= form::radio("데이터전송방식","postType",array("arrOption"=>array("dataJson"=>"dataJson","plainPost"=>"plainPost (json전송데이터를 POST로 전송)")),$setting['postType']);


$echoForm .= form::text("전송데이터","postData",array(),$setting['postData']);
$echoForm .= "<li class='form-line' style='border:0px solid red; width: 100%; height: 1px; padding:0; margin:0;'></li>";	# 줄바꿈
$echoForm .= form::text("addHeader","addHeader",array(),$setting['addHeader']);
$echoForm .= "<li class='form-line' style='border:0px solid red; width: 100%; height: 1px; padding:0; margin:0;'></li>";	# 줄바꿈
$echoForm .= form::button("전송","submitBtn");
#$echoForm .= form::submit("전송","submit");
#$echoForm .= form::text("전송결과","result",array("rows"=>"10"));

$jScript = "$(document).ready(function() {
	// outIP
	$.ajax({
		url : 'https://api.ip.pe.kr/',
		type : 'post',
		success : function(data) {
			$('#outIp').html(data);
		}
	});
	
	// 로그파일 SELECT로 보여줌
	$('#logBtn').on('click',function(){
		$.ajax({
			url : 'logProcess.php',
			type : 'post',
			data : {mode:'file'},
			success : function(data) {
				$('#subHeader_log').html(data);
			}
		});

		//$('#subHeader_log').html('121212');
	});	

	// ajax로 데이터 전송 후 화면에 전달
	$('#input_submitBtn').on('click',function(){
		//alert('input_submitBtn');
		var rand = Math.floor(Math.random()*100);
		if(rand%5==0) var color='red';
		if(rand%5==1) var color='blue';
		if(rand%5==2) var color='green';
		if(rand%5==3) var color='brown';
		if(rand%5==4) var color='orange';
		$.ajax({
			url : 'apiProcess.php',
			type : 'post',
			data : $('#apiTesterFrom').serialize(),
			success : function(data) {
				//alert(data);
				$('#outputText').html(data).css('border','1px solid '+ color);
				$('#outputForm').show();
			}				
		});

	});
});";
$jScript .= "
function logDateSelect(obj){
	var logfile = obj.val();
	//alert(subHeader_log);
	$('#logArray').remove();
	if(!logfile) return 0;
	$.ajax({
		url : 'logProcess.php',
		type : 'post',
		data : {mode:'array',file:logfile},
		success : function(data) {
			obj.after(data);
		}
	});
}
function logDateLoad(obj){
	var logdate = obj.val();
	var logfile = $('select[name=logfile]').val();
	//return alert(logfile);
	if(!logdate) return 0;
	$.ajax({
		url : 'logProcess.php',
		type : 'post',
		data : {mode:'load',file:logfile,date:logdate},
		success : function(data) {
			//alert(data);
			var jsonData = JSON.parse(data);			
			for(key in jsonData) valueLoader(key,jsonData[key]);
			$('#subHeader_log').html('');
		}
	});
}

// 폼정보수정 Jquery - 유용한함수될듯
function valueLoader(name,value){
	$('input[name='+name+'][type=text]').val(value);
	$('select[name='+name+']').val(value);
	$('textarea[name='+name+']').val(value);
	$('input[name='+name+'][type=radio]').each(function(){
		if( $(this).val() == value ) $(this).prop('checked',true);
	});
}

";

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html class="supernova"><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta property="og:title" content="Course Registration Form" >
<meta property="og:description" content="Please click the link to complete this form.">
<meta name="slack-app-id" content="AHNMASS8M">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=1" />
<meta name="HandheldFriendly" content="true" />
<title>Course Registration Form</title>
<link href="formCss.css" rel="stylesheet" type="text/css" />
<link type="text/css" rel="stylesheet" href="nova.css" />
<link type="text/css" rel="stylesheet" href="theme.css"/>
<style type="text/css">
    .form-label-left{
        width:150px;
    }
    .form-line{	/* 공백조절가능 */
        padding-top:6px;
        padding-bottom:6px;
		margin-top: 6px;
		margin-bottom: 6px;
		border : 0px solid red;
    }
    .form-label-right{
        width:150px;
    }
    body, html{
        margin:0;
        padding:0;
        background:rgb(153, 153, 153);
    }

    .form-all{
        margin:0px auto;
        padding-top:0px;
        width:500px;
        color:#555 !important;
        font-family:"Lucida Grande", "Lucida Sans Unicode", "Lucida Sans", Verdana, sans-serif;
        font-size:14px;
    }
    .form-radio-item label, .form-checkbox-item label, .form-grading-label, .form-header{
        color: #555;
    }

</style>

<style type="text/css" id="form-designer-style">
    /* Injected CSS Code */
/*PREFERENCES STYLE*/
    .form-label.form-label-auto {
      
    display: block;
    float: none;
    text-align: left;
  
    }
  
    .form-line {
      margin-top: 12px 36px 12px 36px px;
      margin-bottom: 12px 36px 12px 36px px;
    }
  
    .form-all {
      width: 500px;
    }
  
    .form-label-left,
    .form-label-right {
      width: 150px
    }
  
    .form-all {
      font-family: Lucida Grande, sans-serif;
    }
    .form-all .qq-upload-button,
    .form-all .form-submit-button,
    .form-all .form-submit-reset,
    .form-all .form-submit-print {
      font-family: Lucida Grande, sans-serif;
    }
    .form-all .form-pagebreak-back-container,
    .form-all .form-pagebreak-next-container {
      font-family: Lucida Grande, sans-serif;
    }
    .form-header-group {
      font-family: Lucida Grande, sans-serif;
    }
    .form-label {
      font-family: Lucida Grande, sans-serif;
    }
  
    .form-all {
      font-size: 14px
    }
    .form-all .qq-upload-button,
    .form-all .qq-upload-button,
    .form-all .form-submit-button,
    .form-all .form-submit-reset,
    .form-all .form-submit-print {
      font-size: 14px
    }
    .form-all .form-pagebreak-back-container,
    .form-all .form-pagebreak-next-container {
      font-size: 14px
    }
  
    .supernova .form-all, .form-all {
      background-color: rgb(153, 153, 153);
      border: 1px solid transparent;
    }
  
    .form-all {
      color: #555;
    }
    .form-header-group .form-header {
      color: #555;
    }
    .form-header-group .form-subHeader {
      color: #555;
    }
    .form-label-top,
    .form-label-left,
    .form-label-right,
    .form-html,
    .form-checkbox-item label,
    .form-radio-item label {
      color: #555;
    }
  
    .supernova {
      background-color: undefined;
    }
    .supernova body {
      background: transparent;
    }
  
    .form-textbox,
    .form-textarea,
    .form-radio-other-input,
    .form-checkbox-other-input,
    .form-captcha input,
    .form-spinner input {
      background-color: undefined;
    }
  
      .supernova {
        height: 100%;
        background-repeat: repeat;
        background-attachment: scroll;
        background-position: center top;
      }
      .supernova {
        background-image: url("brushed.png");
      }
      #stage {
        background-image: url("brushed.png");
      }
    
      .form-all {
        background-image: url("brushed.png");
        background-repeat: repeat;
        background-attachment: scroll;
        background-position: center top;
      }
    /*PREFERENCES STYLE*//*__INSPECT_SEPERATOR__*/
    /* Injected CSS Code */
</style>

<!--script src="https://cdnjs.cloudflare.com/ajax/libs/punycode/1.4.1/punycode.min.js"></script-->
<script
  src="https://code.jquery.com/jquery-1.12.4.min.js"
  integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ="
  crossorigin="anonymous"></script>
<script
  src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"
  integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU="
  crossorigin="anonymous"></script>

</head>
<body>

<form action="apiProcess.php"class="jotform-form" method="post" name="apiTesterFrom" id="apiTesterFrom" accept-charset="utf-8" autocomplete="on">
  <div role="main" class="form-all">

	<ul class="form-section page-section" id="outputForm" style='display:none;'>
		<li id="cid_1" class="form-input-wide" data-type="control_head">
        <div class="form-header-group ">
          <div class="header-text httal htvam">
            <div class="form-subHeader" id="outputText" style='border:1px solid red;'>
				
            </div>
          </div>
        </div>
		</li>
	</ul>

    <ul class="form-section page-section" id="inputForm">
      <li id="cid_1" class="form-input-wide" data-type="control_head">
        <div class="form-header-group ">
          <div class="header-text httal htvam">
            <h1 id="header_1" class="form-header" data-component="header">
              <?=$title?>
            </h1>
            <div id="subHeader_1" class="form-subHeader">
				<?=$titleComment?>
            </div>
            <div id="subHeader_log" class="form-subHeader"></div> 
          </div>
        </div>
      </li>

		<?=$echoForm?>









      <!--li class="form-line" data-type="control_fullname" id="id_4">
        <label class="form-label form-label-top form-label-auto" id="label_4" for="first_4"> Student Name </label>
        <div id="cid_4" class="form-input-wide">
          <div data-wrapper-react="true">
            <span class="form-sub-label-container " style="vertical-align:top">
              <input type="text" id="first_4" name="q4_studentName[first]" class="form-textbox" size="10" value="" data-component="first" aria-labelledby="label_4 sublabel_4_first" />
              <label class="form-sub-label" for="first_4" id="sublabel_4_first" style="min-height:13px" aria-hidden="false"> First Name </label>
            </span>
            <span class="form-sub-label-container " style="vertical-align:top">
              <input type="text" id="middle_4" name="q4_studentName[middle]" class="form-textbox" size="10" value="" data-component="middle" aria-labelledby="label_4 sublabel_4_middle" />
              <label class="form-sub-label" for="middle_4" id="sublabel_4_middle" style="min-height:13px" aria-hidden="false"> Middle Name </label>
            </span>
            <span class="form-sub-label-container " style="vertical-align:top">
              <input type="text" id="last_4" name="q4_studentName[last]" class="form-textbox" size="15" value="" data-component="last" aria-labelledby="label_4 sublabel_4_last" />
              <label class="form-sub-label" for="last_4" id="sublabel_4_last" style="min-height:13px" aria-hidden="false"> Last Name </label>
            </span>
          </div>
        </div>
      </li>


      <li class="form-line form-line-column form-col-1" data-type="control_birthdate" id="id_24">
        <label class="form-label form-label-top" id="label_24" for="input_24"> Birth Date </label>
        <div id="cid_24" class="form-input-wide">
          <div data-wrapper-react="true">
            <span class="form-sub-label-container " style="vertical-align:top">
              <select name="q24_birthDate24[month]" id="input_24_month" class="form-dropdown" data-component="birthdate-month" aria-labelledby="label_24 sublabel_24_month">
                <option>  </option>
                <option value="January"> January </option>
                <option value="February"> February </option>
                <option value="March"> March </option>
                <option value="April"> April </option>
 
              </select>
              <label class="form-sub-label" for="input_24_month" id="sublabel_24_month" style="min-height:13px" aria-hidden="false"> Month </label>
            </span>
            <span class="form-sub-label-container " style="vertical-align:top">
              <select name="q24_birthDate24[day]" id="input_24_day" class="form-dropdown" data-component="birthdate-day" aria-labelledby="label_24 sublabel_24_day">
                <option>  </option>
                <option value="1"> 1 </option>
                <option value="2"> 2 </option>
                <option value="3"> 3 </option>
                <option value="4"> 4 </option>
                <option value="5"> 5 </option>
                <option value="6"> 6 </option>

              </select>
              <label class="form-sub-label" for="input_24_day" id="sublabel_24_day" style="min-height:13px" aria-hidden="false"> Day </label>
            </span>
            <span class="form-sub-label-container " style="vertical-align:top">
              <select name="q24_birthDate24[year]" id="input_24_year" class="form-dropdown" data-component="birthdate-year" aria-labelledby="label_24 sublabel_24_year">
                <option>  </option>
                <option value="2020"> 2020 </option>
                <option value="2019"> 2019 </option>
                <option value="2018"> 2018 </option>
                <option value="2017"> 2017 </option>

              </select>
              <label class="form-sub-label" for="input_24_year" id="sublabel_24_year" style="min-height:13px" aria-hidden="false"> Year </label>
            </span>
          </div>
        </div>
      </li>


      <li class="form-line form-line-column form-col-2" data-type="control_dropdown" id="id_3">
        <label class="form-label form-label-top" id="label_3" for="input_3"> Gender </label>
        <div id="cid_3" class="form-input-wide">
          <select class="form-dropdown" id="input_3" name="q3_gender" style="width:150px" data-component="dropdown" aria-labelledby="label_3">
            <option value="">  </option>
            <option value="Male"> Male </option>
            <option value="Female"> Female </option>
            <option value="N/A"> N/A </option>
          </select>
        </div>
      </li>
      <li class="form-line" data-type="control_address" id="id_23">
        <label class="form-label form-label-top form-label-auto" id="label_23" for="input_23undefined"> Address </label>
        <div id="cid_23" class="form-input-wide">
          <table summary="" class="form-address-table">
            <tbody>
              <tr>
                <td colSpan="2">
                  <span class="form-sub-label-container " style="vertical-align:top">
                    <input type="text" id="input_23_addr_line1" name="q23_address[addr_line1]" class="form-textbox form-address-line" value="" data-component="address_line_1" aria-labelledby="label_23 sublabel_23_addr_line1" />
                    <label class="form-sub-label" for="input_23_addr_line1" id="sublabel_23_addr_line1" style="min-height:13px" aria-hidden="false"> Street Address </label>
                  </span>
                </td>
              </tr>
              <tr>
                <td colSpan="2">
                  <span class="form-sub-label-container " style="vertical-align:top">
                    <input type="text" id="input_23_addr_line2" name="q23_address[addr_line2]" class="form-textbox form-address-line" size="46" value="" data-component="address_line_2" aria-labelledby="label_23 sublabel_23_addr_line2" />
                    <label class="form-sub-label" for="input_23_addr_line2" id="sublabel_23_addr_line2" style="min-height:13px" aria-hidden="false"> Street Address Line 2 </label>
                  </span>
                </td>
              </tr>
              <tr>
                <td>
                  <span class="form-sub-label-container " style="vertical-align:top">
                    <input type="text" id="input_23_city" name="q23_address[city]" class="form-textbox form-address-city" size="21" value="" data-component="city" aria-labelledby="label_23 sublabel_23_city" />
                    <label class="form-sub-label" for="input_23_city" id="sublabel_23_city" style="min-height:13px" aria-hidden="false"> City </label>
                  </span>
                </td>
                <td>
                  <span class="form-sub-label-container " style="vertical-align:top">
                    <input type="text" id="input_23_state" name="q23_address[state]" class="form-textbox form-address-state" size="22" value="" data-component="state" aria-labelledby="label_23 sublabel_23_state" />
                    <label class="form-sub-label" for="input_23_state" id="sublabel_23_state" style="min-height:13px" aria-hidden="false"> State / Province </label>
                  </span>
                </td>
              </tr>
              <tr>
                <td>
                  <span class="form-sub-label-container " style="vertical-align:top">
                    <input type="text" id="input_23_postal" name="q23_address[postal]" class="form-textbox form-address-postal" size="10" value="" data-component="zip" aria-labelledby="label_23 sublabel_23_postal" />
                    <label class="form-sub-label" for="input_23_postal" id="sublabel_23_postal" style="min-height:13px" aria-hidden="false"> Postal / Zip Code </label>
                  </span>
                </td>
                <td>
                  <span class="form-sub-label-container " style="vertical-align:top">
                    <select class="form-dropdown form-address-country noTranslate" name="q23_address[country]" id="input_23_country" data-component="country" aria-labelledby="label_23 sublabel_23_country">
                      <option value=""> Please Select </option>
                      <option value="United States"> United States </option>
                      <option value="Afghanistan"> Afghanistan </option>
                      <option value="Albania"> Albania </option>
                      <option value="Algeria"> Algeria </option>
                      <option value="American Samoa"> American Samoa </option>
                    </select>
                    <label class="form-sub-label" for="input_23_country" id="sublabel_23_country" style="min-height:13px" aria-hidden="false"> Country </label>
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </li>


      <li class="form-line form-line-column form-col-1" data-type="control_email" id="id_6">
        <label class="form-label form-label-top" id="label_6" for="input_6"> Student E-mail </label>
        <div id="cid_6" class="form-input-wide">
          <input type="email" id="input_6" name="q6_studentEmail6" class="form-textbox validate[Email]" size="30" value="" placeholder="ex: myname@example.com" data-component="email" aria-labelledby="label_6" />
        </div>
      </li>


      <li class="form-line form-line-column form-col-2" data-type="control_phone" id="id_27">
        <label class="form-label form-label-top" id="label_27" for="input_27_area"> Mobile Number </label>
        <div id="cid_27" class="form-input-wide">
          <div data-wrapper-react="true">
            <span class="form-sub-label-container " style="vertical-align:top">
              <input type="tel" id="input_27_area" name="q27_mobileNumber[area]" class="form-textbox" size="6" value="" data-component="areaCode" aria-labelledby="label_27 sublabel_27_area" />
              <span class="phone-separate" aria-hidden="true">
                 -
              </span>
              <label class="form-sub-label" for="input_27_area" id="sublabel_27_area" style="min-height:13px" aria-hidden="false"> Area Code </label>
            </span>
            <span class="form-sub-label-container " style="vertical-align:top">
              <input type="tel" id="input_27_phone" name="q27_mobileNumber[phone]" class="form-textbox" size="12" value="" data-component="phone" aria-labelledby="label_27 sublabel_27_phone" />
              <label class="form-sub-label" for="input_27_phone" id="sublabel_27_phone" style="min-height:13px" aria-hidden="false"> Phone Number </label>
            </span>
          </div>
        </div>
      </li>


      <li class="form-line form-line-column form-col-3" data-type="control_phone" id="id_25">
        <label class="form-label form-label-top" id="label_25" for="input_25_area"> Phone Number </label>
        <div id="cid_25" class="form-input-wide">
          <div data-wrapper-react="true">
            <span class="form-sub-label-container " style="vertical-align:top">
              <input type="tel" id="input_25_area" name="q25_phoneNumber25[area]" class="form-textbox" size="6" value="" data-component="areaCode" aria-labelledby="label_25 sublabel_25_area" />
              <span class="phone-separate" aria-hidden="true">
                 -
              </span>
              <label class="form-sub-label" for="input_25_area" id="sublabel_25_area" style="min-height:13px" aria-hidden="false"> Area Code </label>
            </span>
            <span class="form-sub-label-container " style="vertical-align:top">
              <input type="tel" id="input_25_phone" name="q25_phoneNumber25[phone]" class="form-textbox" size="12" value="" data-component="phone" aria-labelledby="label_25 sublabel_25_phone" />
              <label class="form-sub-label" for="input_25_phone" id="sublabel_25_phone" style="min-height:13px" aria-hidden="false"> Phone Number </label>
            </span>
          </div>
        </div>
      </li>


      <li class="form-line form-line-column form-col-4" data-type="control_phone" id="id_26">
        <label class="form-label form-label-top" id="label_26" for="input_26_area"> Work Number </label>
        <div id="cid_26" class="form-input-wide">
          <div data-wrapper-react="true">
            <span class="form-sub-label-container " style="vertical-align:top">
              <input type="tel" id="input_26_area" name="q26_workNumber[area]" class="form-textbox" size="6" value="" data-component="areaCode" aria-labelledby="label_26 sublabel_26_area" />
              <span class="phone-separate" aria-hidden="true">
                 -
              </span>
              <label class="form-sub-label" for="input_26_area" id="sublabel_26_area" style="min-height:13px" aria-hidden="false"> Area Code </label>
            </span>
            <span class="form-sub-label-container " style="vertical-align:top">
              <input type="tel" id="input_26_phone" name="q26_workNumber[phone]" class="form-textbox" size="12" value="" data-component="phone" aria-labelledby="label_26 sublabel_26_phone" />
              <label class="form-sub-label" for="input_26_phone" id="sublabel_26_phone" style="min-height:13px" aria-hidden="false"> Phone Number </label>
            </span>
          </div>
        </div>
      </li>


      <li class="form-line form-line-column form-col-5" data-type="control_textbox" id="id_14">
        <label class="form-label form-label-top" id="label_14" for="input_14"> Company </label>
        <div id="cid_14" class="form-input-wide">
          <input type="text" id="input_14" name="q14_company" data-type="input-textbox" class="form-textbox" size="20" value="" placeholder=" " data-component="textbox" aria-labelledby="label_14" />
        </div>
      </li>


      <li class="form-line" data-type="control_dropdown" id="id_46">
        <label class="form-label form-label-top form-label-auto" id="label_46" for="input_46"> Courses </label>
        <div id="cid_46" class="form-input-wide">
          <select class="form-dropdown" id="input_46" name="q46_courses" style="width:150px" data-component="dropdown" aria-labelledby="label_46">
            <option value="">  </option>
            <option value="Windows 8"> Windows 8 </option>
            <option value="Introduction to Linux"> Introduction to Linux </option>
            <option value="English 101"> English 101 </option>
            <option value="English 102"> English 102 </option>
            <option value="Creative Writing 1"> Creative Writing 1 </option>
            <option value="Creative writing 2"> Creative writing 2 </option>
            <option value="History 101"> History 101 </option>
            <option value="History 102"> History 102 </option>
            <option value="Math 101"> Math 101 </option>
            <option value="Math 102"> Math 102 </option>
          </select>
        </div>
      </li>


      <li class="form-line" data-type="control_textarea" id="id_45">
        <label class="form-label form-label-top form-label-auto" id="label_45" for="input_45"> Additional Comments </label>
        <div id="cid_45" class="form-input-wide">
          <textarea id="input_45" class="form-textarea" name="q45_clickTo45" cols="40" rows="6" data-component="textarea" aria-labelledby="label_45"></textarea>
        </div>
      </li>


      <li class="form-line form-line-column form-col-1" data-type="control_button" id="id_20">
        <div id="cid_20" class="form-input-wide">
          <div style="text-align:left" class="form-buttons-wrapper ">
            <button id="input_20" type="submit" class="form-submit-button" data-component="button" data-content="">
              Submit Application
            </button>
          </div>
        </div>
      </li>


      <li class="form-line form-line-column form-col-2" data-type="control_button" id="id_19">
        <div id="cid_19" class="form-input-wide">
          <div style="text-align:right" class="form-buttons-wrapper ">
            <button id="input_19" type="submit" class="form-submit-button" data-component="button" data-content="">
              Clear Fields
            </button>
          </div>
        </div>
      </li>


      <li style="clear:both">
      </li>
      <li style="display:none">
        Should be Empty:
        <input type="text" name="website" value="" />
      </li-->



    </ul>
  </div>



</form></body>
<script>
<?=$jScript?>
</script>
</html>
