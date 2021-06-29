<?php
include "../inc/_common.php";
include "../inc/_head.php";

goto_login();


$od_amount = get_txt_from_data($_cfg['order']['payment'], $_POST['od_pd'], 'val', 'price');

$sql_common = "
	mb_id = '".$member['mb_id']."',
	od_num = '".$_POST['od_num']."',
	od_pd = '".$_POST['od_pd']."',
	od_paymethod = '".$_POST['od_paymethod']."',
	od_ipgum_bank = '".$_POST['od_ipgum_bank']."',
	od_ipgum_name = '".$_POST['od_ipgum_name']."',
	od_ipgum_num = '".$_POST['od_ipgum_num']."',
	od_amount = '".$od_amount."'
";


$sql = "insert into rb_order set
			$sql_common,
			od_regdate = now()
		";
$sql_q = sql_query($sql);

alert("무통장입금 결제가 신청되었습니다..", "/member/order_list.php");


include "../inc/_tail.php";
?>