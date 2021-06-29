<?php
$menu_num = 0;
include "../inc/_common.php";
include "../inc/_head.php";

goto_login();


$t_menu = 8;
$l_menu = 7;


$tpl=new Template;
$tpl->define(array(
    'contents'  =>'member/point_list.tpl',

	'left'  =>	'inc/mypage_left.tpl',

	'body'  =>'inc/body.tpl',
	'top'  =>'inc/top.tpl',
	'quick'  => 'inc/quick.tpl',
	'bottom'  => 'inc/bottom.tpl',

	'my_top'  => 'inc/my_top.tpl',
));

$tpl->assign('page_title', '포인트목록');

$querys = array();
$search_query = "";

if($_GET['ph_status']){
	if($_GET['ph_status'] == 1){
		$search_query .= " and ph_point > 0 ";
	}else if($_GET['ph_status'] == 2){
		$search_query .= " and ph_point < 0 ";
	}
}

if($_GET['s_period'] == "" && $_GET['s_start'] == "" && $_GET['s_end'] == ""){
	$_GET['s_period'] = "1";
	$_GET['s_start'] = date("Y-m-d", strtotime("-".$_GET['s_period']." months", time()));
	$_GET['s_end'] = date("Y-m-d");
}else if($_GET['s_period'] != "" && $_GET['s_start'] == "" && $_GET['s_end'] == ""){
	$_GET['s_start'] = date("Y-m-d", strtotime("-".$_GET['s_period']." months", time()));
	$_GET['s_end'] = date("Y-m-d");
}
$querys[] = "s_period=".$_GET['s_period'];

if($_GET['s_start']){
	$search_query .= " and SUBSTRING(ph_regdate, 1, 10) >= '".$_GET['s_start']."' ";
}

$querys[] = "s_start=".$_GET['s_start'];


if($_GET['s_end']){
	$search_query .= " and SUBSTRING(ph_regdate, 1, 10) <= '".$_GET['s_end']."' ";
}

$querys[] = "s_end=".$_GET['s_end'];



$order_query = "order by ph_idx desc";

$query = (is_array($querys) && count($querys) > 0) ? implode("&", $querys) : "";

$sql = "select * from rb_point_history where mb_id = '".$member['mb_id']."' $search_query $order_query";
$data = sql_list($sql);

$tpl->assign('data', $data); 

$tpl->print_('body');
include "../inc/_tail.php";
?>