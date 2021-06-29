<?php
$menu_code = "400100";
$menu_mode = "w";

include "../inc/_common.php";
include "../inc/_head.php";
include "../inc/_check.php";

$product_config = $_cfg['product_config'];


$query = $_POST['query'];

//echo $sql_common;exit;
// 파일검사
for($i=0;$i<$_POST['file_cnt'];$i++){
	if($_FILES["pd_file_" . $i]['tmp_name']){
		$type_arr = explode('/', $_FILES["pd_file_" . $i]['type']);
		if ($type_arr[0] == 'image') {
			$_POST['pd_upload_type'] = 1;
			$timg = @getimagesize($_FILES["pd_file_" . $i]['tmp_name']);
			if($timg[2] != 1 && $timg[2] != 2 && $timg[2] != 3) alert("jpg, gif, png 파일만 업로드 가능합니다.");
		} else if ($type_arr[0] == 'video') {
			$_POST['pd_upload_type'] = 2;
		}

	}
}

//이미지체크
$field_arr = array("pd_sell_img");
foreach($field_arr as $k => $v){
	if($_FILES[$v]['tmp_name']){
		$timg = @getimagesize($_FILES[$v]['tmp_name']);
		if($timg[2] != 1 && $timg[2] != 2 && $timg[2] != 3) alert("jpg, gif, png 파일만 업로드 가능합니다.");
	}
}

$_POST['pd_tag'] = str_replace(" ", "", $_POST['pd_tag']);

$sql_common = "
	pd_type = '".$_POST['pd_type']."',
	pd_name = '".$_POST['pd_name']."',
	pd_exp = '".$_POST['pd_exp']."',
	c1_idx = '".$_POST['c1_idx']."',
	c2_idx = '".$_POST['c2_idx']."',
	pd_code = '".$_POST['pd_code']."',
	pd_sell_name = '".$_POST['pd_sell_name']."',
	pd_sell_link = '".$_POST['pd_sell_link']."',
	pd_upload_type = '".$_POST['pd_upload_type']."',
	pd_tag = '".$_POST['pd_tag']."',
	pd_price = '".$_POST['pd_price']."',
	pd_use = '".$_POST['pd_use']."',

	pd_origin = '".$_POST['pd_origin']."',

	pd_img_num = '".$_POST['pd_img_num']."',
	pd_img_cnt = '".$_POST['pd_img_cnt']."',

	pd_option1_name = '".$_POST['pd_option1_name']."',
	pd_option2_name = '".$_POST['pd_option2_name']."',
	pd_option1 = '".$_POST['pd_option1']."',
	pd_option2 = '".$_POST['pd_option2']."',

	pd_delivery_type = '".$_POST['pd_delivery_type']."',
	pd_delivery_type_cnt = '".$_POST['pd_delivery_type_cnt']."',
	pd_delivery_type2 = '".$_POST['pd_delivery_type2']."',
	pd_delivery_free_amount = '".$_POST['pd_delivery_free_amount']."',
	pd_delivery_amount = '".$_POST['pd_delivery_amount']."',
	pd_contents = '".$_POST['pd_contents']."'

";


