<?php
include "../inc/_common.php";

set_cookie('user_id_auto', '', 3600*24*365);
set_cookie('user_id_pass', '', 3600*24*365);


if($_SESSION['chk_user_id']){
	unset($_SESSION['chk_user_id']);
	?>
	<script>
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

goto_url("/");
?>