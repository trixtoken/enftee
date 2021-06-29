<?php
$page_name = "index";

include "./inc/_common.php";
$is_index = 1;

//$is_sub_admin_page = 1;
if ($user_agent != "app") {
	$gnb = "1";
}

$t_menu = 0;
$l_menu = 0;


$tpl=new Template;
$tpl->define(array(
    'contents'  =>'index.tpl',
	'body'  =>'inc/body.tpl',
	'top'  =>'inc/top.tpl',
	'quick'  => 'inc/quick.tpl',
	'bottom'  => 'inc/bottom.tpl',
));
$product_config = $_cfg['product_config'];
$tpl->assign('product_config', $product_config);


//팝업
$popup_data = sql_list("select * from rb_popup where pp_agent = '".$user_agent."' and pp_use = 1 ");
for($i=0;$i<custom_count($popup_data);$i++){
	if( $_COOKIE['popup_'.$popup_data[$i]['pp_idx']] != "done"){
		$popup_data[$i]['pp_view'] = 1;
	}else{
		$popup_data[$i]['pp_view'] = 0;
	}
}
$tpl->assign('popup_data', $popup_data);

//코인시셋 바로 가져오기
$one_dollar = trix_coin_api();
$tpl->assign('one_dollar', $one_dollar['one_dollar']);


if($user_agent == "web"){
	$banner_basic = 100;
}else if($user_agent == "mobile"){
	$banner_basic = 300;
}else if($user_agent == "app"){
	$banner_basic = 600;
}
//$banner_basic = 0;
//스크롤배너
$banner1 = sql_list("select * from rb_banner where bn_loc = '".(1 + $banner_basic)."' order by bn_sort desc");
$tpl->assign('banner1', $banner1);

// //메인중단
// $banner2 = sql_list("select * from rb_banner where bn_loc = '".(2 + $banner_basic)."' order by bn_sort desc");
// $tpl->assign('banner2', $banner2);

// //최상단 folding-banner
// $banner3 = sql_list("select * from rb_banner where bn_loc = '".(3 + $banner_basic)."' order by bn_sort desc limit 0, 1");
// $tpl->assign('banner3', $banner3);
// //p_arr($banner3);


//new
// $sql_new = "select * from rb_product where 1 order by pd_idx desc limit 0, 3";

$sql_new = "SELECT * FROM (
							SELECT * FROM rb_product
							WHERE (pd_img_url, pd_img_num)
							IN (
								SELECT pd_img_url, MIN( pd_img_num ) AS pd_img_num
								FROM rb_product
								WHERE pd_use = 1 and IF( pd_buy_idx = 0, pd_buy_idx = 0, pd_img_num = pd_img_cnt) and pd_type = 2 
								GROUP BY pd_img_url
							)
						)t
						GROUP BY t.pd_img_url
						ORDER BY t.pd_idx DESC
						limit 0, 6";
$data_new = sql_list($sql_new);
foreach ($data_new as $key => $value) {
	$pd_price_arr = explode('.', $value['pd_price']);
	$data_new[$key]['pd_price_1'] = $pd_price_arr[0];
	$data_new[$key]['pd_price_2'] = $pd_price_arr[1];

	$trix_price = $value['pd_price'] * $one_dollar['one_dollar'];
	$trix_price_arr = explode('.', $trix_price);
	$trix_price_view = number_format($trix_price_arr[0]).".".substr($trix_price_arr[1], 0, 1);
	$data_new[$key]['trix_price_view'] = $trix_price_view;

	// $pd_file_data = sql_list("select * from rb_product_file where pd_idx = '".$value['pd_idx']."' order by fi_num asc");
	// $data_new[$key]['fi_name'] = $pd_file_data[0]['fi_name'];
	if ($data_new['pd_buy_idx']) {
		$sql_buyer = "select pb.pb_idx, mb.mb_idx, mb.mb_nick, mb.mb_img1 from rb_product_buyer as pb
									left join rb_member as mb on mb.mb_idx = pb.mb_idx
									where pb.pd_idx = '".$value['pd_idx']."' order by pb_idx desc limit 0, 1 ";
	} else {
		$sql_buyer = "select pb.pb_idx, mb.mb_idx, mb.mb_nick, mb.mb_img1 from rb_product_buyer as pb
									left join rb_member as mb on mb.mb_idx = pb.mb_idx
									where pb.pd_img_url = '".$value['pd_img_url']."' order by pb_idx desc limit 0, 1 ";
	}
	$data_buyer = sql_fetch($sql_buyer);
	$data_new[$key]['pd_buyer_idx'] = $data_buyer['mb_idx'];
	$data_new[$key]['pd_buyer_nick'] = $data_buyer['mb_nick'];
	$data_new[$key]['pd_buyer_img'] = $data_buyer['mb_img1'];

	//sold out 계산
	$sql_out = "select count(*) as sell_cnt from rb_product where pd_img_url = '".$value['pd_img_url']."' and pd_buy_idx = 0 ";
	$data_out = sql_fetch($sql_out);
	if ($data_out['sell_cnt'] == 0) {
		$data_new[$key]['pd_sold_out'] = true;
	} else {
		$data_new[$key]['pd_sold_out'] = false;
	}
}
$tpl->assign('data_new', $data_new);

