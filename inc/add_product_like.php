<?
include "../_inc/_common.php";
header("Content-Type: application/json;charset=utf-8");

$pd_idx = $_GET['pd_idx'];

if(!$is_member){
	$arr = array();
	$arr['result'] = "error";
	$arr['msg'] = "Please use after login.";
	echo json_encode($arr);exit;
}

$chk = sql_fetch("select * from rb_product where pd_idx = '{$pd_idx}'");
if(!$chk['pd_idx']){
	$arr = array();
	$arr['result'] = "error";
	$arr['msg'] = "It is not a product.";
	echo json_encode($arr);exit;
}


$chk_like = sql_fetch("select * from rb_product_like where pd_idx = '$pd_idx' and mb_id = '".$member['mb_id']."'");
if($chk_like['li_idx']){
	sql_query("delete from rb_product_like where li_idx = '".$chk_like['li_idx']."'");
	$like_cnt = sql_total("select * from rb_product_like where pd_idx = '$pd_idx'");
	sql_query("update rb_product set pd_like_cnt = $like_cnt where pd_idx = '{$pd_idx}'");
	$arr = array();
	$arr['result'] = "success";
	$arr['like_result'] = "0";
	$arr['like_cnt'] = $like_cnt;
	$arr['msg'] = "Like was cancelled.";
	echo json_encode($arr);exit;
}else{
	sql_query("insert into rb_product_like set pd_idx = '$pd_idx', mb_id = '".$member['mb_id']."', li_regdate = now()");
	$like_cnt = sql_total("select * from rb_product_like where pd_idx = '$pd_idx'");
	sql_query("update rb_product set pd_like_cnt = $like_cnt where pd_idx = '{$pd_idx}'");
	$arr = array();
	$arr['result'] = "success";
	$arr['like_result'] = "1";
	$arr['like_cnt'] = $like_cnt;
	$arr['msg'] = "It’s good.";
	echo json_encode($arr);exit;
}
?>