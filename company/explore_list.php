<?php
include "../inc/_common.php";

$t_menu = 1;
$l_menu = 1;

$tpl=new Template;
$tpl->define(array(
	'contents'  =>'company/explore_list.tpl',

	'left'  =>	'inc/cs_left.tpl',

	'body'  =>'inc/body.tpl',
	'top'  =>'inc/top.tpl',
	'quick'  => 'inc/quick.tpl',
	'bottom'  => 'inc/bottom.tpl',
));

//코인시셋 바로 가져오기
$one_dollar = trix_coin_api();
$tpl->assign('one_dollar', $one_dollar['one_dollar']);

$tag_arr = explode(",", $_cfg['config']['cf_tag']);

if ($_GET['stx']) {
	// $sql = "select * from rb_product where concat(',', pd_tag, ',') like '%,".$_GET['stx'].",%' and pd_use = 1 ";
	$sql = "SELECT * FROM (
								SELECT * FROM rb_product
								WHERE (pd_img_url, pd_img_num) 
								IN (
									SELECT pd_img_url, MIN( pd_img_num ) AS pd_img_num
									FROM rb_product
									WHERE pd_use = 1 and IF( pd_buy_idx = 0, pd_buy_idx = 0, pd_img_num = pd_img_cnt) and pd_type = 2 and concat(',', pd_tag, ',') like '%,".$_GET['stx'].",%' 
									GROUP BY pd_img_url
								)
							)t
							GROUP BY t.pd_img_url
						";
	$data = sql_list($sql);
	foreach ($data as $key => $value) {
		//파일정보
		$pd_file_data = sql_list("select * from rb_product_file where pd_idx = '".$value['pd_idx']."' order by fi_num asc");
		$data[$key]['fi_name'] = $pd_file_data[0]['fi_name'];

		if ($value['pd_buy_idx']) {
			$sql_buyer = "select pb.pb_idx, mb.mb_idx, mb.mb_nick, mb.mb_img1 from rb_product_buyer as pb 
										left join rb_member as mb on mb.mb_idx = pb.mb_idx 
										where pb.pd_idx = '".$value['pd_idx']."' order by pb_idx desc limit 0, 1 ";
		} else {
			$sql_buyer = "select pb.pb_idx, mb.mb_idx, mb.mb_nick, mb.mb_img1 from rb_product_buyer as pb 
										left join rb_member as mb on mb.mb_idx = pb.mb_idx 
										where pb.pd_img_url = '".$value['pd_img_url']."' order by pb_idx desc limit 0, 1 ";
		}
		$data_buyer = sql_fetch($sql_buyer);
		$data[$key]['pd_buyer_idx'] = $data_buyer['mb_idx'];
		$data[$key]['pd_buyer_nick'] = $data_buyer['mb_nick'];
		$data[$key]['pd_buyer_img'] = $data_buyer['mb_img1'];

		$pd_price_arr = explode('.', $value['pd_price']);
		$data[$key]['pd_price_1'] = $pd_price_arr[0];
		$data[$key]['pd_price_2'] = $pd_price_arr[1];

		$trix_price = $value['pd_price'] * $one_dollar['one_dollar'];
		$trix_price_arr = explode('.', $trix_price);
		$trix_price_view = number_format($trix_price_arr[0]).".".substr($trix_price_arr[1], 0, 1);
		$data[$key]['trix_price_view'] = $trix_price_view;

		//sold out 계산
		$sql_out = "select count(*) as sell_cnt from rb_product where pd_img_url = '".$value['pd_img_url']."' and pd_buy_idx = 0 ";
		$data_out = sql_fetch($sql_out);
		if ($data_out['sell_cnt'] == 0) {
			$data[$key]['pd_sold_out'] = true;
		} else {
			$data[$key]['pd_sold_out'] = false;
		}
		
	}

	$tpl->assign('data', $data); 

} else {
	
	foreach ($tag_arr as $key_2 => $value_2) {
		// $sql = "select * from rb_product where concat(',', pd_tag, ',') like '%,".$value_2.",%' and pd_use = 1 ";

		$sql = "SELECT * FROM (
								SELECT * FROM rb_product
								WHERE (pd_img_url, pd_img_num) 
								IN (
									SELECT pd_img_url, MIN( pd_img_num ) AS pd_img_num
									FROM rb_product
									WHERE pd_use = 1 and IF( pd_buy_idx = 0, pd_buy_idx = 0, pd_img_num = pd_img_cnt) and pd_type = 2 and concat(',', pd_tag, ',') like '%,".$value_2.",%'
									GROUP BY pd_img_url
								)
							)t
							GROUP BY t.pd_img_url
						";

		$data = sql_list($sql);
		foreach ($data as $key => $value) {
			//파일정보
			// $pd_file_data = sql_list("select * from rb_product_file where pd_idx = '".$value['pd_idx']."' order by fi_num asc");
			// $data[$key]['fi_name'] = $pd_file_data[0]['fi_name'];

			if ($value['pd_buy_idx']) {
				$sql_buyer = "select pb.pb_idx, mb.mb_idx, mb.mb_nick, mb.mb_img1 from rb_product_buyer as pb 
											left join rb_member as mb on mb.mb_idx = pb.mb_idx 
											where pb.pd_idx = '".$value['pd_idx']."' order by pb_idx desc limit 0, 1 ";
			} else {
				$sql_buyer = "select pb.pb_idx, mb.mb_idx, mb.mb_nick, mb.mb_img1 from rb_product_buyer as pb 
											left join rb_member as mb on mb.mb_idx = pb.mb_idx 
											where pb.pd_img_url = '".$value['pd_img_url']."' order by pb_idx desc limit 0, 1 ";
			}
			$data_buyer = sql_fetch($sql_buyer);
			$data[$key]['pd_buyer_idx'] = $data_buyer['mb_idx'];
			$data[$key]['pd_buyer_nick'] = $data_buyer['mb_nick'];
			$data[$key]['pd_buyer_img'] = $data_buyer['mb_img1'];

			$pd_price_arr = explode('.', $value['pd_price']);
			$data[$key]['pd_price_1'] = $pd_price_arr[0];
			$data[$key]['pd_price_2'] = $pd_price_arr[1];

			$trix_price = $value['pd_price'] * $one_dollar['one_dollar'];
			$trix_price_arr = explode('.', $trix_price);
			$trix_price_view = number_format($trix_price_arr[0]).".".substr($trix_price_arr[1], 0, 1);
			$data[$key]['trix_price_view'] = $trix_price_view;

			$data_list[$key_2]['tag_name'] = $value_2;
			$data_list[$key_2]['tag_list'] = $data;
		}
		
		
	}

	
	$tpl->assign('data_list', $data_list); 
}



include "../inc/_head.php";
$tpl->print_('body');
include "../inc/_tail.php";
?>