//best
// $sql_best = "select p.pd_img_url from rb_icon as ic
// 							left join rb_product as p on ic.pd_idx = p.pd_idx
// 							where p.pd_use = 1 and ic.ic_type = '1' order by rand() limit 0, 3";
// $data_best = sql_list($sql_best);

$sql_best = "SELECT * FROM (
							SELECT * FROM rb_product
							WHERE (pd_img_url, pd_img_num)
							IN (
								SELECT pd_img_url, MIN( pd_img_num ) AS pd_img_num
								FROM rb_product
								WHERE pd_use = 1 and pd_img_url
									IN(select p.pd_img_url from rb_icon as ic
											left join rb_product as p on ic.pd_idx = p.pd_idx
											where p.pd_use = 1 and ic.ic_type = '1'  and p.pd_type = 2 order by rand()
									) and IF( pd_buy_idx = 0, pd_buy_idx = 0, pd_img_num = pd_img_cnt)
								GROUP BY pd_img_url
							)
						)t
						GROUP BY t.pd_img_url
						limit 0, 6";

$data_best = sql_list($sql_best);
foreach ($data_best as $key => $value) {
	$pd_price_arr = explode('.', $value['pd_price']);
	$data_best[$key]['pd_price_1'] = $pd_price_arr[0];
	$data_best[$key]['pd_price_2'] = $pd_price_arr[1];

	$trix_price = $value['pd_price'] * $one_dollar['one_dollar'];
	$trix_price_arr = explode('.', $trix_price);
	$trix_price_view = number_format($trix_price_arr[0]).".".substr($trix_price_arr[1], 0, 1);
	$data_best[$key]['trix_price_view'] = $trix_price_view;

	// $pd_file_data = sql_list("select * from rb_product_file where pd_idx = '".$value['pd_idx']."' order by fi_num asc");
	// $data_best[$key]['fi_name'] = $pd_file_data[0]['fi_name'];

	if ($data_new['pd_buy_idx']) {
		$sql_buyer = "select pb.pb_idx, mb.mb_idx, mb.mb_nick, mb.mb_img1 from rb_product_buyer as pb
									left join rb_member as mb on mb.mb_idx = pb.mb_idx
									where pb.pd_idx = '".$value['pd_idx']."' order by pb_idx desc limit 0, 1 ";
	} else {
		$sql_buyer = "select pb.pb_idx, mb.mb_idx, mb.mb_nick, mb.mb_img1 from rb_product_buyer as pb
									left join rb_member as mb on mb.mb_idx = pb.mb_idx
									where pb.pd_img_url = '".$value['pd_img_url']."' order by pb_idx desc limit 0, 1 ";
	}
	$data_buyer = sql_fetch($sql_buyer);
	$data_best[$key]['pd_buyer_idx'] = $data_buyer['mb_idx'];
	$data_best[$key]['pd_buyer_nick'] = $data_buyer['mb_nick'];
	$data_best[$key]['pd_buyer_img'] = $data_buyer['mb_img1'];

	//sold out 계산
	$sql_out = "select count(*) as sell_cnt from rb_product where pd_img_url = '".$value['pd_img_url']."' and pd_buy_idx = 0 ";
	$data_out = sql_fetch($sql_out);
	if ($data_out['sell_cnt'] == 0) {
		$data_best[$key]['pd_sold_out'] = true;
	} else {
		$data_best[$key]['pd_sold_out'] = false;
	}
}
$tpl->assign('data_best', $data_best);

