<?
include "../_inc/_common.php";
header("Content-Type: application/json;charset=utf-8");

if($_GET['mb_email']){

	if (!preg_match("/([0-9a-zA-Z_-]+)@([0-9a-zA-Z_-]+)\.([0-9a-zA-Z_-]+)/", $_GET['mb_email'])) {
		$arr = array();
		$arr['result'] = "error";
		$arr['mb_email'] = $_GET['mb_email'];
		$arr['msg'] = '- Email format is incorrect.';
		echo json_encode($arr);exit;
	}else{

		$sql = "select * from rb_member where mb_id = '".$_GET['mb_email']."'";
		$data = sql_fetch($sql);
		if($data['mb_idx']){
			$arr = array();
			$arr['result'] = "error";
			$arr['msg'] = '- This is a duplicate ID.';
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
	$arr['msg'] = '- Please enter your email.';
	echo json_encode($arr);exit;
}