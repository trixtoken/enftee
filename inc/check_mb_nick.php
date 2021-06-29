<?
include "../_inc/_common.php";
header("Content-Type: application/json;charset=utf-8");

if($mb_nick){

	if (preg_match("/([^가-힣\x20^a-z^A-Z^0-9])/", $mb_nick)) {
		$arr = array();
		$arr['result'] = "error";
		$arr['msg'] = '- Only Korean, English, and numbers can be used.';
		echo json_encode($arr);exit;
	}else{
		if($mb_id != ""){
			$tmp_sql = " and mb_id != '$mb_id' ";
		}
		$sql = "select * from rb_member where mb_nick = '".$mb_nick."' and mb_status > 0 $tmp_sql";
		$data = sql_fetch($sql);
		if($data['mb_idx']){
			$arr = array();
			$arr['result'] = "error";
			$arr['msg'] = '- This is a duplicate nickname.';
			echo json_encode($arr);exit;

		}else{
			
			$arr = array();
			$arr['result'] = "success";
			$arr['msg'] = "";
			echo json_encode($arr);exit;
		}
	}
}else{
	$arr = array();
	$arr['result'] = "error";
	$arr['msg'] = '- Please enter your nickname.';
	echo json_encode($arr);exit;
}
?>