<?
include "../_inc/_common.php";
header("Content-Type: application/json;charset=utf-8");

if(!$is_member){
	$arr = array();
	$arr['result'] = "error";
	$arr['msg'] = "Available after logging in.";
	echo json_encode($arr);exit;
}

if ($mode == 'search_date') {

	//엑세스토큰 가져오기
	$_arr = array(
		'email' => 'Kayro.seoul@gmail.com'
		, 'password' => '92407c83d15f86fa78e3a827db0e8bd2be17998b1e9aa3cfd3c10a7b0d549a1b'
	);

	$_url = 'https://api.klipwallet.com/v2/partner/auth';
	$_param = array(
		'email' => $_arr['email']
		, 'password' => $_arr['password']
	);
	$_body = json_encode($_param);
	$_header = array();
	$_header[] = "Content-Type: application/json";
	$_header[] = "Accept: application/json";
	$_header[] = "User-Agent: curl by lusoft";
	$_header[] = "Content-Length: ".strlen($_body);

	$curlObj = curl_init();
	//curl_setopt($curlObj, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($curlObj, CURLOPT_URL, $_url);
	curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curlObj, CURLOPT_HEADER, false);
	curl_setopt($curlObj, CURLOPT_HTTPHEADER, $_header);
	curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, true);
	//curl_setopt($curlObj, CURLOPT_TIMEOUT, 10); // curl 실행시간
	curl_setopt($curlObj, CURLOPT_POST, true);
	curl_setopt($curlObj, CURLOPT_POSTFIELDS, $_body);

	$response = curl_exec($curlObj);
	$errno = curl_errno($curlObj);
	$errmsg = curl_error($curlObj);
	$http_code = curl_getinfo($curlObj,CURLINFO_HTTP_CODE);
	$_json = json_decode($response,true);
	curl_close($curlObj);
	$curlObj = null;

	//환경설정에 클래이튼 정보기록
	sql_query("update rb_config set cf_klaytn_address = '".$_json['klaytn_address']."', cf_contract_address = '".$_json['contract_address']."' ");

	// 카드정보 알아오기
	$_header = array();
	$_header[] = "Authorization: ".$_json['access_token'];
	$_header[] = "Content-Type: application/json";
	$_header[] = "User-Agent: curl by lusoft";

	// GET방식
	// $_url = "https://api.klipwallet.com/v2/wallet/bapp?cursor=".http_build_query($_param);
	$_url = "https://api.klipwallet.com/v2/wallet/bapp?";

	$curlObj = curl_init();
	curl_setopt($curlObj, CURLOPT_URL, $_url);
	curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curlObj, CURLOPT_HEADER, false);
	curl_setopt($curlObj, CURLOPT_HTTPHEADER, $_header);
	curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, true);

	$response = curl_exec($curlObj);
	$_json = json_decode($response,true);
	curl_close($curlObj);

	$sql_del = "delete from rb_klip_bapp";
	sql_query($sql_del);

	foreach ($_json['bapps'] as $key => $value) {
	
		foreach ($value['cards'] as $key_2 => $value_2) {

			$curlObj = curl_init();
			curl_setopt($curlObj, CURLOPT_URL, $value_2['card_uri']);
			curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curlObj, CURLOPT_HEADER, 0);
			$response = curl_exec($curlObj);
			$_json_2 = json_decode($response,true);
			curl_close($curlObj);

			// print_r($_json_2);
			$sql_inc = "insert into rb_klip_bapp set
									bapp_img = '".$value['bapp_img']."',
									category_id = '".$value['category_id']."',
									bapp_id = '".$value['id']."',
									bapp_name = '".$value['name']."',
									nft_id = '".$value['nft_id']."',
									nft_order_no = '".$value['nft_order_no']."',
									summary = '".$value['summary']."',
									card_id = '".$value_2['card_id']."',
									card_uri = '".$value_2['card_uri']."',
									created_at = '".$value_2['created_at']."',
									created_at_formatted = '".$value_2['created_at_formatted']."',
									owner = '".$value_2['owner']."',
									sender = '".$value_2['sender']."',
									transaction_hash = '".$value_2['transaction_hash']."',
									updated_at = '".$value_2['updated_at']."',
									updated_at_formatted = '".$value_2['updated_at_formatted']."',
									pd_name = '".addslashes($_json_2['name'])."',
									pd_description = '".addslashes($_json_2['description'])."',
									pd_img_url = '".$_json_2['image']."',
									kb_regdate = now()
								";
			sql_query($sql_inc);
		}
	}

	//카드가 100개 정보밖에 안가져온다. 그래서 그이상은 다시 호출해서 등록해야함------------------------
	if ($value['cards_next_cursor']) {
		$_cursor = $value['cards_next_cursor'];
		$_nft_id = $value['nft_id'];
		$_card_count = $value['card_count'];

		$quotient = sprintf('%d', $_card_count / 100);

		for ($n=0; $n<$quotient; $n++) {
			$_param = array(
				'cursor' => $_cursor
			);
			
			// GET방식
			$_url = "https://api.klipwallet.com/v2/wallet/nft/".$_nft_id."?".http_build_query($_param); 

			// 카드정보 알아오기
			// $_header = array();
			// $_header[] = "Authorization: ".$_res['json']['access_token'];
			// $_header[] = "Content-Type: application/json";
			// $_header[] = "User-Agent: curl by lusoft";

			$curlObj = curl_init();
			//curl_setopt($curlObj, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($curlObj, CURLOPT_URL, $_url);
			curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curlObj, CURLOPT_HEADER, false);
			curl_setopt($curlObj, CURLOPT_HTTPHEADER, $_header);
			curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, true);
			//curl_setopt($curlObj, CURLOPT_TIMEOUT, 10); // curl 실행시간
			
			$response = curl_exec($curlObj);
			$_json_3 = json_decode($response,true);
			curl_close($curlObj);

			if (!empty($_json_3['cards'])){
				foreach ($_json_3['cards'] as $key_5 => $value_5) {
				 	$curlObj = curl_init();
					curl_setopt($curlObj, CURLOPT_URL, $value_5['card_uri']);
					curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
					curl_setopt($curlObj, CURLOPT_HEADER, 0);
					$response = curl_exec($curlObj);
					$_json_4 = json_decode($response,true);
					curl_close($curlObj);

					// // print_r($_json_4);
					$sql_inc = "insert into rb_klip_bapp set
											bapp_img = '".$value['bapp_img']."',
											category_id = '".$value['category_id']."',
											bapp_id = '".$value['id']."',
											bapp_name = '".$value['name']."',
											nft_id = '".$value['nft_id']."',
											nft_order_no = '".$value['nft_order_no']."',
											summary = '".$value['summary']."',
											card_id = '".$value_5['card_id']."',
											card_uri = '".$value_5['card_uri']."',
											created_at = '".$value_5['created_at']."',
											created_at_formatted = '".$value_5['created_at_formatted']."',
											owner = '".$value_5['owner']."',
											sender = '".$value_5['sender']."',
											transaction_hash = '".$value_5['transaction_hash']."',
											updated_at = '".$value_5['updated_at']."',
											updated_at_formatted = '".$value_5['updated_at_formatted']."',
											pd_name = '".addslashes($_json_4['name'])."',
											pd_description = '".addslashes($_json_4['description'])."',
											pd_img_url = '".$_json_4['image']."',
											kb_regdate = now()
										";
					sql_query($sql_inc);

				}
			}
			$_cursor = $_json_3['next_cursor'];
			
			// print_r($_json_3);

		}
	}
	//-------------------------------------------------------------------------------------


	//동일한 이미지 카운트
	$sql = "SELECT pd_img_url, COUNT( pd_img_url ) AS pd_img_cnt
					FROM rb_klip_bapp WHERE 1 GROUP BY pd_img_url";
	$data = sql_list($sql);

	foreach ($data as $key_3 => $value_3) {
		$sql = "select * from rb_klip_bapp where pd_img_url = '".$value_3['pd_img_url']."' order by kb_idx desc";
		$data_sub = sql_list($sql);

		foreach ($data_sub as $key_4 => $value_4) {
			$pd_img_num = $key_4 + 1;
			$sql_upd = "update rb_klip_bapp set 
									pd_img_num = '".$pd_img_num."', 
									pd_img_cnt = '".$value_3['pd_img_cnt']."'
									where kb_idx = '".$value_4['kb_idx']."'
								";
			sql_query($sql_upd);
		}
	}

	//상품으로 저장혹은 업데이트 작업
	$sql = "select * from rb_klip_bapp order by kb_idx desc";
	$data_2 = sql_list($sql);

	foreach ($data_2 as $key_5 => $value_5) {
		$sql = "select * from rb_product where pd_card_id = '".$value_5['card_id']."' and pd_card_uri = '".$value_5['card_uri']."' ";
		$data_chk = sql_fetch($sql);

		if ($data_chk['pd_idx']) {
			$sql_upd = "update rb_product set
									pd_name = '".addslashes($value_5['pd_name'])."',
									nft_id = '".$value_5['nft_id']."',
									pd_card_uri = '".$value_5['card_uri']."',
									pd_contents = '".addslashes($value_5['pd_description'])."',
									pd_img_url = '".$value_5['pd_img_url']."',
									pd_img_num = '".$value_5['pd_img_num']."',
									pd_img_cnt ='".$value_5['pd_img_cnt']."',
									pd_update_regdate = now()
									where pd_idx = '".$data_chk['pd_idx']."'
								";
			sql_query($sql_upd);
		} else {
			$sql_inc = "insert into rb_product set
									pd_card_id = '".$value_5['card_id']."',
									pd_name = '".addslashes($value_5['pd_name'])."',
									nft_id = '".$value_5['nft_id']."',
									pd_card_uri = '".$value_5['card_uri']."',
									pd_contents = '".addslashes($value_5['pd_description'])."',
									pd_upload_type = 1,
									pd_img_url = '".$value_5['pd_img_url']."',
									pd_img_num = '".$value_5['pd_img_num']."',
									pd_img_cnt ='".$value_5['pd_img_cnt']."',
									pd_use = 2,
									pd_regdate = now()
								";
			sql_query($sql_inc);
		}
	}

	$arr = array();
	$arr['result'] = "success";
	$arr['data'] = $_json;
	$arr['msg'] = "";
	echo json_encode($arr);exit;

}

$arr = array();
$arr['result'] = "error";
$arr['msg'] = 'The wrong approach.';
echo json_encode($arr);exit;