if($_POST['mode'] == "insert"){

	$sql = "insert into rb_product set
				$sql_common,
				pd_regdate = now()
				
			";
	$sql_q = sql_query($sql);
	$pd_idx = sql_insert_id();

	//파일저장
	for($i=0;$i<$_POST['file_cnt'];$i++){
		if($_FILES["pd_file_" . $i]['tmp_name']){
			$src = $_FILES["pd_file_" . $i]['tmp_name'];
			$ext = strtolower(get_file_ext($_FILES["pd_file_" . $i]['name']));
			$tgt_name = md5(uniqid(rand(), TRUE)).".".$ext;
			$org_name = $_FILES["pd_file_" . $i]['name'];
			$tgt = $_cfg['web_home'].$_cfg['data_dir']."/files/".$tgt_name;
			$fi_size = filesize($src);

			Chk_exif_WH($src, $tgt);
			if ($_POST['pd_upload_type'] == 1) {
				$thumb = $_cfg['web_home'].$_cfg['data_dir']."/files/thumb_".$tgt_name;
				put_gdimage($tgt, $product_config['thumb2_size'], 0, $thumb);	
			}
			// $thumb1 = $_cfg['web_home'].$_cfg['data_dir']."/files/thumb1_".$tgt_name;
			// put_gdimage($tgt, $product_config['thumb1_size'], 0, $thumb1);

			// $thumb2 = $_cfg['web_home'].$_cfg['data_dir']."/files/thumb2_".$tgt_name;
			// put_gdimage($tgt, $product_config['thumb2_size'], 0, $thumb2);

			sql_query("insert into rb_product_file set fi_num = '$i', pd_idx = '$pd_idx', fi_name = '$tgt_name', fi_name_org = '$org_name', fi_size = '$fi_size', fi_regdate = now() ");

		}
	}

	//파일카운트
	$pd_file_cnt = sql_total("select * from rb_product_file where pd_idx = '$pd_idx'");
	sql_query("update rb_product set pd_file_cnt = '$pd_file_cnt' where pd_idx = '$pd_idx'");


	//파일저장
	foreach($field_arr as $k => $v){
		if($_FILES[$v]['tmp_name']){
			$src = $_FILES[$v]['tmp_name'];
			$ext = get_file_ext($_FILES[$v]['name']);
			$tgt_name = md5(uniqid(rand(), TRUE)).".".$ext;
			$org_name = $_FILES[$v]['name'];
			$tgt = $_cfg['web_home'].$_cfg['data_dir']."/files/".$tgt_name;

			Chk_exif_WH($src, $tgt);

			$thumb = $_cfg['web_home'].$_cfg['data_dir']."/files/thumb_".$tgt_name;
			put_gdimage($tgt, $product_config['thumb2_size'], 0, $thumb);

			// $thumb1 = $_cfg['web_home'].$_cfg['data_dir']."/files/thumb1_".$tgt_name;
			// put_gdimage($tgt, $product_config['thumb1_size'], 0, $thumb1);

			// $thumb2 = $_cfg['web_home'].$_cfg['data_dir']."/files/thumb2_".$tgt_name;
			// put_gdimage($tgt, $product_config['thumb2_size'], 0, $thumb2);

			sql_query("update rb_product set $v = '$tgt_name', {$v}_org = '$org_name' where pd_idx = '$pd_idx'");

		}
	}


	

	//연관상품
	// $product_relation = $_POST['product_relation'];
	// $product_relation_arr = ($product_relation != "") ? explode('|;|', $product_relation) : array();
	// for($i=0;$i<custom_count($product_relation_arr);$i++){
	// 	$p_info = ($product_relation_arr[$i] != "") ? explode('|:|', $product_relation_arr[$i]) : array();
	// 	sql_query("insert into rb_product_relation set parent_idx = '$pd_idx', pd_idx = '".$p_info[0]."', pr_order = '".($i+1)."'");
	// }		


	//옵션
	$data = sql_fetch("select p.* from rb_product as p where p.pd_idx = '$pd_idx'");
	$option = array();
	$chk_option = array();
	$option1 = make_product_option_value($data['pd_option1']);
	$option2 = make_product_option_value($data['pd_option2']);
	// $stock_data = sql_list("select * from rb_product_stock where pd_idx = '$pd_idx'");
	if(custom_count($option1) > 0 && custom_count($option2) == 0){
		foreach($option1 as $row1){
			$ps_option = $row1['o_name'];
			$temp = array();
			$temp['ps_option'] = $ps_option;
			$option[] = $temp;
			$chk_option[] = $pd_idx."|;|".$ps_option;
		}
	}else if(custom_count($option1) == 0 && custom_count($option2) > 0){
		foreach($option2 as $row2){
			$ps_option = $row2['o_name'];
			$temp = array();
			$temp['ps_option'] = $ps_option;
			$option[] = $temp;
			$chk_option[] = $pd_idx."|;|".$ps_option;
		}
	}else if(custom_count($option1) > 0 && custom_count($option2) > 0){
		foreach($option1 as $row1){
			foreach($option2 as $row2){
				$ps_option = $row1['o_name'].":".$row2['o_name'];
				$temp = array();
				$temp['ps_option'] = $ps_option;
				$option[] = $temp;
				$chk_option[] = $pd_idx."|;|".$ps_option;
			}
		}
	}else{
		$ps_option = "";
		$temp = array();
		$temp['ps_option'] = $ps_option;
		$option[] = $temp;
		$chk_option[] = $pd_idx."|;|".$ps_option;
	}

	// //사라진것 삭제
	// foreach($stock_data as $row){
	// 	$chk_txt = $row['pd_idx']."|;|".$row['ps_option'];
	// 	if(!in_array($chk_txt, $chk_option)){
	// 		sql_query("delete from rb_product_stock where ps_idx = '".$row['ps_idx']."'");
	// 	}
	// }

	// //추가
	// foreach($option as $row){
		
	// 	// 2019.12.30 허정진 임시수정
	// 	$ct_option_tmp = ("-"===$row['ps_option']) ? "" : $row['ps_option'];
	// 	$chk = sql_fetch("select * from rb_product_stock where pd_idx = '$pd_idx' and ps_option = '".$ct_option_tmp."'");
	// 	if(!$chk['ps_idx']){
	// 		sql_query("insert into rb_product_stock set pd_idx = '$pd_idx', ps_option = '".$row['ps_option']."' ");
	// 	}
	// }

	//recent등록
	$sql_his = "insert into rb_product_view_history set 
							ph_type = 1,
							pd_idx = '".$pd_idx."',
							ph_regdate = now()
						";
	sql_query($sql_his);

	alert("상품이 추가되었습니다.", "./product_list.php?$query");
}else if($_POST['mode'] == "update"){

	$data = sql_fetch("select * from rb_product as b where b.pd_idx = '$pd_idx' $search_query");
	if(!$data['pd_idx']) alert("없는 상품입니다.");


	//파일삭제
	foreach($field_arr as $k => $v){
		if($_FILES[$v]['tmp_name']){
			sql_query("update rb_product set $v = '', {$v}_org = '' where pd_idx = '$pd_idx'");
			@unlink($_cfg['web_home'].$_cfg['data_dir']."/files/".$data[$v]);
			@unlink($_cfg['web_home'].$_cfg['data_dir']."/files/thumb_".$data[$v]);
			// @unlink($_cfg['web_home'].$_cfg['data_dir']."/files/thumb1_".$data[$v]);
			// @unlink($_cfg['web_home'].$_cfg['data_dir']."/files/thumb2_".$data[$v]);
		}
	}

	//파일삭제
	for($i=0;$i<$_POST['file_cnt'];$i++){
		if($_POST["pd_file_del_".$i] == 1 && $_POST["fi_idx_".$i]){
			$f_data = sql_fetch("select * from rb_product_file where fi_idx = '".$_POST["fi_idx_".$i]."'");
			sql_query("delete from rb_product_file where fi_idx = '".$_POST["fi_idx_".$i]."'");
			@unlink($_cfg['web_home'].$_cfg['data_dir']."/files/".$f_data['fi_name']);
			@unlink($_cfg['web_home'].$_cfg['data_dir']."/files/thumb_".$f_data['fi_name']);
			// @unlink($_cfg['web_home'].$_cfg['data_dir']."/files/thumb1_".$f_data['fi_name']);
			// @unlink($_cfg['web_home'].$_cfg['data_dir']."/files/thumb2_".$f_data['fi_name']);
		}
	}

	$sql = "update rb_product set
				$sql_common
				where pd_idx = '".$_POST['pd_idx']."'
			";
	$sql_q = sql_query($sql);
	$pd_idx = $_POST['pd_idx'];

	//파일저장
	$fi_num = 0;
	for($i=0;$i<$_POST['file_cnt'];$i++){
		if($_FILES["pd_file_" . $i]['tmp_name']){

			if($_POST["fi_idx_".$i]){
				$f_data = sql_fetch("select * from rb_product_file where fi_idx = '".$_POST["fi_idx_".$i]."'");
				sql_query("delete from rb_product_file where fi_idx = '".$_POST["fi_idx_".$i]."'");
				@unlink($_cfg['web_home'].$_cfg['data_dir']."/files/".$f_data['fi_name']);
				@unlink($_cfg['web_home'].$_cfg['data_dir']."/files/thumb_".$f_data['fi_name']);
				// @unlink($_cfg['web_home'].$_cfg['data_dir']."/files/thumb1_".$f_data['fi_name']);
				// @unlink($_cfg['web_home'].$_cfg['data_dir']."/files/thumb2_".$f_data['fi_name']);
			}

			$src = $_FILES["pd_file_" . $i]['tmp_name'];
			$ext = strtolower(get_file_ext($_FILES["pd_file_" . $i]['name']));
			$tgt_name = md5(uniqid(rand(), TRUE)).".".$ext;
			$org_name = $_FILES["pd_file_" . $i]['name'];
			$tgt = $_cfg['web_home'].$_cfg['data_dir']."/files/".$tgt_name;
			$fi_size = filesize($src);

			Chk_exif_WH($src, $tgt);

			if ($_POST['pd_upload_type'] == 1) {
				$thumb = $_cfg['web_home'].$_cfg['data_dir']."/files/thumb_".$tgt_name;
				put_gdimage($tgt, $product_config['thumb2_size'], 0, $thumb);	
			}

			// $thumb1 = $_cfg['web_home'].$_cfg['data_dir']."/files/thumb1_".$tgt_name;
			// put_gdimage($tgt, $product_config['thumb1_size'], 0, $thumb1);

			// $thumb2 = $_cfg['web_home'].$_cfg['data_dir']."/files/thumb2_".$tgt_name;
			// put_gdimage($tgt, $product_config['thumb2_size'], 0, $thumb2);

			sql_query("insert into rb_product_file set fi_num = '$fi_num', pd_idx = '$pd_idx', fi_name = '$tgt_name', fi_name_org = '$org_name', fi_size = '$fi_size', fi_regdate = now() ");
			$fi_num++;
		}else{
			if($_POST["fi_idx_".$i]){
				$f_data = sql_fetch("select * from rb_product_file where fi_idx = '".$_POST["fi_idx_".$i]."'");
				if($f_data['fi_idx']){
					sql_query("update rb_product_file set fi_num = '$fi_num' where fi_idx = '".$_POST["fi_idx_".$i]."'");
					$fi_num++;
				}
			}
		}
	}

	//파일카운트
	$pd_file_cnt = sql_total("select * from rb_product_file where pd_idx = '$pd_idx'");
	sql_query("update rb_product set pd_file_cnt = '$pd_file_cnt' where pd_idx = '$pd_idx'");

	//파일저장
	foreach($field_arr as $k => $v){
		if($_FILES[$v]['tmp_name']){
			$src = $_FILES[$v]['tmp_name'];
			$ext = get_file_ext($_FILES[$v]['name']);
			$tgt_name = md5(uniqid(rand(), TRUE)).".".$ext;
			$org_name = $_FILES[$v]['name'];
			$tgt = $_cfg['web_home'].$_cfg['data_dir']."/files/".$tgt_name;

			Chk_exif_WH($src, $tgt);

			$thumb = $_cfg['web_home'].$_cfg['data_dir']."/files/thumb_".$tgt_name;
			put_gdimage($tgt, $product_config['thumb2_size'], 0, $thumb);	

			// $thumb1 = $_cfg['web_home'].$_cfg['data_dir']."/files/thumb1_".$tgt_name;
			// put_gdimage($tgt, $product_config['thumb1_size'], 0, $thumb1);

			// $thumb2 = $_cfg['web_home'].$_cfg['data_dir']."/files/thumb2_".$tgt_name;
			// put_gdimage($tgt, $product_config['thumb2_size'], 0, $thumb2);

			sql_query("update rb_product set $v = '$tgt_name', {$v}_org = '$org_name' where pd_idx = '$pd_idx'");

		}
	}

	

	// //연관상품
	// $product_relation = $_POST['product_relation'];
	// $product_relation_arr = ($product_relation != "") ? explode('|;|', $product_relation) : array();
	// $product_relation_data = array();
	// for($i=0;$i<custom_count($product_relation_arr);$i++){
	// 	$p_info = ($product_relation_arr[$i] != "") ? explode('|:|', $product_relation_arr[$i]) : array();
	// 	$product_relation_data[] = $p_info[0];
	// }		

	// //없어진것 삭제
	// $old_data = sql_list("select * from rb_product_relation where parent_idx = '$pd_idx'");
	// for($i=0;$i<custom_count($old_data);$i++){
	// 	if(!in_array($old_data[$i]['pd_idx'], $product_relation_data)){
	// 		sql_query("delete from rb_product_relation where pr_idx = '".$old_data[$i]['pr_idx']."'");
	// 	}
	// }

	// for($i=0;$i<custom_count($product_relation_arr);$i++){
	// 	$p_info = ($product_relation_arr[$i] != "") ? explode('|:|', $product_relation_arr[$i]) : array();
	// 	$chk = sql_fetch("select * from rb_product_relation where parent_idx = '$pd_idx' and pd_idx = '".$p_info[0]."' ");
	// 	if(!$chk['pr_idx']){
	// 		sql_query("insert into rb_product_relation set parent_idx = '$pd_idx', pd_idx = '".$p_info[0]."', pr_order = '".($i+1)."'");
	// 	}else{
	// 		sql_query("update rb_product_relation set pr_order = '".($i+1)."' where pr_idx = '".$chk['pr_idx']."'");
	// 	}
		
	// }


	//옵션
	$data = sql_fetch("select p.* from rb_product as p where p.pd_idx = '$pd_idx'");
	$option = array();
	$chk_option = array();
	$option1 = make_product_option_value($data['pd_option1']);
	$option2 = make_product_option_value($data['pd_option2']);
	// $stock_data = sql_list("select * from rb_product_stock where pd_idx = '$pd_idx'");
	if(custom_count($option1) > 0 && custom_count($option2) == 0){
		foreach($option1 as $row1){
			$ps_option = $row1['o_name'];
			$temp = array();
			$temp['ps_option'] = $ps_option;
			$option[] = $temp;
			$chk_option[] = $pd_idx."|;|".$ps_option;
		}
	}else if(custom_count($option1) == 0 && custom_count($option2) > 0){
		foreach($option2 as $row2){
			$ps_option = $row2['o_name'];
			$temp = array();
			$temp['ps_option'] = $ps_option;
			$option[] = $temp;
			$chk_option[] = $pd_idx."|;|".$ps_option;
		}
	}else if(custom_count($option1) > 0 && custom_count($option2) > 0){
		foreach($option1 as $row1){
			foreach($option2 as $row2){
				$ps_option = $row1['o_name'].":".$row2['o_name'];
				$temp = array();
				$temp['ps_option'] = $ps_option;
				$option[] = $temp;
				$chk_option[] = $pd_idx."|;|".$ps_option;
			}
		}
	}else{
		$ps_option = "";
		$temp = array();
		$temp['ps_option'] = $ps_option;
		$option[] = $temp;
		$chk_option[] = $pd_idx."|;|".$ps_option;
	}

	// //사라진것 삭제
	// foreach($stock_data as $row){
	// 	$chk_txt = $row['pd_idx']."|;|".$row['ps_option'];
	// 	if(!in_array($chk_txt, $chk_option)){
	// 		sql_query("delete from rb_product_stock where ps_idx = '".$row['ps_idx']."'");
	// 	}
	// }

	// //추가
	// foreach($option as $row){

	// 	// 2019.12.30 허정진 임시수정
	// 	$ct_option_tmp = ("-"===$row['ps_option']) ? "" : $row['ps_option'];

	// 	$chk = sql_fetch("select * from rb_product_stock where pd_idx = '$pd_idx' and ps_option = '".$ct_option_tmp."'");
	// 	if(!$chk['ps_idx']){
	// 		sql_query("insert into rb_product_stock set pd_idx = '$pd_idx', ps_option = '".$row['ps_option']."' ");
	// 	}
	// }

	
	if ($_POST['pd_use'] == 1 && $_POST['pd_type'] == 2) {
		//recent등록
		$sql_chk = "select * from rb_product_view_history where ph_type = 1 and pd_idx = '".$pd_idx."' ";
		$data_chk = sql_fetch($sql_chk);
		if ($data_chk['ph_idx']) {
			$sql_upd = "update rb_product_view_history set ph_regdate = now() where ph_idx = '".$data_chk['ph_idx']."' ";
			sql_query($sql_upd);
		} else {
			$sql_his = "insert into rb_product_view_history set 
									ph_type = 1,
									pd_idx = '".$pd_idx."',
									ph_regdate = now()
								";
			sql_query($sql_his);
		}


		//알림 등록
		$sql_alarm = "select * from rb_member where mb_status = 1 and mb_level = 1 and mb_push_sub = 1";
		$data_alarm = sql_list($sql_alarm);
		foreach ($data_alarm as $key => $value) {
			$al_contents = $data['pd_name']." product has been registered.";

			//상품등록 알림 발송
			$sql_ins = "insert into rb_alarm_list set 
									mb_idx = '".$value['mb_idx']."',
									mb_id = '".$value['mb_id']."',
									al_contents = '".addslashes($al_contents)."',
									al_regdate = now()
							";
			sql_query($sql_ins);
			$al_idx = sql_insert_id();
		}
		
	}

	


	alert("상품이 수정되었습니다.", "./product_view.php?pd_idx=".$_POST['pd_idx']."&$query");
	
}else if($_POST['mode'] == "stock"){

	$data = sql_fetch("select * from rb_product as b where b.pd_idx = '$pd_idx' $search_query");
	if(!$data['pd_idx']) alert("없는 상품입니다.");

	if(custom_count($_POST['ps_idx']) > 0){
		for($i=0;$i<custom_count($_POST['ps_idx']);$i++){
			$ps_idx = $_POST['ps_idx'][$i];
			$ps_stock = $_POST['ps_stock_'.$ps_idx];
			sql_query("update rb_product_stock set ps_stock = '$ps_stock' where ps_idx = '".$ps_idx."' ");
		}
	}

	alert("재고가 수정되었습니다.", "./product_view.php?pd_idx=".$_POST['pd_idx']."&$query");

}else if($_GET['mode'] == "delete"){

	$querys = array();
	$querys[] = "page=".$page;
	$querys[] = "sca=".$_GET['sca'];
	$querys[] = "stx=".$_GET['stx'];
	$querys[] = "c1_idx=".$_GET['c1_idx'];
	$querys[] = "c2_idx=".$_GET['c2_idx'];
	$querys[] = "c3_idx=".$_GET['c3_idx'];
	
	$query = (is_array($querys) && count($querys) > 0) ? implode("&", $querys) : "";

	//echo "select * from rb_product as b left join rb_member as m on b.mb_id = m.mb_id where b.bc_code = '$bc_code' and b.pd_idx = '$pd_idx' $search_query";exit;

	$data = sql_fetch("select * from rb_product as b where b.pd_idx = '$pd_idx' $search_query");
	if(!$data['pd_idx']) alert("없는 상품입니다.");


	delete_product_article($pd_idx);

	alert("상품이 삭제되었습니다.", "./product_list.php?$query");

}else if($_GET['mode'] == "c_delete"){

	$querys = array();
	$querys[] = "page=".$page;
	$querys[] = "sca=".$_GET['sca'];
	$querys[] = "stx=".$_GET['stx'];
	if(is_array($product_config['category'])){
		if($_GET['pd_category']){
			$querys[] = "pd_category=".$_GET['pd_category'];
		}
	}
	$query = (is_array($querys) && count($querys) > 0) ? implode("&", $querys) : "";

	$data = sql_fetch("select * from rb_product as b where b.pd_idx = '$pd_idx' $search_query");
	if(!$data['pd_idx']) alert("없는 상품입니다.");

	if(!$product_config['is_comment']) alert("잘못된 접근입니다.");

	$data2 = sql_fetch("select * from rb_product_comment where pd_idx = '$pd_idx' and cm_idx = '$cm_idx'");
	if(!$data2['cm_idx']) alert("없는 댓글입니다.");


	$sql = "delete from rb_product_comment where cm_idx = '$cm_idx'
			";
	$sql_q = sql_query($sql);

	alert("댓글이 삭제되었습니다.", "./product_view.php?pd_idx=".$data['pd_idx']."&$query");

} else if($_GET['mode'] == "buy_delete"){
	
	$querys = array();
	$querys[] = "page=".$page;
	$querys[] = "sca=".$_GET['sca'];
	$querys[] = "stx=".$_GET['stx'];
	if(is_array($product_config['category'])){
		if($_GET['pd_category']){
			$querys[] = "pd_category=".$_GET['pd_category'];
		}
	}
	$query = (is_array($querys) && count($querys) > 0) ? implode("&", $querys) : "";

	$data = sql_fetch("select * from rb_product as b where b.pd_idx = '".$pd_idx."' $search_query");
	if(!$data['pd_idx']) alert("없는 상품입니다.");

	//구매정보 삭제
	$sql_buyer = "select * from rb_product_buyer where pd_idx = '".$data['pd_idx']."' and mb_idx = '".$mb_idx."' ";
	$data_buyer = sql_fetch($sql_buyer);
	if ($data_buyer['pb_idx']) {
		$sql_del = "delete from rb_product_buyer where pb_idx = '".$data_buyer['pb_idx']."' ";
		sql_query($sql_del);

		//상품 상태값 변경
		$sql_check = "select * from rb_product_buyer where pd_idx = '".$data['pd_idx']."' order by pb_idx asc";
		$data_check = sql_fetch($sql_check);
		if ($data_check['pb_idx']) {
			$sql_upd = "update rb_product set pd_buy_idx = '".$data_check['mb_idx']."' where pd_idx = '".$data['pd_idx']."' ";
			sql_query($sql_upd);

		} else {
			$sql_upd = "update rb_product set pd_buy_idx = 0 where pd_idx = '".$data['pd_idx']."' ";
			sql_query($sql_upd);

		}

	}

	//주문 상태값 변경
	if ($data_buyer['od_idx']) {
		$sql_upd = "update rb_order set od_status = 7 where od_idx = '".$data_buyer['od_idx']."' ";
		sql_query($sql_upd);
	}


	alert("구매정보가 삭제 되었습니다.", "./product_view.php?pd_idx=".$data['pd_idx']."&$query");

} else if($_GET['mode'] == "buy_insert"){
	$sql = "select * from rb_member where mb_idx = '".$mb_idx."' and mb_status = 1 ";
	$data_mb = sql_fetch($sql);
	if (!$data_mb['mb_idx']) {
		alert("정상회원이 아닙니다.");
	}

	$sql = "select * from rb_product where pd_idx = '".$pd_idx."' and pd_use = 1 ";
	$data_pd = sql_fetch($sql);
	if ($data_pd['pd_idx']) {
		if ($data_pd['pd_buy_idx'] != 0) {
			alert("이미 판매된 상품 입니다.");
		}
	} else {
		alert("판매중인 상품이 아닙니다.");
	}

	//코인시셋 바로 가져오기
	$one_dollar = trix_coin_api();
	$trix = $data_pd['pd_price'] * $one_dollar['one_dollar'];

	//order 테이블 작성
	$od_num = date("YmdHi").substr(md5(uniqid(rand(), TRUE)), 0, 8);
	$sql_ins = "insert into rb_order set 
							od_num = '".$od_num."',  
							mb_idx = '".$data_mb['mb_idx']."',  
							mb_id = '".$data_mb['mb_id']."', 
							pd_idx = '".$pd_idx."', 
							od_tno = '', 
							od_title = '".addslashes($data_pd['pd_name'])."', 
							od_status = 4, 
							od_coin_status = 2, 
							total_amount_all = '".$data_pd['pd_price']."', 
							total_pay_amount = '".$trix."', 
							od_regdate = now()
					";
	sql_query($sql_ins);
	$od_idx = sql_insert_id();

	
	//거래완료 알림 발송
	$al_contents = "The transaction of ".$data_pd['pd_name']." goods has been completed.";
	$sql_ins = "insert into rb_alarm_list set 
							mb_idx = '".$data_mb['mb_idx']."',
							mb_id = '".$data_mb['mb_id']."',
							al_contents = '".addslashes($al_contents)."',
							al_regdate = now()
					";
	sql_query($sql_ins);
	$al_idx = sql_insert_id();


	//상품에 구매자 업데이트
	$sql_upd = "update rb_product set 
							pd_buy_idx = '".$data_mb['mb_idx']."'
							where pd_idx = '".$pd_idx."'
						";
	sql_query($sql_upd);

	//구매테이블 작성
	$sql_ins = "insert into rb_product_buyer set 
							pd_idx = '".$data_pd['pd_idx']."', 
							pd_img_url = '".$data_pd['pd_img_url']."', 
							od_idx = '".$od_idx."', 
							mb_idx = '".$data_mb['mb_idx']."', 
							mb_id = '".$data_mb['mb_id']."', 
							pb_price = '".$data_pd['pd_price']."', 
							pb_coin_hash = '', 
							pb_regdate = now()
						";
	sql_query($sql_ins);

	if ($data_pd['pd_type'] == 2) {
		//뷰 히스토리 작성
		$sql_his = "insert into rb_product_view_history set 
								ph_type = 2,
								od_idx = '".$od_idx."',
								pd_idx = '".$pd_idx."',
								mb_idx = '".$data_mb['mb_idx']."',
								mb_id = '".$data_mb['mb_id']."',
								ph_regdate = now()
							";
		sql_query($sql_his);
	}

	alert("적용되었습니다.", "/admin/product/product_view.php?pd_idx=$pd_idx");

}

alert("잘못된 접근입니다.", "./product_list.php?$query");
?>