<?php
$menu_num = 0;
include "../inc/_common.php";
include "../inc/_head.php";

already_logged();


$chk = sql_fetch("select * from rb_member where mb_id = '".$_POST['id']."' and mb_name = '".$_POST['name']."' ");
$_SESSION['find_idpw_id'] = $chk['mb_id'];

if($chk['mb_id']){

	$new_pass = substr(md5(uniqid(rand(), TRUE)), 0, 6);

	$mb_verify_word2 = md5(uniqid(rand(), TRUE));
	$mb_cert = md5($chk['mb_id'].$chk['mb_name'].$mb_verify_word2);

	sql_query("update rb_member set mb_new_pass = sha2('".$new_pass."', 256), mb_verify_word2 = '$mb_verify_word2' where mb_id = '".$chk['mb_id']."'");

	ob_start();
	include_once ("./mail_pass.html");
	$content = ob_get_contents();
	ob_end_clean();

	$content = str_replace("[아이디]", $chk['mb_id'], $content);
	$content = str_replace("[비밀번호]", $new_pass, $content);
	$content = str_replace("[URL]", $_cfg['url'], $content);
	$content = str_replace("[인증경로]", $_cfg['url']."/member/pass_cert.php?mb_id=".$chk['mb_id']."&mb_cert=".$mb_cert , $content);

	//구글 smtp사용 발송방법
	$rslt = sendMail($chk['mb_id'], 'lusoft.rt@gmail.com', 'admin', 'Authentication mail', $content);
	// $rslt = sendMail('landzz@gmail.com', 'lusoft.rt@gmail.com', 'admin', 'Authentication mail', $content);
	if($rslt){
		// echo "전송완료";
		// print_r($rslt);
	}else{
		// echo "전송실패";
	}

	// print_r($rslt);
	// //일반적인 메일발송방법
	// $arr_email            = array();
	// $arr_email['from']    = $chk['mb_email'];
	// $arr_email['to']      = $admin_data['mb_email'];
	// $arr_email['name']    = $admin_data['mb_name'];
	// $arr_email['title']   = "Authentication mail";
	// $arr_email['content'] = $content;
	// $arr_email['file'] = $arr_file;

	// $cls = new yskEmailClass;
	// $result = $cls->init($arr_email);
	// if($result == true) {
	//   //echo 1;
	// } else {
	//   //echo 0;
	// }
}

goto_url("/member/find_pw_result.php");


include "../inc/_tail.php";
?>