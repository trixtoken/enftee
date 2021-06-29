<?php
include "../inc/_common.php";

$t_menu = 8;
$l_menu = 1;

goto_login();

$tpl=new Template;
$tpl->define(array(
	'contents'  =>'member/collection_list.tpl',

	'left'  =>	'inc/mypage_left.tpl',

	'body'  =>'inc/body.tpl',
	'top'  =>'inc/top.tpl',
	'quick'  => 'inc/quick.tpl',
	'bottom'  => 'inc/bottom.tpl',

));


//코인시셋 바로 가져오기
$one_dollar = trix_coin_api();
$tpl->assign('one_dollar', $one_dollar['one_dollar']);

// GET 값 구해서 각각 필요에 따라 파라미터 만들기
if ($_GET['page']){
	$page = $_GET['page'];
} else {
	$page = 1;
}
$querys[] = "page=".$page;

if ($_GET['order_by']) {
	$order_by = $_GET['order_by'];
} else {
	$order_by = 1;
}
$querys[] = "order_by=".$order_by;
$querys_page[] = "order_by=".$order_by;

$order_query = " order by pd.pd_idx desc";

switch ($order_by) {
	case 1: //새로운거
		$order_query = " order by pd.pd_idx desc";
		break;
	case 2: //오래된거
		$order_query = " order by pd.pd_idx asc";
		break;
	case 3: //높은가격
		$order_query = " order by pd.pd_price desc";
		break;
	case 4: //낮은가격
		$order_query = " order by pd.pd_price asc";
		break;
	
	default:
		$order_query = " order by pd.pd_idx desc";
		break;
}

$query = (is_array($querys) && count($querys) > 0) ? implode("&", $querys) : "";
$query_page = (is_array($querys_page) && count($querys_page) > 0) ? implode("&", $querys_page) : "";


if ($_GET['mb_idx']) {
	$sql = "select * from rb_product_buyer as pb 
					left join rb_product as pd on pd.pd_idx = pb.pd_idx
					where pb.mb_idx = '".$_GET['mb_idx']."' and pd.pd_use = 1 $search_query $order_query";
	$data = sql_list($sql);
	$mb = get_member2($_GET['mb_idx']);
	$tpl->assign('mb_nick', $mb['mb_nick']);
} else {
	if ($is_member) {
		$sql = "select * from rb_product_buyer as pb 
						left join rb_product as pd on pd.pd_idx = pb.pd_idx
						where pb.mb_idx = '".$member['mb_idx']."' and pd.pd_use = 1 $search_query $order_query";
		$data = sql_list($sql);
		$tpl->assign('mb_nick', $member['mb_nick']);
	} else {
		alert("wrong approach");
	}
}

if ($data) {
	foreach ($data as $key => $value) {
		$pd_price_arr = explode('.', $value['pd_price']);
		$data[$key]['pd_price_1'] = $pd_price_arr[0];
		$data[$key]['pd_price_2'] = $pd_price_arr[1];

		$trix_price = $value['pd_price'] * $one_dollar['one_dollar'];
		$trix_price_arr = explode('.', $trix_price);
		$trix_price_view = number_format($trix_price_arr[0]).".".substr($trix_price_arr[1], 0, 1);
		$data[$key]['trix_price_view'] = $trix_price_view;

		$pd_file_data = sql_list("select * from rb_product_file where pd_idx = '".$value['pd_idx']."' order by fi_num asc");
		$data[$key]['fi_name'] = $pd_file_data[0]['fi_name'];

		$sql_buyer = "select pb.pb_idx, mb.mb_idx, mb.mb_nick, mb.mb_img1 from rb_product_buyer as pb 
									left join rb_member as mb on mb.mb_idx = pb.mb_idx 
									where pb.pd_idx = '".$value['pd_idx']."' order by pb_idx desc limit 0, 1 ";
		$data_buyer = sql_fetch($sql_buyer);
		$data[$key]['pd_buyer_idx'] = $data_buyer['mb_idx'];
		$data[$key]['pd_buyer_nick'] = $data_buyer['mb_nick'];
		$data[$key]['pd_buyer_img'] = $data_buyer['mb_img1'];

		//sold out 계산
		$sql_out = "select count(*) as sell_cnt from rb_product where pd_img_url = '".$value['pd_img_url']."' and pd_buy_idx = 0 ";
		$data_out = sql_fetch($sql_out);
		if ($data_out['sell_cnt'] == 0) {
			$data[$key]['pd_sold_out'] = true;
		} else {
			$data[$key]['pd_sold_out'] = false;
		}
	}
}

$tpl->assign('data', $data);




include "../inc/_head.php";
$tpl->print_('body');
include "../inc/_tail.php";
?>