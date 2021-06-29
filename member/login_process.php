<?php
header("Content-type: text/html; charset=UTF-8");
include "../inc/_common.php";

$_POST['hp'] = split_tel_number($_POST['hp']);

$sql_s = "select * from rb_member where mb_id = '".$_POST['id']."' and mb_status > 0";
// $sql_s = "select * from rb_member where mb_n_telnum = '".$_POST['n_telnum']."' and mb_hp = '".$_POST['hp']."' and mb_status > 0";
$sql_t = sql_total($sql_s);
if($sql_t == 0){
	alert("Login failed.");
}else{
	$sql_r = sql_fetch($sql_s);
	if(sql_password($_POST['pw']) != $sql_r['mb_pass']){
		alert("Login failed.");
	}
}


if($sql_r['mb_certified'] != 1 && $sql_r['mb_level'] < $_cfg['subadmin_level']){
	alert("Login failed.");
}
if($sql_r['mb_status'] == 2){
	alert("Login failed.");
}

if($sql_r['mb_status'] == 3){
	alert("Login failed.");
}

if($_POST['auto_login'] == 1){
	set_cookie('user_id_auto', $sql_r['mb_id'], 3600*24*365);
	set_cookie('user_id_pass', $sql_r['mb_pass'], 3600*24*365);
}

$_SESSION['ss_mb_idx'] = $sql_r['mb_idx'];
$_SESSION['ss_mb_id'] = $sql_r['mb_id'];
$_SESSION['ss_mb_nick'] = $sql_r['mb_nick'];
$_SESSION['ss_mb_level'] = $sql_r['mb_level'];

sql_query("update rb_member set mb_lastlogin = mb_nowlogin where mb_id = '".$sql_r['mb_id']."'");
sql_query("update rb_member set mb_nowlogin = now() where mb_id = '".$sql_r['mb_id']."'");

sql_query("insert into rb_login_history set mb_id = '".$sql_r['mb_id']."', lh_regdate = now(), lh_year = '".date("Y")."', lh_month = '".date("n")."', lh_day = '".date("j")."'");

$url = (!$url) ? "/" : $url;


if($user_agent == "app"){

	if($user_br == "And"){
	?>
	<script language='JavaScript'>
		window.lusoft.hybridGetMbid('<?=$sql_r['mb_id']?>');
	</script>
	<?
	}else if($user_br == "iOS"){
	?>
	<script language='JavaScript'>
		var sendObjectMessage_obj = {
				mb_id: '<?=$sql_r['mb_id']?>'
			}
		window.webkit.messageHandlers.hybridGetMbid.postMessage(JSON.stringify(sendObjectMessage_obj));
	</script>
	<?
	}

}
goto_url($url);
?>
