<?php
ini_set("memory_limit" , -1);
$menu_code = "300100";
$menu_mode = "w";

include "../inc/_common.php";
include "../inc/_head.php";
include "../inc/_check.php";

if($_GET[mode] == "payment" || $_GET[mode] == "cancel"){
	$querys = array();
	$querys[] = "page=".$page;
	$querys[] = "sca=".$_GET[sca];
	$querys[] = "stx=".$_GET[stx];

	$querys[] = "od_status=".$_GET[od_status];
	$querys[] = "s_start=".$_GET[s_start];
	$querys[] = "s_end=".$_GET[s_end];
	$querys[] = "date_field=".$_GET[date_field];

	$query = (is_array($querys) && count($querys) > 0) ? implode("&", $querys) : "";

	$sql = "select * from rb_order as o left join rb_member as m on o.mb_id = m.mb_id where od_idx = '$_GET[od_idx]'";
	$data = sql_fetch($sql);

	if(!$data[od_idx]){
		alert("없는 결제건입니다.");
	}
}else if($_POST[mode] == "delivery_all" || $_POST[mode] == "delivery_view"){
	$query = $_POST[query];
	$sql = "select * from rb_order as o left join rb_member as m on o.mb_id = m.mb_id where od_idx = '$_POST[od_idx]'";
	$data = sql_fetch($sql);

	if(!$data[od_idx]){
		alert("없는 결제건입니다.");
	}
}

