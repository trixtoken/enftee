<?php
$is_icon_menu = 1;
$menu_mode = "v";

include "../inc/_common.php";
include "../inc/_head.php";
include "../inc/_check.php";

$tpl=new Template;
$tpl->define(array(
    'contents'  =>'product/icon_list.tpl',
	'body'  =>'inc/body.tpl',
	'top'  =>'inc/top.tpl',
	'bottom'  =>'inc/bottom.tpl',
	'second_menu' => 'inc/second_menu.tpl',
));

$tpl->assign('page_title', get_txt_from_data($_cfg['menu_data'], $menu_code, "menu_code", "menu_name")."- 목록");


$querys = array();
$querys_page = array();
$search_query = "";

$search_query .= " and ic.ic_type = '$ic_type' ";
$querys[] = "ic_type=".$ic_type;
$tpl->assign('ic_type', $ic_type);

$order_query = "order by ic.ic_sort asc";

$query = (is_array($querys) && count($querys) > 0) ? implode("&", $querys) : "";
$query_page = (is_array($querys_page) && count($querys_page) > 0) ? implode("&", $querys_page) : "";

$sql = "select ic.*, p.* from rb_icon as ic left join rb_product as p on ic.pd_idx = p.pd_idx where p.pd_use = 1 $search_query $order_query ";
$data = sql_list($sql);

foreach ($data as $key => $value) {
	$pd_price_arr = explode('.', $value['pd_price']);
	$data[$key]['pd_price_1'] = $pd_price_arr[0];
	$data[$key]['pd_price_2'] = $pd_price_arr[1];
}

$tpl->assign('data', $data); 


$tpl->print_('body');
include "../inc/_tail.php";
?> 
