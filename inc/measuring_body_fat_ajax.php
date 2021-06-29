<?
include "../_inc/_common.php";
header("Content-Type: application/json;charset=utf-8");

$_is_ajax = false;
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
	&& strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ){
		$_is_ajax = true;
}

if ($_is_ajax != true) {
	$arr = array();
	$arr['result'] = "error";
	$arr['msg'] = "".$_lang['inc']['text_0696']."";
	echo json_encode($arr);exit;
}

$sql_inc = "insert into rb_body_fat set 
						mb_idx = '".$member['mb_idx']."',
						mb_id = '".$member['mb_id']."',
						weight = '".$weight."', 
						BMI = '".$BMI."', 
						body_fat_rate = '".$body_fat_rate."', 
						subcutaneous_fat = '".$subcutaneous_fat."', 
						visceral_fat = '".$visceral_fat."', 
						body_water_rate = '".$body_water_rate."', 
						muscle_rate = '".$muscle_rate."', 
						bone_mass = '".$bone_mass."', 
						BMR = '".$BMR."', 
						body_type = '".$body_type."', 
						protein = '".$protein."',
						muscle_mass = '".$muscle_mass."',
						metabolic_age = '".$metabolic_age."',
						heart_rate = '".$heart_rate."',
						bf_regdate = now()
					";
$sql_q = sql_query($sql_inc);
$bf_idx = sql_insert_id();



// $chk = sql_fetch("select * from rb_cate1 as c1 where c1.c1_idx = '$c1_idx' ");
// if(!$chk['c1_idx']){
// 	$arr = array();
// 	$arr['result'] = "error";
// 	$arr['msg'] = "없는 카테고리입니다.";
// 	echo json_encode($arr);exit;
// }

// $sql = "select * from rb_cate2 as c2 where c1_idx = '$c1_idx' order by c2.c2_sort asc";
// $data = sql_list($sql);

$arr = array();
$arr['result'] = "success";
$arr['idx'] = $bf_idx;
$arr['datas_cnt'] = count($_POST);
echo json_encode($arr);exit;
?>