if($_GET[mode] == "payment"){
	if($data[od_status] != 1){
		alert("이미 결제된 주문건입니다.");
	}

	$mb_id = $data[mb_id];
	$mb = get_member($mb_id);

	// 주문테이블에 적기
	sql_query("
	update rb_order set
			od_status = 2,
			od_paydate = now()
			 where od_idx = '$data[od_idx]'
	");

	sql_query("update rb_order_cart set ct_status = '2' where od_idx = '$data[od_idx]'");

	if($mb[mb_id] && $data[od_get_point] > 0){
		write_member_point($member[mb_id], $data[od_get_point], $data[od_title]." 구매" );
	}

	$cart = sql_list("select * from rb_order_cart where od_idx = '$data[od_idx]'");
	for($i=0;$i<count($cart);$i++){
		$cr_idx = $cart[$i][cr_idx];
		if($cr_idx){
			sql_query("update rb_coupon_record set cr_status = 2, cr_use_date = now() where cr_idx = '$cr_idx'");
		}
	}

	$url = ($from_view == '1') ? "./order_view.php?od_idx=".$data[od_idx]."&".$query : "./order_list.php?$query";
	alert("결제처리되었습니다.", $url);
}else if($_GET[mode] == "cancel"){
	if(!in_array($data[od_status], array(1, 2, 3, 4, 5))){
		alert("취소할수 없는 주문건입니다.");
	}

	$mb_id = $data[mb_id];
	$mb = get_member($mb_id);

	// 주문테이블에 적기
	sql_query("
	update rb_order set
			od_status = 9,
			od_paydate = '0000-00-00 00:00:00'
			 where od_idx = '$_GET[od_idx]'
	");
	sql_query("update rb_order_cart set ct_status = '9' where od_idx = '$data[od_idx]'");

	if(in_array($data[od_status], array(2, 3, 4, 5))){
		//준 포인트 삭제
		if($mb[mb_id] && $data[od_get_point] > 0){
			write_member_point($member[mb_id], (0 - $data[od_get_point]), $data[od_title]." 구매 취소" );
		}
	}

	//사용한 포인트 환불
	if($mb[mb_id] && $_POST[od_point] > 0){
		write_member_point($member[mb_id], $_POST[od_point], $data[od_title]." 구매 취소" );
	}

	//쿠폰되돌리기
	$cart = sql_list("select * from rb_order_cart where od_idx = '$data[od_idx]'");
	for($i=0;$i<count($cart);$i++){
		$cr_idx = $cart[$i][cr_idx];
		if($cr_idx){
			sql_query("update rb_coupon_record set cr_status = 1, cr_use_date = ''0000-00-00 00:00:00'' where cr_idx = '$cr_idx'");
		}
	}

	$cart = sql_list("select * from rb_order_cart as c where c.od_idx = '$data[od_idx]' ");

	//배송삭제
	sql_query("delete from rb_delivery  where od_idx = '$data[od_idx]'");
	sql_query("delete from rb_order_cart  where od_idx = '$data[od_idx]'");

	//후기삭제
	sql_query("delete from rb_product_review  where od_idx = '$data[od_idx]'");
	for($i=0;$i<count($cart);$i++){
		$_pd_idx = $cart[$i][pd_idx];
		if($_pd_idx){
			set_product_review_result($_pd_idx);
		}
	}


	$url = ($from_view == '1') ? "./order_view.php?od_idx=".$data[od_idx]."&".$query : "./order_list.php?$query";
	alert("취소되었습니다.", $url);
}else if($_POST[mode] == "delivery_all"){
	if(!in_array($data[od_status], array(2, 3))){
		alert("배송할수 없는 주문건입니다.");
	}

	$mb_id = $data[mb_id];
	$mb = get_member($mb_id);

	// 주문테이블에 적기
	sql_query("
	update rb_order set
			od_status = 5
			 where od_idx = '$data[od_idx]'
	");

	//배송테이블에 적기
	sql_query("
	insert into rb_delivery set
		od_idx = '$data[od_idx]',
		de_company = '$_POST[de_company]',
		de_num = '$_POST[de_num]',
		de_regdate = now()
	");
	$de_idx = mysql_insert_id();

	////배송카트에 적기
	//카트꺼부르기
	$cart = sql_list("select * from rb_order_cart as c where c.od_idx = '$data[od_idx]' ");

	for($i=0;$i<count($cart);$i++){
		//배송할수량
		$ct_idx = $cart[$i][ct_idx];
		$de_cnt = $cart[$i][ct_cnt] - $cart[$i][ct_deli_cnt];
		if($de_cnt > 0){
			sql_query("
			insert into rb_delivery_cart set
				od_idx = '$data[od_idx]',
				de_idx = '$de_idx',
				ct_idx = '$ct_idx',
				de_cnt = '$de_cnt'
			");
		}
	}
	sql_query("update rb_order_cart set ct_status = '5', ct_deli_cnt = ct_cnt where od_idx = '$data[od_idx]'");


	$url = ($from_view == '1') ? "./order_view.php?od_idx=".$data[od_idx]."&".$query : "./order_list.php?$query";
	alert("배송되었습니다.", $url);
}else if($_POST[mode] == "delivery_view"){
	if(!in_array($data[od_status], array(2, 3))){
		alert("배송할수 없는 주문건입니다.");
	}

	$mb_id = $data[mb_id];
	$mb = get_member($mb_id);


	////배송카트에 적기
	//카트꺼부르기
	$cart = sql_list("select * from rb_order_cart as c where c.od_idx = '$data[od_idx]' ");
	$de_c = 0;
	for($i=0;$i<count($cart);$i++){
		$ct_idx = $cart[$i][ct_idx];
		if($_POST["de_cnt_".substr($admin_user_agent, 0, 1)."_".$ct_idx] > 0){
			$de_c = 1;
		}
	}

	if($de_c > 0){
		//배송테이블에 적기
		sql_query("
		insert into rb_delivery set
			od_idx = '$data[od_idx]',
			de_company = '$_POST[de_company]',
			de_num = '$_POST[de_num]',
			de_regdate = now()
		");
		$de_idx = mysql_insert_id();


		$cart_cnt = count($cart);
		$fin_deli_cnt = 0;


		for($i=0;$i<count($cart);$i++){
			//배송할수량
			$ct_idx = $cart[$i][ct_idx];
			if($cart[$i][ct_cnt] == $cart[$i][ct_deli_cnt]){
				$fin_deli_cnt++;
			}else if($_POST["de_cnt_".substr($admin_user_agent, 0, 1)."_".$ct_idx] > 0){
				$de_cnt = (($cart[$i][ct_cnt] - $cart[$i][ct_deli_cnt] - $_POST["de_cnt_".substr($admin_user_agent, 0, 1)."_".$ct_idx]) < 0) ? $cart[$i][ct_cnt] - $cart[$i][ct_deli_cnt] : $_POST["de_cnt_".substr($admin_user_agent, 0, 1)."_".$ct_idx];

				sql_query("
				insert into rb_delivery_cart set
					od_idx = '$data[od_idx]',
					de_idx = '$de_idx',
					ct_idx = '$ct_idx',
					de_cnt = '$de_cnt'
				");

				if($cart[$i][ct_cnt] - $cart[$i][ct_deli_cnt] <= $de_cnt){
					$fin_deli_cnt++;
					$ct_status = 5;
					$ct_deli_cnt = $cart[$i][ct_cnt];
				}else{
					$ct_status = 3;
					$ct_deli_cnt = $cart[$i][ct_deli_cnt] + $de_cnt;
				}
				sql_query("update rb_order_cart set ct_status = '$ct_status', ct_deli_cnt = '$ct_deli_cnt' where ct_idx = '$ct_idx'");
			}
		}

		$od_status = ($fin_deli_cnt == $cart_cnt) ? 5 : 3;
		

		// 주문테이블에 적기
		sql_query("
		update rb_order set
				od_status = '$od_status'
				 where od_idx = '$data[od_idx]'
		");

		$url = ($from_view == '1') ? "./order_view.php?od_idx=".$data[od_idx]."&".$query : "./order_list.php?$query";
		alert("배송되었습니다", $url);
	}else{
		$url = ($from_view == '1') ? "./order_view.php?od_idx=".$data[od_idx]."&".$query : "./order_list.php?$query";
		alert("배송수량이 모두 0입니다.", $url);
	}
}

alert("잘못된 접근입니다.", "./order_list.php?$query");
?>