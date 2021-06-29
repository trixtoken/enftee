<?php
include "./inc/_common.php";

$_param = array(
	'symbol' => 'ETH-USDT'
);
$_url = "https://global-openapi.bithumb.pro/openapi/v1/spot/ticker?".http_build_query($_param);

$curlObj = curl_init();
curl_setopt($curlObj, CURLOPT_URL, $_url);
curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($curlObj, CURLOPT_HEADER, 0);
$response = curl_exec($curlObj);
$_json = json_decode($response,true);
curl_close($curlObj);

p_arr($_json);

$trix_usdt = $_json['data'][0]['c'];
$one_usd = 1 / $trix_usdt;
echo $one_usd;
exit;

?>