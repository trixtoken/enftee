<?php
$menu_code = "400100";
$menu_mode = "v";

include "../inc/_common.php";
include "../inc/_head.php";
include "../inc/_check.php";

$tpl=new Template;
$tpl->define(array(
	'contents'  =>'product/product_view.tpl',
	'body'  =>'inc/body.tpl',
	'top'  =>'inc/top.tpl',
	'bottom'  =>'inc/bottom.tpl',
	'second_menu' => 'inc/second_menu.tpl',
));

$tpl->assign('page_title', get_txt_from_data($_cfg['menu_data'], $menu_code, "menu_code", "menu_name")."- 보기");
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
	$search_query .= " and shop_id = '".$member['mb_id']."' ";
}

$data = sql_fetch("select p.* from rb_product as p where p.pd_idx = '$pd_idx' $search_query");

if(!$data['pd_idx']) {
	alert("없는 상품입니다.");
} else {
	$pd_price_arr = explode('.', $data['pd_price']);
	$data['pd_price_1'] = $pd_price_arr[0];
	$data['pd_price_2'] = $pd_price_arr[1];
}


/*
//조회수 체크
if($data['mb_id'] != $member['mb_id']){
	$chk = sql_fetch("select * from rb_product_history where pd_idx = '$pd_idx' and (mb_id = '$member['mb_id']' or bh_ip = '$_SERVER['REMOTE_ADDR']')");
	if(!$chk['bh_idx']){
		sql_query("insert into rb_product_history set pd_idx = '$pd_idx', bc_code = '$data['bc_code']', mb_id = '$member['mb_id']', bh_ip = '$_SERVER['REMOTE_ADDR']', bh_regdate = now()");
		$data['pd_view_cnt'] = $data['pd_view_cnt'] + 1;
		sql_query("update rb_product set pd_view_cnt = pd_view_cnt + 1 where pd_idx = '$pd_idx'");

	}
}
*/

$tpl->assign('data', $data);

//옵션
// $option = sql_list("select * from rb_product_stock where pd_idx = '$pd_idx'");
// $tpl->assign('option', $option);

$pd_file_data = sql_list("select * from rb_product_file where pd_idx = '$pd_idx' order by fi_num asc");
$tpl->assign('pd_file_data', $pd_file_data);


//구매자 정보 확인
// if ($data['pd_buy_idx']) {
// 	$sql = "select * from rb_member as mb where mb.mb_idx = '".$data['pd_buy_idx']."' ";
// 	$data_buyer = sql_fetch($sql);
// 	if ($data_buyer['mb_idx']) {
// 		$sql_od = "select * from rb_order where pd_idx = '".$data['pd_idx']."' and mb_idx = '".$data_buyer['mb_idx']."' order by od_idx desc";
// 		$data_od = sql_fetch($sql_od);
// 		$data_buyer['total_amount_all'] = $data_od['total_amount_all'];
// 		$data_buyer['total_pay_amount'] = $data_od['total_pay_amount'];
// 		$data_buyer['od_coin_status'] = $data_od['od_coin_status'];
// 	}
// 	$tpl->assign('data_buyer', $data_buyer);
// }

$sql = "select *, 
					(select mb_nick from rb_member where mb_idx = pb.mb_idx) as mb_nick,
					(select mb_img1 from rb_member where mb_idx = pb.mb_idx) as mb_img1,
					(select mb_coin_address from rb_member where mb_idx = pb.mb_idx) as mb_coin_address,
					(select total_amount_all from rb_order where od_idx = pb.od_idx) as total_amount_all,
					(select total_pay_amount from rb_order where od_idx = pb.od_idx) as total_pay_amount,
					(select od_coin_status from rb_order where od_idx = pb.od_idx) as od_coin_status
					from rb_product_buyer as pb 
					where pb.pd_idx = '".$data['pd_idx']."'
				";
$data_buyer = sql_list($sql);
$tpl->assign('data_buyer', $data_buyer);

////관련상품
// $product_relation_data = sql_list("select* from rb_product_relation as a left join rb_product as b on a.pd_idx = b.pd_idx where a.parent_idx = '".$pd_idx."' order by a.pr_order asc");
// $tpl->assign('product_relation_data', $product_relation_data);


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