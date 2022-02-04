<?php
/*
*
* @Author : iewil
* @Github : https://www.github.com/iewilmaestro
* @Youtube: https://www.youtube.com/c/iewil
* @Support: Team-Function-Family
*
*/
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
	
	$Create=json_decode(Run($host.'/createTask',$ua,$data));
	if($Create->errorId == '1'){
		return 0;
	}else{
		$Task=$Create->taskId;
		while(true){
			$data=json_encode(array("clientKey"=>$apikey,"taskId"=>$Task));
			$Result=json_decode(Run($host.'/getTaskResult',$ua,$data));
			if($Result->status=='processing'){
				sleep(5);continue;
			}
			return $Result->solution->gRecaptchaResponse;
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

echo str_repeat('~',56)."\n";

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

$siteurl = "https://recaptcha.example.com";
/* @example Api RecaptchaV2 */
$sitekey = "6Ld06asZAAAAAPKfIQIFkOct7aLdb2cDeEI1gFJ5";
/* @example Api HCaptcha */
$sitekey = "4dc72c58-72a1-40b9-b244-83b5187a64aa";

$apikey = "YOUR_APIKEY";

$respon = Bypass($typeApi, $typeCaptcha, $siteurl, $sitekey, $apikey);

echo $respon;

