<?php
include "../inc/_common.php";

// p_arr($_POST); exit;

$_POST['mb_si_idx'] = substr($_POST['mb_bcode'], 0, 5);
$_POST['mb_sd_idx'] = substr($_POST['mb_bcode'], 0, 2);
$_POST['mb_hp'] = split_tel_number($_POST['mb_hp']);

$sql_common = "
	mb_name = '".$_POST['mb_nick']."',
	mb_nick = '".$_POST['mb_nick']."',
	mb_email = '".$_POST['mb_id']."',
	mb_n_telnum = '".$_POST['mb_n_telnum']."',
	mb_hp = '".$_POST['mb_hp']."',
	mb_zip = '".$_POST['mb_zip']."',
	mb_addr1 = '".$_POST['mb_addr1']."',
	mb_addr2 = '".$_POST['mb_addr2']."',

	mb_sido = '".$_POST['mb_sido']."',
	mb_sigungu = '".$_POST['mb_sigungu']."',
	mb_dong = '".$_POST['mb_dong']."',
	mb_x_pos = '".$_POST['mb_x_pos']."',
	mb_y_pos = '".$_POST['mb_y_pos']."',
	mb_bcode = '".$_POST['mb_bcode']."',
	mb_si_idx = '".$_POST['mb_si_idx']."',
	mb_sd_idx = '".$_POST['mb_sd_idx']."',

	mb_sms_y = '".$_POST['mb_sms_y']."',
	mb_mail_y = '".$_POST['mb_mail_y']."',

	mb_com_name = '".$_POST['mb_com_name']."'
	
";

//p_arr($_POST);echo "ok";exit;
//이미지체크
// $field_arr = array("mb_img1", "mb_img2");
// foreach($field_arr as $k => $v){
// 	if($user_agent == "app"){
// 		if($_POST[$v]){
// 			$timg = @getimagesize($_cfg['web_home']."/data/tmp/".$_POST[$v]);
// 			if($timg[2] != 1 && $timg[2] != 2 && $timg[2] != 3) alert("jpg, gif, png 파일만 업로드 가능합니다.");
// 		}
// 	}else{
// 		if($_FILES[$v]['tmp_name']){
// 			$timg = @getimagesize($_FILES[$v]['tmp_name']);
// 			if($timg[2] != 1 && $timg[2] != 2 && $timg[2] != 3) alert("jpg, gif, png 파일만 업로드 가능합니다.");
// 		}
// 	}
// }

