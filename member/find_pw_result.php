<?php
$page_name = "member";
include "../inc/_common.php";

$t_menu = 9;
$l_menu = 4;

already_logged();

$tpl=new Template;
$tpl->define(array(
    'contents'  =>'member/find_pw_result.tpl',
	'left'  =>	'inc/member_left.tpl',
	'body'  =>'inc/body.tpl',
	'top'  =>'inc/top.tpl',
	'quick'  => 'inc/quick.tpl',
	'bottom'  => 'inc/bottom.tpl',
));

$tpl->assign('mb_id', $_SESSION['find_idpw_id']);

unset($_SESSION['find_idpw_id']);

include "../inc/_head.php";
$tpl->print_('body');
include "../inc/_tail.php";
?>