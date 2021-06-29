<?php
$menu_code = "110100";
$menu_mode = "w";

$limit_access = "";
$limit_access_level = "";

include "../inc/_common.php";
include "../inc/_head.php";
include "../inc/_check.php";

$tpl=new Template;
$tpl->define(array(
    'contents'  =>'config/config_insert.tpl',
	'body'  =>'inc/body.tpl',
	'top'  =>'inc/top.tpl',
	'bottom'  =>'inc/bottom.tpl',
	'second_menu' => 'inc/second_menu.tpl',
));

$tpl->assign('page_title', get_txt_from_data($_cfg[menu_data], $menu_code, "menu_code", "menu_name")."- 수정");
$tpl->assign('mode', "update");

$_param = array(
	'symbol' => 'TRIX-USDT'
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


$one_trix = $_json['data'][0]['c'];
$one_dollar = 1 / $one_trix;

$tpl->assign('one_trix', $one_trix);
$tpl->assign('one_dollar', $one_dollar);



$tpl->print_('body');
include "../inc/_tail.php";
?> 
