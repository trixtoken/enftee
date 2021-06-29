<?php
include "./_inc/_common.php";

//동일한 이미지 카운트	
$sql = "select * from rb_klip_bapp order by kb_idx desc";
$data = sql_list($sql);

foreach ($data as $key => $value) {
	$sql = "select * from rb_product where pd_card_id = '".$value['card_id']."' and pd_card_uri = '".$value['card_uri']."' ";
	$data_chk = sql_fetch($sql);

	// if ($data_chk['pd_idx']) {
	// 	$sql_upd = "update rb_product set
	// 							pd_name = '".$value['pd_name']."',
	// 							nft_id = '".$value['nft_id']."',
	// 							pd_card_uri = '".$value['card_uri']."',
	// 							pd_contents = '".$value['pd_description']."',
	// 							pd_img_url = '".$value['pd_img_url']."',
	// 							pd_img_num = '".$value['pd_img_num']."',
	// 							pd_img_cnt ='".$value['pd_img_cnt']."',
	// 							pd_update_regdate = now()
	// 							where pd_idx = '".$data_chk['pd_idx']."'
	// 						";
	// 	sql_query($sql_upd);
	// } else {
	// 	$sql_inc = "insert into rb_product set
	// 							pd_card_id = '".$value['card_id']."',
	// 							pd_name = '".$value['pd_name']."',
	// 							nft_id = '".$value['nft_id']."',
	// 							pd_card_uri = '".$value['card_uri']."',
	// 							pd_contents = '".$value['pd_description']."',
	// 							pd_upload_type = 1,
	// 							pd_img_url = '".$value['pd_img_url']."',
	// 							pd_img_num = '".$value['pd_img_num']."',
	// 							pd_img_cnt ='".$value['pd_img_cnt']."',
	// 							pd_use = 2,
	// 							pd_regdate = now()
	// 						";
	// 	sql_query($sql_inc);
	// }
}
?>