if($_POST['mode'] == "insert"){
	include "../inc/_head.php";
	already_logged();
	$_POST['mb_level'] = 1;

	$hp_chk = sql_fetch("select * from rb_member where mb_id = '".$_POST['mb_id']."' ");
	if($hp_chk['mb_idx']){
		alert("The ID that already exists.");
	}


	$sql = "insert into rb_member set
				$sql_common,
				mb_id = '".$mb_id."',
				mb_level = '".$_POST['mb_level']."',
				mb_pass = sha2('".$_POST['mb_pass']."', 256),
				mb_regdate = now()
			";
	$sql_q = sql_query($sql);
	$mb_idx = sql_insert_id();


	// //이미지저장
	// foreach($field_arr as $k => $v){
	// 	if($user_agent == "app"){
	// 		if($_POST[$v]){
	// 			$src = $_cfg['web_home']."/data/tmp/".$_POST[$v];
	// 			$ext = strtolower(get_file_ext($_POST[$v]));
	// 			$tgt_name = md5(uniqid(rand(), TRUE)).".".$ext;
	// 			$tgt_name = preg_replace("/\.(php|phtm|htm|cgi|pl|exe|jsp|asp|inc)/i", "$0-x", $tgt_name);
	// 			$org_name = $_POST[$v."_org"];
	// 			$tgt = $_cfg['web_home'].$_cfg['data_dir']."/files/".$tgt_name;

	// 			Chk_exif_WH2($src, $tgt);

	// 			sql_query("update rb_member set $v = '$tgt_name', {$v}_org = '$org_name' where mb_idx = '$mb_idx'");

	// 		}
	// 	}else{
	// 		if($_FILES[$v]['tmp_name']){
	// 			$src = $_FILES[$v]['tmp_name'];
	// 			$ext = strtolower(get_file_ext($_FILES[$v]['name']));
	// 			$tgt_name = md5(uniqid(rand(), TRUE)).".".$ext;
	// 			$tgt_name = preg_replace("/\.(php|phtm|htm|cgi|pl|exe|jsp|asp|inc)/i", "$0-x", $tgt_name);
	// 			$org_name = $_FILES[$v]['name'];
	// 			$tgt = $_cfg['web_home'].$_cfg['data_dir']."/files/".$tgt_name;

	// 			Chk_exif_WH($src, $tgt);

	// 			sql_query("update rb_member set $v = '$tgt_name', {$v}_org = '$org_name' where mb_idx = '$mb_idx'");

	// 		}
	// 	}
	// }


	//파일저장 => 멀티업로드 버전
	$num = 0;
	for($i=0;$i<$_POST['file_cnt1_total'];$i++){
		
			if($_POST["mb_img1_" . $i]){
				$src = $_cfg['web_home']."/data/tmp/".$_POST["mb_img1_" . $i];
				$ext = strtolower(get_file_ext($_POST["mb_img1_" . $i]));
				$tgt_name = md5(uniqid(rand(), TRUE)).".".$ext;
				$tgt_name = preg_replace("/\.(php|phtm|htm|cgi|pl|exe|jsp|asp|inc)/i", "$0-x", $tgt_name);
				$org_name = $_POST["mb_img1_" . $i."_org"];
				$tgt = $_cfg['web_home'].$_cfg['data_dir']."/files/".$tgt_name;
				$fi_size = filesize($src);

				Chk_exif_WH2($src, $tgt);
				
				$thumb = $_cfg['web_home'].$_cfg['data_dir']."/files/thumb_".$tgt_name;
				put_gdimage($tgt, 240, 0, $thumb);
				

				sql_query("update rb_member set mb_img1 = '$tgt_name', mb_img1_org = '$org_name' where mb_idx = '$mb_idx'");

				// sql_query("insert into rb_board_file set fi_num = '$num', bd_idx = '$bd_idx', fi_name = '$tgt_name', fi_name_org = '$org_name', fi_size = '$fi_size', fi_regdate = now() ");
				$num++;
			}
		
	}

	unset($_SESSION['ss_idx']);
	$chk2 = sql_fetch("select * from rb_sns where mb_id = '".$mb_id."'");
	if($_POST['ss_idx'] && !$chk2['ss_idx']){
		sql_query("update rb_sns set mb_id = '".$mb_id."' where ss_idx = '".$_POST['ss_idx']."'");
	}

	$mb = get_member($mb_id);


	set_cookie('user_id_auto', $mb['mb_id'], 3600*24*365);
	set_cookie('user_id_pass', $mb['mb_pass'], 3600*24*365);
	$_SESSION['ss_mb_idx'] = $mb['mb_idx'];
	$_SESSION['ss_mb_id'] = $mb['mb_id'];
	$_SESSION['ss_mb_nick'] = $mb['mb_nick'];
	$_SESSION['ss_mb_level'] = $mb['mb_level'];

	sql_query("update rb_member set mb_lastlogin = mb_nowlogin where mb_id = '".$mb['mb_id']."'");
	sql_query("update rb_member set mb_nowlogin = now() where mb_id = '".$mb['mb_id']."'");

	sql_query("insert into rb_login_history set mb_id = '".$mb['mb_id']."', lh_regdate = now(), lh_year = '".date("Y")."', lh_month = '".date("n")."', lh_day = '".date("j")."'");


	if($user_agent == "app"){

		if($user_br == "And"){
		?>
		<script language='JavaScript'>
			window.lusoft.hybridGetMbid('<?=$mb['mb_id']?>');
		</script>
		<?
		}else if($user_br == "iOS"){
		?>
		<script language='JavaScript'>
			var sendObjectMessage_obj = {
					mb_id: '<?=$mb['mb_id']?>'
				}
			window.webkit.messageHandlers.hybridGetMbid.postMessage(JSON.stringify(sendObjectMessage_obj));
		</script>
		<?
		}

	}

	
	goto_url("/member/member_result.php");
	
}else if($_POST['mode'] == "update"){
	include "../inc/_head.php";
	goto_login();

	$pw_chk = sql_fetch("select * from rb_member where mb_id = '".$mb_id."' and mb_pass = sha2('".$_POST['mb_pass']."', 256)");
	if(!$pw_chk['mb_idx']){
		alert("The password is incorrect");
	}

	// $hp_chk = sql_fetch("select * from rb_member where mb_hp = '".$_POST['mb_hp']."' and mb_n_telnum = '".$_POST['mb_n_telnum']."' and mb_status = 1 and mb_idx != '".$member['mb_idx']."' ");
	// if($hp_chk['mb_idx']){
	// 	alert($_lang['member']['text_0788']);
	// }

	$mb_idx = $pw_chk['mb_idx'];

	if($_POST['mb_pass_new']){
		$sql_common .= " , mb_pass = sha2('".$_POST['mb_pass_new']."', 256) ";
	}

	$sql = "update rb_member set
				$sql_common
				
				where mb_idx = '".$mb_idx."'
			";
	$sql_q = sql_query($sql);


	// //파일삭제
	// foreach($field_arr as $k => $v){
	// 	if($_POST[$v."_del"] == 1){
	// 		sql_query("update rb_member set $v = '', {$v}_org = '' where mb_idx = '$mb_idx'");
	// 		@unlink($_cfg['web_home'].$_cfg['data_dir']."/files/".$member[$v]);
	// 	}
	// }

	//파일삭제 => 멀티업로드
	for($i=0;$i<$_POST['file_cnt1_total'];$i++){
		if($_POST["mb_img1_".$i."_del"] == 1 && $_POST["fi_idx1_".$i]){
			sql_query("update rb_member set mb_img1 = '', mb_img1_org = '' where mb_idx = '$mb_idx'");
			@unlink($_cfg['web_home'].$_cfg['data_dir']."/files/".$member['mb_img1']);
		}
	}

	// //이미지저장
	// foreach($field_arr as $k => $v){
	// 	if($user_agent == "app"){
	// 		if($_POST[$v]){
	// 			$src = $_cfg['web_home']."/data/tmp/".$_POST[$v];
	// 			$ext = strtolower(get_file_ext($_POST[$v]));
	// 			$tgt_name = md5(uniqid(rand(), TRUE)).".".$ext;
	// 			$tgt_name = preg_replace("/\.(php|phtm|htm|cgi|pl|exe|jsp|asp|inc)/i", "$0-x", $tgt_name);
	// 			$org_name = $_POST[$v."_org"];
	// 			$tgt = $_cfg['web_home'].$_cfg['data_dir']."/files/".$tgt_name;

	// 			Chk_exif_WH2($src, $tgt);

	// 			sql_query("update rb_member set $v = '$tgt_name', {$v}_org = '$org_name' where mb_idx = '$mb_idx'");

	// 		}
	// 	}else{
	// 		if($_FILES[$v]['tmp_name']){
	// 			$src = $_FILES[$v]['tmp_name'];
	// 			$ext = strtolower(get_file_ext($_FILES[$v]['name']));
	// 			$tgt_name = md5(uniqid(rand(), TRUE)).".".$ext;
	// 			$tgt_name = preg_replace("/\.(php|phtm|htm|cgi|pl|exe|jsp|asp|inc)/i", "$0-x", $tgt_name);
	// 			$org_name = $_FILES[$v]['name'];
	// 			$tgt = $_cfg['web_home'].$_cfg['data_dir']."/files/".$tgt_name;

	// 			Chk_exif_WH($src, $tgt);

	// 			sql_query("update rb_member set $v = '$tgt_name', {$v}_org = '$org_name' where mb_idx = '$mb_idx'");

	// 		}
	// 	}
	// }

	//파일저장 => 멀티업로드 버전
	$fi_num = 0;
	for($i=0;$i<$_POST['file_cnt1_total'];$i++){

			if($_POST["mb_img1_" . $i]){

				if($_POST["mb_img1_".$i."_del"] == 1 && $_POST["fi_idx1_".$i]){
					sql_query("update rb_member set mb_img1 = '', mb_img1_org = '' where mb_idx = '$mb_idx'");
					@unlink($_cfg['web_home'].$_cfg['data_dir']."/files/".$member['mb_img1']);

					// $f_data = sql_fetch("select * from rb_board_file where fi_idx = '".$_POST["fi_idx1_".$i]."'");
					// sql_query("delete from rb_board_file where fi_idx = '".$_POST["fi_idx_".$i]."'");
					// @unlink($_cfg['web_home'].$_cfg['data_dir']."/files/".$f_data[fi_name]);
					// @unlink($_cfg['web_home'].$_cfg['data_dir']."/files/thumb_".$f_data[fi_name]);
				}

				$src = $_cfg['web_home']."/data/tmp/".$_POST["mb_img1_" . $i];
				$ext = strtolower(get_file_ext($_POST["mb_img1_" . $i]));
				$tgt_name = md5(uniqid(rand(), TRUE)).".".$ext;
				$tgt_name = preg_replace("/\.(php|phtm|htm|cgi|pl|exe|jsp|asp|inc)/i", "$0-x", $tgt_name);
				$org_name = $_POST["mb_img1_" . $i."_org"];
				$tgt = $_cfg['web_home'].$_cfg['data_dir']."/files/".$tgt_name;
				$fi_size = filesize($src);

				Chk_exif_WH2($src, $tgt);
				$thumb = $_cfg['web_home'].$_cfg['data_dir']."/files/thumb_".$tgt_name;
				put_gdimage($tgt, 240, 0, $thumb);

				sql_query("update rb_member set mb_img1 = '$tgt_name', mb_img1_org = '$org_name' where mb_idx = '$mb_idx'");

				// sql_query("insert into rb_board_file set fi_num = '$fi_num', bd_idx = '$bd_idx', fi_name = '$tgt_name', fi_name_org = '$org_name', fi_size = '$fi_size', fi_regdate = now() ");
				$fi_num++;
			}else{
				if($_POST["fi_idx1_".$i]){
					// $f_data = sql_fetch("select * from rb_board_file where fi_idx = '".$_POST["fi_idx1_".$i]."'");
					// if($f_data[fi_idx]){
					// 	sql_query("update rb_board_file set fi_num = '$fi_num' where fi_idx = '".$_POST["fi_idx_".$i]."'");
					// 	$fi_num++;
					// }
				}
			}
	}

	alert("Modified", "/");
}else if($_POST['mode'] == "out"){
	goto_login();

	$pw_chk = sql_fetch("select * from rb_member where mb_id = '".$member['mb_id']."' and mb_pass = sha2('".$_POST['mb_pass']."', 256) ");
	if(!$pw_chk['mb_idx']){
		alert("The password is incorrect");
	}


	$sql = "update rb_member set
				mb_status = 3 , mb_out_reason = '$mb_out_reason' , mb_outdate = now()

				
				where mb_id = '".$member['mb_id']."'
			";
	$sql_q = sql_query($sql);

	alert("Withdrawed", "/member/logout_process.php");

	set_cookie('user_id_auto', '', 3600*24*365);
	set_cookie('user_id_pass', '', 3600*24*365);
	include "../inc/_head.php";


	if($_SESSION['chk_user_id']){
		unset($_SESSION['chk_user_id']);
		?>
		<script>
			alert("Withdrawed");
			self.close();
		</script>
		<?
		exit;
	}else{
		unset($_SESSION['ss_mb_idx']);
		unset($_SESSION['ss_mb_id']);
		unset($_SESSION['ss_mb_name']);
		unset($_SESSION['ss_mb_level']);
	}

	alert("Withdrawed", "/");

}
	
//p_arr($_POST);echo "error";exit;

alert("wrong approach", "/");

include "../inc/_tail.php";
?>