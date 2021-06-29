<?php
$menu_code = "400100";
$menu_mode = "w";

include "../inc/_common.php";
include "../inc/_head.php";
include "../inc/_check.php";

$tpl=new Template;
$tpl->define(array(
    'contents'  =>'product/product_insert.tpl',
	'body'  =>'inc/body.tpl',
	'top'  =>'inc/top.tpl',
	'bottom'  =>'inc/bottom.tpl',
	'second_menu' => 'inc/second_menu.tpl',
	'find_address' => 'admin/inc/find_address.tpl',
));

$tpl->assign('page_title', get_txt_from_data($_cfg['menu_data'], $menu_code, "menu_code", "menu_name")."- 수정");
$tpl->assign('mode', "update");

$product_config = $_cfg['product_config'];
$tpl->assign('product_config', $product_config); 
$search_query = "";


$querys = array();
$querys[] = "page=".$page;
$querys[] = "sca=".$_GET['sca'];
$querys[] = "stx=".$_GET['stx'];
$querys[] = "c1_idx=".$_GET['c1_idx'];
$querys[] = "c2_idx=".$_GET['c2_idx'];
$querys[] = "c3_idx=".$_GET['c3_idx'];

$query = (is_array($querys) && count($querys) > 0) ? implode("&", $querys) : "";

if($is_seller){
	$search_query .= " and p.shop_id = '".$member['mb_id']."' ";
}

$data = sql_fetch("select * from rb_product as b where b.pd_idx = '$pd_idx' $search_query");

if(!$data['pd_idx']) alert("없는 상품입니다.");

$tpl->assign('data', $data);

if($product_config['is_file'] > 0){
	$pd_file_data = sql_list("select * from rb_product_file where pd_idx = '$pd_idx' order by fi_num asc");
	$start = count($pd_file_data);
	for($i=$start;$i<$product_config['is_file'];$i++){
		$temp = array();
		$temp['fi_idx'] = "";
		$temp['fi_num'] = "";
		$temp['fi_name'] = "";
		$temp['fi_name_org'] = "";
		$pd_file_data[] = $temp;
	}
}
$tpl->assign('pd_file_data', $pd_file_data);


////연관상품
// $product_relation = "";
// $comma = "";
// if($data['pd_relation'] == 2){
// 	$product_relation_data = sql_list("select* from rb_product_relation as a left join rb_product as b on a.pd_idx = b.pd_idx where a.parent_idx = '".$pd_idx."' order by a.pr_order asc");
// 	for($i=0;$i<custom_count($product_relation_data);$i++){
// 		$product_relation .= $comma.$product_relation_data[$i]['pd_idx']."|:|".$product_relation_data[$i]['pd_name']."|:|".$product_relation_data[$i]['pd_img'];
// 		$comma = "|;|";
// 	}
// }
// $tpl->assign('product_relation', $product_relation);

// $sql = "select * from rb_cate where ca_step = 1 order by ca_sort asc";
// $c1_data = sql_list($sql);
// $tpl->assign('c1_data', $c1_data); 

// $sql = "select * from rb_brand where 1 order by br_sort asc";
// $br_data = sql_list($sql);
// $tpl->assign('br_data', $br_data); 

// $tpl->assign('custom_field', $product_config['custom_field']); 
// $tpl->assign('info_field', $product_config['info_field']); 


$tpl->print_('body');
include "../inc/_tail.php";
?> 