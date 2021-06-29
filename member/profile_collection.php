<?php
include "../inc/_common.php";
include "../inc/_head.php";

// goto_login();

$t_menu = 8;
$l_menu = 7;


$tpl=new Template;
$tpl->define(array(
	'contents'  =>'member/profile_collection.tpl',
	'left'  =>	'inc/mypage_left.tpl',

	'body'  =>'inc/body.tpl',
	'top'  =>'inc/top.tpl',
	'quick'  => 'inc/quick.tpl',
	'bottom'  => 'inc/bottom.tpl',
));

$tpl->assign('page_title', 'profile_collection');

if ($_GET['mb_idx']) {
	$sql = "select * from rb_member where mb_idx = '".$_GET['mb_idx']."' ";
	$data = sql_fetch($sql);
} else {
	if ($is_member) {
		$sql = "select * from rb_member where mb_idx = '".$member['mb_idx']."' ";
		$data = sql_fetch($sql);
	} else {
		alert("wrong approach");
	}
}
$tpl->assign('data', $data);

//최근 구매한 작품리스트 최대 3개(collection)
$sql_col = "select * from rb_product_buyer as pb
						left join rb_product as pd on pd.pd_idx = pb.pd_idx 
						where pb.mb_idx = '".$data['mb_idx']."' and pd.pd_use = 1 order by pb.pb_idx desc limit 0, 3";
$data_col = sql_list($sql_col);
$sql_col_cnt = "select count(*) as col_cnt from rb_product_buyer as pb
								left join rb_product as pd on pd.pd_idx = pb.pd_idx 
								where pb.mb_idx = '".$data['mb_idx']."' and pd.pd_use = 1 order by pb.pb_idx desc";
$data_col_cnt = sql_fetch($sql_col_cnt);
// foreach ($data_col as $key => $value) {
// 	$pd_file_data = sql_list("select * from rb_product_file where pd_idx = '".$value['pd_idx']."' order by fi_num asc");
// 	$data_col[$key]['fi_name'] = $pd_file_data[0]['fi_name'];
// }
$tpl->assign('data_col', $data_col);
$tpl->assign('data_col_cnt', $data_col_cnt['col_cnt']);

//좋아요한 작품리스트 전부(favorites)
$sql_like = "select * from rb_product_like as li
							left join rb_product as pd on pd.pd_idx = li.pd_idx 
							where li.mb_id = '".$data['mb_id']."' and pd.pd_use = 1";
$data_like = sql_list($sql_like);
// foreach ($data_like as $key => $value) {
// 	$pd_file_data = sql_list("select * from rb_product_file where pd_idx = '".$value['pd_idx']."' order by fi_num asc");
// 	$data_like[$key]['fi_name'] = $pd_file_data[0]['fi_name'];
// }
$tpl->assign('data_like', $data_like);
$tpl->assign('data_like_cnt', count($data_like));

$tpl->print_('body');
include "../inc/_tail.php";
?>