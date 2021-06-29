<?php
$page_option = "bgwhite";
include "../inc/_common.php";

$t_menu = 8;
$l_menu = 1;

goto_login();

if($is_admin){
	alert($_lang['member']['text_0766'], "/index.php");
}

$tpl=new Template;
$tpl->define(array(
    'contents'  =>'member/my_info.tpl',

	'left'  =>	'inc/mypage_left.tpl',

	'body'  =>'inc/body.tpl',
	'top'  =>'inc/top.tpl',
	'quick'  => 'inc/quick.tpl',
	'bottom'  => 'inc/bottom.tpl',

	'm_form'  => 'member/m_form.tpl',
));

$tpl->assign('mode', "update");

// $session_id = session_id();
// $sql_chk = "select * from rb_certi where ce_session = '$session_id' and ce_end > '".date("Y-m-d H:i:s", strtotime("-3 minutes"))."' ";
// echo $sql_chk;
// $data_chk = sql_fetch($sql_chk);
// p_arr($data_chk); exit;

$sql = "select * from rb_country_tel";
$data_tel = sql_list($sql);
$tpl->assign('data_tel', $data_tel);

//SNS
$sns_data1 = sql_fetch("select * from rb_sns where mb_id = '".$member['mb_id']."' and ss_from = 'facebook'");
$tpl->assign('sns_data1', $sns_data1);

$sns_data2 = sql_fetch("select * from rb_sns where mb_id = '".$member['mb_id']."' and ss_from = 'kakao'");
$tpl->assign('sns_data2', $sns_data2);

$sns_data3 = sql_fetch("select * from rb_sns where mb_id = '".$member['mb_id']."' and ss_from = 'naver'");
$tpl->assign('sns_data3', $sns_data3);

include "../inc/_head.php";
$tpl->print_('body');
include "../inc/_tail.php";
?>