//recommend
// $sql_re = "select p.pd_img_url from rb_icon as ic
// 							left join rb_product as p on ic.pd_idx = p.pd_idx
// 							where p.pd_use = 1 and ic.ic_type = '2' order by rand() limit 0, 3";

$sql_re = "SELECT * FROM (
						SELECT * FROM rb_product
						WHERE (pd_img_url, pd_img_num)
						IN (
							SELECT pd_img_url, MIN( pd_img_num ) AS pd_img_num
							FROM rb_product
							WHERE pd_use = 1 and pd_img_url
								IN(select p.pd_img_url from rb_icon as ic
										left join rb_product as p on ic.pd_idx = p.pd_idx
										where p.pd_use = 1 and ic.ic_type = '2' and p.pd_type = 2  order by rand()
								) and IF( pd_buy_idx = 0, pd_buy_idx = 0, pd_img_num = pd_img_cnt)
							GROUP BY pd_img_url
						)
					)t
					GROUP BY t.pd_img_url
					limit 0, 6";
$data_re = sql_list($sql_re);
foreach ($data_re as $key => $value) {
	$pd_price_arr = explode('.', $value['pd_price']);
	$data_re[$key]['pd_price_1'] = $pd_price_arr[0];
	$data_re[$key]['pd_price_2'] = $pd_price_arr[1];

	$trix_price = $value['pd_price'] * $one_dollar['one_dollar'];
	$trix_price_arr = explode('.', $trix_price);
	$trix_price_view = number_format($trix_price_arr[0]).".".substr($trix_price_arr[1], 0, 1);
	$data_re[$key]['trix_price_view'] = $trix_price_view;

	// $pd_file_data = sql_list("select * from rb_product_file where pd_idx = '".$value['pd_idx']."' order by fi_num asc");
	// $data_re[$key]['fi_name'] = $pd_file_data[0]['fi_name'];
	if ($data_new['pd_buy_idx']) {
		$sql_buyer = "select pb.pb_idx, mb.mb_idx, mb.mb_nick, mb.mb_img1 from rb_product_buyer as pb
									left join rb_member as mb on mb.mb_idx = pb.mb_idx
									where pb.pd_idx = '".$value['pd_idx']."' order by pb_idx desc limit 0, 1 ";
	} else {
		$sql_buyer = "select pb.pb_idx, mb.mb_idx, mb.mb_nick, mb.mb_img1 from rb_product_buyer as pb
									left join rb_member as mb on mb.mb_idx = pb.mb_idx
									where pb.pd_img_url = '".$value['pd_img_url']."' order by pb_idx desc limit 0, 1 ";
	}

	$data_buyer = sql_fetch($sql_buyer);
	$data_re[$key]['pd_buyer_idx'] = $data_buyer['mb_idx'];
	$data_re[$key]['pd_buyer_nick'] = $data_buyer['mb_nick'];
	$data_re[$key]['pd_buyer_img'] = $data_buyer['mb_img1'];

	//sold out 계산
	$sql_out = "select count(*) as sell_cnt from rb_product where pd_img_url = '".$value['pd_img_url']."' and pd_buy_idx = 0 ";
	$data_out = sql_fetch($sql_out);
	if ($data_out['sell_cnt'] == 0) {
		$data_re[$key]['pd_sold_out'] = true;
	} else {
		$data_re[$key]['pd_sold_out'] = false;
	}
}
$tpl->assign('data_re', $data_re);




include "./inc/_head.php";
$tpl->print_('body');
include "./inc/_tail.php";
?>