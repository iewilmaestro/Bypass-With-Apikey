<?php
/*
*
* @Author : iewil
* @Github : https://www.github.com/iewilmaestro
* @Youtube: https://www.youtube.com/c/iewil
* @Support: Team-Function-Family
*
*/
function Run($url, $httpheader = 0, $post = 0, $proxy = 0){ // url, postdata, http headers, proxy, uagent
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($ch, CURLOPT_COOKIE,TRUE);
	if($post){
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	}
	if($httpheader){
		curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
	}
	if($proxy){
		curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
		curl_setopt($ch, CURLOPT_PROXY, $proxy);
		//curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
	}
	curl_setopt($ch, CURLOPT_HEADER, true);
	$response = curl_exec($ch);
	$httpcode = curl_getinfo($ch);
	if(!$httpcode) return "Curl Error : ".curl_error($ch); else{
		$header = substr($response, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
		$body = substr($response, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
		curl_close($ch);
		return array($header, $body)[1];
	}
}
function arrayInsert($array, $position, $insertArray){
    $ret = [];
    if($position == count($array)) {
        $ret = $array + $insertArray;
    }else{
        $i = 0;
        foreach($array as $key => $value){
            if($position == $i++){
                $ret += $insertArray;
            }
            $ret[$key] = $value;
        }
    }
    return $ret;
}
function Bypass($typeApi, $typeCaptcha, $siteurl, $sitekey, $apikey){
	$base = json_decode(file_get_contents("https://bypass-61f6b-default-rtdb.firebaseio.com/iewil.json"),1);
	$host = $base["typeApi"][$typeApi]["host"];
	$ua = $base["typeApi"][$typeApi]["header"];
	$type = $base[$typeCaptcha]["data"];
	$data=json_encode(array("clientKey"=>$apikey,"task"=>arrayInsert($type,1,["websiteURL"=>$siteurl,"websiteKey"=>$sitekey])));
	
	$Create=json_decode(Run($host.'/createTask',$ua,$data),1);
	if($Create["errorId"] == 1){
		echo $Create["errorCode"];
		exit;
	}else{
		$Task=$Create["taskId"];
		while(true){
			$data=json_encode(array("clientKey"=>$apikey,"taskId"=>$Task));
			$Result=json_decode(Run($host.'/getTaskResult',$ua,$data),1);
			if($Result["status"] == 'processing'){
				sleep(5);continue;
			}
			return $Result["solution"]["gRecaptchaResponse"];
		}
	}
}
TypeApi:
echo "1 - Anycaptcha\n";
echo "2 - Anticaptcha\n";
$menu = readline("Input type Api : ");
if($menu==1){
	$typeApi = "anycaptcha";
}else if($menu==2){
	$typeApi = "anticaptcha";
}else{
	echo "Bad Number\n";
	goto TypeApi;
}

TypeCaptcha:
echo "1 - RecaptchaV2\n";
echo "2 - Hcaptcha\n";
$menu = readline("Input type Api : ");
if($menu==1){
	$typeCaptcha = "recaptcha";
}else if($menu==2){
	$typeCaptcha = "hcaptcha";
}else{
	echo "Bad Number\n";
	goto TypeCaptcha;
}

/*
* PLEASE REPLACE WITH YOUR DATA
*
* @example site https://recaptcha.example.com 
*/
$siteurl = "sITE_URL";

/*
* @example Api RecaptchaV2 : 6Ld06asZAAAAAPKfIQIFkOct7aLdb2cDeEI1gFJ5
* @example Api HCaptcha : 4dc72c58-72a1-40b9-b244-83b5187a64aa 
*/
$sitekey = "SITE_KEY";

$apikey = "YOUR_APIKEY";

$respon = Bypass($typeApi, $typeCaptcha, $siteurl, $sitekey, $apikey);

echo $respon;

