<?php
/**
 * 클립API테스트
 *
*/
$is_api = 1;
include "./_inc/_common.php";



function klip_login($_arr=array()) {

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

	return array(
		'response' => $response
		, 'json' => $_json
		, 'param' => $_arr
		, 'errno' => $errno
		, 'errmsg' => $errmsg
		, 'http_code' => $http_code
		, '_url' => $_url
	);
}
// end func

$_arr = array(
	'email' => 'Kayro.seoul@gmail.com'
	, 'password' => '92407c83d15f86fa78e3a827db0e8bd2be17998b1e9aa3cfd3c10a7b0d549a1b'
);


// $password = "176200";
// $password_hash = hash("sha256", $password);

// echo $password_hash."<br>";

// $_arr = array(
// 	'email' => '98kd013@naver.com'
// 	, 'password' => '71e747e02633bf700de5837354d7a0dcc4bf6d1b11879e49be49aefdc9d26478'
// );


$_res = klip_login($_arr);

echo "<pre>";
print_r($_res);
sql_query("update rb_config set cf_klaytn_address = '".$_res['json']['klaytn_address']."', cf_contract_address = '".$_res['json']['contract_address']."' ");
// echo $_res['json']['access_token'];

// 카드정보 알아오기
$_header = array();
$_header[] = "Authorization: ".$_res['json']['access_token'];
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

print_r($_json);

$_cursor = '';
$_nft_id = '';
$_card_count = 0;
// $sql_del = "delete from rb_klip_bapp";
// sql_query($sql_del);

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

		// $sql_inc = "insert into rb_klip_bapp set
		// 						bapp_img = '".$value['bapp_img']."',
		// 						category_id = '".$value['category_id']."',
		// 						bapp_id = '".$value['id']."',
		// 						bapp_name = '".$value['name']."',
		// 						nft_id = '".$value['nft_id']."',
		// 						nft_order_no = '".$value['nft_order_no']."',
		// 						summary = '".$value['summary']."',
		// 						card_id = '".$value_2['card_id']."',
		// 						card_uri = '".$value_2['card_uri']."',
		// 						created_at = '".$value_2['created_at']."',
		// 						created_at_formatted = '".$value_2['created_at_formatted']."',
		// 						owner = '".$value_2['owner']."',
		// 						sender = '".$value_2['sender']."',
		// 						transaction_hash = '".$value_2['transaction_hash']."',
		// 						updated_at = '".$value_2['updated_at']."',
		// 						updated_at_formatted = '".$value_2['updated_at_formatted']."',
		// 						pd_name = '".$_json_2['name']."',
		// 						pd_description = '".$_json_2['description']."',
		// 						pd_img_url = '".$_json_2['image']."',
		// 						kb_regdate = now()
		// 					";
		// sql_query($sql_inc);
	}

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

					// print_r($_json_4);
				}
			}
			$_cursor = $_json_3['next_cursor'];
			
			print_r($_json_3);

		}
	}
}

// if ($_cursor != '') {

// 	$_param = array(
// 		'cursor' => $_cursor
// 	);
	
// 	// GET방식
// 	// $_url = "https://api.etherscan.io/api?".http_build_query($_param); //실서버
// 	$_url = "https://api.klipwallet.com/v2/wallet/nft/".$_nft_id."?".http_build_query($_param); 

// 	// 카드정보 알아오기
// 	$_header = array();
// 	$_header[] = "Authorization: ".$_res['json']['access_token'];
// 	$_header[] = "Content-Type: application/json";
// 	$_header[] = "User-Agent: curl by lusoft";

// 	$curlObj = curl_init();
// 	//curl_setopt($curlObj, CURLOPT_FOLLOWLOCATION, 1);
// 	curl_setopt($curlObj, CURLOPT_URL, $_url);
// 	curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, false);
// 	curl_setopt($curlObj, CURLOPT_HEADER, false);
// 	curl_setopt($curlObj, CURLOPT_HTTPHEADER, $_header);
// 	curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, true);
// 	//curl_setopt($curlObj, CURLOPT_TIMEOUT, 10); // curl 실행시간
	
	

// 	$response = curl_exec($curlObj);
// 	$_json = json_decode($response,true);
// 	curl_close($curlObj);

// 	print_r($_json);
// }




//동일한 이미지 카운트
// $sql = "SELECT pd_img_url, COUNT( pd_img_url ) AS pd_img_cnt
// 				FROM rb_klip_bapp WHERE 1 GROUP BY pd_img_url";
// $data = sql_list($sql);

// foreach ($data as $key => $value) {
// 	$sql = "select * from rb_klip_bapp where pd_img_url = '".$value['pd_img_url']."' order by kb_idx desc";
// 	$data_sub = sql_list($sql);

// 	foreach ($data_sub as $key_2 => $value_2) {
// 		$pd_img_num = $key_2 + 1;
// 		$sql_upd = "update rb_klip_bapp set 
// 								pd_img_num = '".$pd_img_num."', 
// 								pd_img_cnt = '".$value['pd_img_cnt']."'
// 								where kb_idx = '".$value_2['kb_idx']."'
// 							";
// 		sql_query($sql_upd);
// 	}
// }


/*
curl -X POST "https://api.klipwallet.com/v2/partner/auth" \
-d '{"email":"Kayro.seoul@gmail.com", "password":"92407c83d15f86fa78e3a827db0e8bd2be17998b1e9aa3cfd3c10a7b0d549a1b"}' \
-H "Content-Type: application/json"
*/

//eof
