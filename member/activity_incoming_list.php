<?php
include "../inc/_common.php";

$t_menu = 8;
$l_menu = 1;

goto_login();

$tpl=new Template;
$tpl->define(array(
	'contents'  =>'member/activity_incoming_list.tpl',

	'left'  =>	'inc/mypage_left.tpl',

	'body'  =>'inc/body.tpl',
	'top'  =>'inc/top.tpl',
	'quick'  => 'inc/quick.tpl',
	'bottom'  => 'inc/bottom.tpl',

));


//코인시셋 바로 가져오기
$one_dollar = trix_coin_api();
$tpl->assign('one_trix', $one_dollar['one_trix']);


include "../inc/_head.php";
$tpl->print_('body');
include "../inc/_tail.php";
?>