<?php
$page_option = "not-content-padding-bottom";
include "../inc/_common.php";

$t_menu = 5;
$l_menu = 11;

$tpl=new Template;
$tpl->define(array(
    'contents'  =>'member/site_privacy.tpl',
	'left'  =>	'inc/cs_left.tpl',
	'body'  =>'inc/body.tpl',
	'top'  =>'inc/top.tpl',
	'quick'  => 'inc/quick.tpl',
	'bottom'  => 'inc/bottom.tpl',
));

include "../inc/_head.php";
$tpl->print_('body');
include "../inc/_tail.php";
?>