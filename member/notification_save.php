<?php
include "../inc/_common.php";
include "../inc/_head.php";

goto_login();

if ($mode == 'del_one') {
	$sql = "delete from rb_alarm_list where al_idx = '".$al_idx."' and mb_idx = '".$member['mb_idx']."' ";
	sql_query($sql);
} else if ($mode == 'del_all') {
	$sql = "delete from rb_alarm_list where mb_idx = '".$member['mb_idx']."' ";
	sql_query($sql);
}

goto_url("/member/notification_list.php");
include "../inc/_tail.php";
?>