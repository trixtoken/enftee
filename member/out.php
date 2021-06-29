<?php
$page_option = "bgwhite header-white";
include "../inc/_common.php";

$t_menu = 8;
$l_menu = 4;

goto_login();

if($is_admin){
	alert("관리자는 탈퇴가 불가능합니다.." , "/index.php");
}

$tpl=new Template;
$tpl->define(array(
    'contents'  =>'member/out.tpl',

	'left'  =>	'inc/mypage_left.tpl',

	'body'  =>'inc/body.tpl',
	'top'  =>'inc/top.tpl',
	'quick'  => 'inc/quick.tpl',
	'bottom'  => 'inc/bottom.tpl',

	'my_top'  => 'inc/my_top.tpl',
));

$sql = "select * from rb_country_tel";
$data_tel = sql_list($sql);
$tpl->assign('data_tel', $data_tel);

include "../inc/_head.php";
$tpl->print_('body');
include "../inc/_tail.php";
?>