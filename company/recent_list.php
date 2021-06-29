<?php
include "../inc/_common.php";

$t_menu = 1;
$l_menu = 1;

$tpl=new Template;
$tpl->define(array(
	'contents'  =>'company/recent_list.tpl',

	'left'  =>	'inc/cs_left.tpl',

	'body'  =>'inc/body.tpl',
	'top'  =>'inc/top.tpl',
	'quick'  => 'inc/quick.tpl',
	'bottom'  => 'inc/bottom.tpl',
	'photo_swipe'  =>'inc/photo_swipe.tpl',
));


// GET 값 구해서 각각 필요에 따라 파라미터 만들기
if($_GET['page']){
	$page = $_GET['page'];
}else{
	$page = 1;
}
$querys[] = "page=".$page;

$order_query = "order by ph.ph_regdate desc";

$query = (is_array($querys) && count($querys) > 0) ? implode("&", $querys) : "";
$query_page = (is_array($querys_page) && count($querys_page) > 0) ? implode("&", $querys_page) : "";

// 전체 데이터수 구하기
// $sql_total = "select * from rb_product_view_history as ph 
// 							left join rb_product as pd on pd.pd_idx = ph.pd_idx 
// 							where pd.pd_use = '1' $search_query";
$sql_total = "SELECT * FROM rb_product_view_history AS ph
							LEFT JOIN rb_product AS pd ON pd.pd_idx = ph.pd_idx
							WHERE IF( ph.ph_type = 1, (pd.pd_img_url, pd.pd_img_num) 
							IN (
								SELECT pd2.pd_img_url, MIN( pd2.pd_img_num ) AS pd_img_num
								FROM rb_product_view_history AS ph2
								LEFT JOIN rb_product AS pd2 ON pd2.pd_idx = ph2.pd_idx
								WHERE pd2.pd_use = 1 AND IF( pd2.pd_buy_idx = 0, pd2.pd_buy_idx = 0, pd2.pd_img_num = pd2.pd_img_cnt)
								GROUP BY pd2.pd_img_url
							), 1)
						";
$total = sql_total($sql_total);


//$total = 2367;
//$page = 46;
// 페이징 만들기 시작
$arr = array('total' => $total,
             'page' => $page,
             'row' => 10,
             'scale' => 5,
             'center' => $_cfg['admin_paging_center'],
			 'link' => $query_page,
			 'page_name' => ""
        );

try {$paging = C::paging($arr); }
catch (Exception $e) {
    print 'LINE: '.$e->getLine().' '
                  .C::get_errmsg($e->getmessage());
    exit;
}
$tpl->assign($paging);
$tpl->assign('paging_data', $paging);

// 페이징 만들기 끝

if($total){
	$limit_query = " limit ".$paging['query']->limit." offset ".$paging['query']->offset;

	// $sql = "select pd.pd_use, pd.pd_upload_type, pd.pd_name, pd.pd_price, ph.* from rb_product_view_history as ph 
	// 				left join rb_product as pd on pd.pd_idx = ph.pd_idx 
	// 				where pd.pd_use = '1' $search_query $order_query $limit_query";
	$sql = "SELECT * FROM rb_product_view_history AS ph
					LEFT JOIN rb_product AS pd ON pd.pd_idx = ph.pd_idx
					WHERE IF( ph.ph_type = 1, (pd.pd_img_url, pd.pd_img_num) 
					IN (
						SELECT pd2.pd_img_url, MIN( pd2.pd_img_num ) AS pd_img_num
						FROM rb_product_view_history AS ph2
						LEFT JOIN rb_product AS pd2 ON pd2.pd_idx = ph2.pd_idx
						WHERE pd2.pd_use = 1 AND IF( pd2.pd_buy_idx = 0, pd2.pd_buy_idx = 0, pd2.pd_img_num = pd2.pd_img_cnt)
						GROUP BY pd2.pd_img_url
					), 1)
					$order_query $limit_query
				";
	$data = sql_list($sql);


	if (count($data) > 0) {
		foreach ($data as $key => $value) {
			//파일정보
			// $pd_file_data = sql_list("select * from rb_product_file where pd_idx = '".$value['pd_idx']."' order by fi_num asc");
			// $data[$key]['fi_name'] = $pd_file_data[0]['fi_name'];

			//이미지 확대보기 위한 추가 사항
			// if ($value['pd_upload_type'] == 1) {
			// 	$info = getimagesize($_SERVER['DOCUMENT_ROOT'].$_cfg['data_dir']."/files/".$data[$key]['fi_name']);
			// 	$data[$key]['img_w'] = $info[0];
			// 	$data[$key]['img_h'] = $info[1];
			// }

			$pd_price_arr = explode('.', $value['pd_price']);
			$data[$key]['pd_price_1'] = $pd_price_arr[0];
			$data[$key]['pd_price_2'] = $pd_price_arr[1];

			//구매자 정보가져오기
			if($value['ph_type'] == 2) {
				$mb = get_member2($value['mb_idx']);
				$data[$key]['mb_nick'] = $mb['mb_nick'];
				$data[$key]['mb_img1'] = $mb['mb_img1'];
			}

			if($value['od_idx']) {
				$sql = "select * from rb_order where od_idx = '".$value['od_idx']."' ";
				$data_od = sql_fetch($sql);
				if ($data_od['od_idx']) {
					$data[$key]['od_paymethod'] = $data_od['od_paymethod'];
					$data[$key]['od_price_view'] = $data_od['total_pay_amount'];
				}
			}

			
		}
	}



	$tpl->assign('data', $data); 
}

// p_arr($data);exit;

include "../inc/_head.php";
$tpl->print_('body');
include "../inc/_tail.php";
?>