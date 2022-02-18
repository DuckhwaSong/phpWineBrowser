<?
header("Content-Type: text/html; charset=UTF-8");
$mode = $_POST['mode'];
function logFiler($filename,$date=""){
	$file = file_get_contents($filename);
	$fileArray = explode("\n\n",$file);
	foreach($fileArray as $fileObj){
		list($dateKey)=explode("] =>Array",$fileObj);		
		$dateKey = substr($dateKey,1);
		if(!$dateKey) continue;
		$dateKey = str_replace("[","",$dateKey);
		
		$fileObjArr=explode("] => ",$fileObj);
		$contents = substr(array_pop($fileObjArr),0,-1);
		foreach($fileObjArr as $idx => $arrData){
			$key[$idx+1] = array_pop(explode("[",$arrData));
			if($idx) list($val[$idx]) = explode("[",$arrData);
		}
		foreach($key as $idx => $k) $objData[$dateKey][$k]=trim($val[$idx]);
		$objData[$dateKey]['postData'] = $contents;
		#exit("<xmp id='logArray'>".print_r($contents,1)."</xmp>");
		#echo("<xmp id='logArray'>key=>".print_r($key,1)."</xmp>");
		#echo("<xmp id='logArray'>val=>".print_r($val,1)."</xmp>");
		#exit("<xmp id='logArray'>".print_r($objData,1)."</xmp>");
	}
	#exit("<div id='logArray'>".print_r($objData,1)."</div>");
	#exit("<xmp id='logArray'>objData=>".print_r($objData,1)."</xmp>");
	if(!empty($date) && $objData[$date]) return $objData[$date];
	return $objData;
}

if($mode=="file"){
	$scandir = 	scandir("./");
	foreach($scandir as $file){
		if(substr($file,0,1)==".") continue;
		if(substr($file,-4)==".log") $select[] = $file;
	}
	$select = array_reverse($select);
	$selectOption ="<option value=''>===========</option>";
	foreach($select as $logfile) $selectOption .="<option>$logfile</option>";
	exit("<select name='logfile' onchange='logDateSelect($(this));'>$selectOption</select>"); 
}
else if($mode=="array"){
	$objData = logFiler($_POST['file']);
	$selectOption ="<option value=''>===========</option>";
	foreach($objData as $date => $data){
		$selectOption .="<option>$date</option>";
		$jsonDate = json_encode($data);
		#exit("<xmp id='logArray'>jsonDate=>".print_r($jsonDate,1)."</xmp>");
	}
	exit("<div id='logArray'><select name='logdate' onchange='logDateLoad($(this));'>$selectOption</select></div>"); 
}
else if($mode=="load"){
	$objData = logFiler($_POST['file'],$_POST['date']);
	exit(json_encode($objData));
	exit("<xmp id='logArray'>objData=>".print_r($objData,1)."</xmp>");

}
exit("<xmp>".print_r($select,1)."</xmp>");

?>