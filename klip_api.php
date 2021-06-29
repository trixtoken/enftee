<?php
include "./inc/_common.php";

// $_param = array(
// 	'module' => 'transaction',
// 	'action' => 'getstatus',
// 	'txhash' => '0xbc8b667a7a8eeda3fa841ff8daffa45103603813d9fc99e3fa35cbe758429e5d',
// 	'apikey' => 'DXQCBSEBE2GJKXSESV1FYGNDJCYUM78T2F',
// );

$_param = array(
	'module' => 'proxy',
	'action' => 'eth_getTransactionByHash',
	'txhash' => '0x5fb93eb57cae2a68ad25980630b38bca3ef5e0ca7350813198537a68c75a2e91',
	'apikey' => 'DXQCBSEBE2GJKXSESV1FYGNDJCYUM78T2F',
);

if ($user_agent != 'web') {
	$_url = $_cfg['eth']['url_web']."?".http_build_query($_param);
} else {
	$_url = $_cfg['eth']['url']."?".http_build_query($_param);
}

// GET방식
// $_url = "https://api.etherscan.io/api?".http_build_query($_param); //실서버
// $_url = "https://api-ropsten.etherscan.io/api?".http_build_query($_param); //테스트넷

$curlObj = curl_init();
curl_setopt($curlObj, CURLOPT_URL, $_url);
curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($curlObj, CURLOPT_HEADER, 0);
$response = curl_exec($curlObj);
$_json = json_decode($response,true);
curl_close($curlObj);

echo "<pre>";
print_r($_json);

// if ($_json['result']['isError'] == 0) {
// 	echo "거래완료";
// } else if ($_json['result']['isError'] == 1) {
// 	echo "거래대기중";
// }

// // POST방식
// $_url = "https://api.klipwallet.com/v2/partner/auth";

// // $post_field_string = http_build_query($_param, '', '&');
// $curlObj = curl_init();
// curl_setopt($curlObj, CURLOPT_URL, $url);
// curl_setopt($curlObj, CURLOPT_POST, 1);
// curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($curlObj, CURLOPT_POSTFIELDS, $_param);
// // curl_setopt($curlObj, CURLOPT_CONNECTTIMEOUT, 10);
// // curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, false);

// $headers = [
//     'Content-Type: application/json'
// ];

// curl_setopt($curlObj, CURLOPT_HTTPHEADER, $headers);

// $response = curl_exec($curlObj);
// $_json = json_decode($response,true);
// curl_close ($curlObj);

// print_r($response);
// exit;


// $vars = array(
// 	'email' => 'Kayro.seoul@gmail.com',
// 	'password' => '92407c83d15f86fa78e3a827db0e8bd2be17998b1e9aa3cfd3c10a7b0d549a1b'
// );

// $ch = curl_init();
// curl_setopt($ch, CURLOPT_URL,"https://api.klipwallet.com/v2/partner/auth");
// curl_setopt($ch, CURLOPT_POST, 1);
// curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);  //Post Fields
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// $headers = [
//     'Content-Type: application/json'
// ];

// curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// $server_output = curl_exec ($ch);

// curl_close ($ch);

// print_r($server_output);



?>