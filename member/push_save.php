<?php
include "../inc/_common.php";
include "../inc/_head.php";

goto_login();

$sql_common = "
	mb_push = '".$_POST['mb_push']."',
	mb_push_sub = '".$_POST['mb_push_sub']."'
";


	$sql = "update rb_member set
				$sql_common
				
				where mb_idx = '".$member['mb_idx']."'
			";
	$sql_q = sql_query($sql);

	// c_db2();

	// sql_query("update member set recv_on = '".$_POST['mb_push']."' , last_time =now() where tag = '$mb_id' and domain = '$sv_code' and removed = 0");


	// alert("Your settings have been saved.", "/member/push_set.php");
	goto_url("/member/push_set.php");

include "../inc/_tail.php";
?>