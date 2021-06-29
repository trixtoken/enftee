<?php
$menu_code = "400112";
$menu_mode = "w";

include "../inc/_common.php";
include "../inc/_head.php";
include "../inc/_check.php";

$tpl=new Template;
$tpl->define(array(
    'contents'  =>'product/cate2_insert.tpl',
	'body'  =>'inc/body.tpl',
	'top'  =>'inc/top.tpl',
	'bottom'  =>'inc/bottom.tpl',
	'second_menu' => 'inc/second_menu.tpl',
));

$tpl->assign('page_title', get_txt_from_data($_cfg['menu_data'], $menu_code, "menu_code", "menu_name")."- 등록");
$tpl->assign('mode', "insert");

$cate2_config = $_cfg['cate2_config'];
$tpl->assign('cate2_config', $cate2_config); 

$search_query = "";


$querys = array();
$querys[] = "page=".$page;
$querys[] = "sca=".$_GET['sca'];
$querys[] = "stx=".$_GET['stx'];
$querys[] = "c1_idx=".$_GET['c1_idx'];
$query = (is_array($querys) && count($querys) > 0) ? implode("&", $querys) : "";

$sql = "select * from rb_cate1 as c1 where 1 order by c1.c1_sort asc";
$c1_data = sql_list($sql);

$tpl->assign('c1_data', $c1_data); 

$tpl->print_('body');
include "../inc/_tail.php";
?> 
