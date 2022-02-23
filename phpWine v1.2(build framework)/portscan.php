<?
exec("netstat -tn",$netstat);
foreach($netstat as $data){
    list(,$port_ip)=explode(":",$data);
    list($port)=explode(" ",$port_ip);
    $usedPort[]=$port;
}
$rand=rand(21090,21099);
for($i=$rand;;$i++){
    if(!in_array($i,$usedPort)){
		//unlink("port.ini");
        //file_put_contents("port.ini", $i);
		echo "$i";
        break;
    }
}


?>