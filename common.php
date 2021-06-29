<?php
/*******************************************************************************
** 개발시점에서 PHP 에러메시지 표시설정 (PHP.ini 설정적용시)
*******************************************************************************/
error_reporting(E_ALL);
ini_set('display_errors', 0);	// 0:메시지 표시않음, 1:메시지 표시
/*******************************************************************************
** 공통 변수, 상수, 코드
*******************************************************************************/
error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING );



require '/var/www/html/vendor/autoload.php';
use \Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Denpa\Bitcoin\Client as BitcoinClient;
// 보안설정이나 프레임이 달라도 쿠키가 통하도록 설정
header('P3P: CP="ALL CURa ADMa DEVa TAIa OUR BUS IND PHY ONL UNI PUR FIN COM NAV INT DEM CNT STA POL HEA PRE LOC OTC"');

if (!defined('G5_SET_TIME_LIMIT')) define('G5_SET_TIME_LIMIT', 0);
@set_time_limit(G5_SET_TIME_LIMIT);

//==========================================================================================================================
// extract($_GET); 명령으로 인해 page.php?_POST[var1]=data1&_POST[var2]=data2 와 같은 코드가 _POST 변수로 사용되는 것을 막음
// 081029 : letsgolee 님께서 도움 주셨습니다.
//--------------------------------------------------------------------------------------------------------------------------
$ext_arr = array ('PHP_SELF', '_ENV', '_GET', '_POST', '_FILES', '_SERVER', '_COOKIE', '_SESSION', '_REQUEST',
				  'HTTP_ENV_VARS', 'HTTP_GET_VARS', 'HTTP_POST_VARS', 'HTTP_POST_FILES', 'HTTP_SERVER_VARS',
				  'HTTP_COOKIE_VARS', 'HTTP_SESSION_VARS', 'GLOBALS');
$ext_cnt = count($ext_arr);
for ($i=0; $i<$ext_cnt; $i++) {
	// POST, GET 으로 선언된 전역변수가 있다면 unset() 시킴
	if (isset($_GET[$ext_arr[$i]]))  unset($_GET[$ext_arr[$i]]);
	if (isset($_POST[$ext_arr[$i]])) unset($_POST[$ext_arr[$i]]);
}
//==========================================================================================================================

include_once("lib/db.connect.php");
include_once("config.php");

session_start();

// echo 'current message domain is set: ' . $results. "\n";
// try {
// 	$i18n = new \Delight\I18n\I18n([]);
// }
// catch (\Delight\I18n\Throwable\EmptyLocaleListError $e) {
// 	$i18n = null;
// }
// ($i18n === null) or \fail(__LINE__);


// $i18n = new \Delight\I18n\I18n([
// 	\Delight\I18n\Codes::EN_US,
// 	\Delight\I18n\Codes::DA_DK,
// 	\Delight\I18n\Codes::ES_AR,
// 	\Delight\I18n\Codes::ES,
// 	\Delight\I18n\Codes::KO_KR,
// 	\Delight\I18n\Codes::ZH_CN,
// 	\Delight\I18n\Codes::KO,
// 	\Delight\I18n\Codes::SW,
// 	\Delight\I18n\Codes::RU_RU
// ]);
// ($i18n instanceof \Delight\I18n\I18n) or \fail(__LINE__);


// $i18n->setDirectory('/var/www/html/locale');
// ($i18n->getDirectory() === \realpath('/var/www/html/locale')) or \fail(__LINE__);

// $i18n->setModule('messages');
// try {
//     $i18n->setLocaleManually($lang);
// }
// catch (\Delight\I18n\Throwable\LocaleNotSupportedException $e) {
//     die('The locale requested by the user is not supported');
// }

// multi-dimensional array에 사용자지정 함수적용
function array_map_deep($fn, $array)
{
	if(is_array($array)) {
		foreach($array as $key => $value) {
			if(is_array($value)) {
				$array[$key] = array_map_deep($fn, $value);
			} else {
				$array[$key] = call_user_func($fn, $value);
			}
		}
	} else {
		$array = call_user_func($fn, $array);
	}

	return $array;
}


// SQL Injection 대응 문자열 필터링
function sql_escape_string($str)
{
	if(defined('G5_ESCAPE_PATTERN') && defined('G5_ESCAPE_REPLACE')) {
		$pattern = G5_ESCAPE_PATTERN;
		$replace = G5_ESCAPE_REPLACE;

		if($pattern)
			$str = preg_replace($pattern, $replace, $str);
	}

	$str = call_user_func('addslashes', $str);

	return $str;
}


// php.ini 의 register_globals=off 일 경우
@extract($_GET);
@extract($_POST);
@extract($_SERVER);

global $lang;
$lang = "en_US";
$charset = "UTF-8";
if(isset($_GET["lang"]) && strlen($_GET["lang"])> 0){
	$lang = $_GET["lang"];
	if($lang == 'ko' || $lang == "kr" || $lang == "ko_kr" || $lang == "ko_KR"){
		$lang = "ko_KR";
		$charset = "UTF-8";
	}
	else if($lang == 'ja' || $lang == "jp" || $lang == "ja_jp" || $lang == "ja_JP"){
		$lang = "ja_JP";
		$charset = "UTF-8";
	}
	else if($lang == 'zh' || $lang == "cn" || $lang == "zh_cn" || $lang == "zh_CN"){
		$lang = "zh_CN";
		$charset = "UTF-8";
	}
	else if($lang == 'en' || $lang == "us" || $lang == "en_US" || $lang == "en_us"){
		$lang = "en_US";
		$charset = "UTF-8";
	}
}
else{
	// if(isset($_SESSION["ss_member_id"])){
	// 	$member_id = $_SESSION["ss_member_id"];
	// 	$sqlm = "SELECT * FROM members WHERE id = $member_id";
	// 	$resultm = mysqli_query($conn, $sqlm);
	// 	$rowm = mysqli_fetch_assoc($resultm);
	// 	$country = $rowm["country_code"];

	// 	if($country == "+82"){
	// 		$lang = "ko_KR";
	// 		$charset = "UTF-8";
	// 	}
	// 	else if($country == "+86"){
	// 		$lang = "zh_CN";
	// 		$charset = "UTF-8";
	// 	}
	// 	else if($country == "+81"){
	// 		$lang = "ja_JP";
	// 		$charset = "UTF-8";
	// 	}
	// 	else{
	// 		$lang = "en_US";
	// 		$charset = "UTF-8";
	// 	}
	// }
	// else{
		$lang = "ko_KR";
		$charset = "UTF-8";
	// }
}
// header("Content-Type: text/html; charset=UTF-8");
// $results = putenv("LC_ALL=$lang");
// if (!$results) {
//     exit ('putenv failed');
// }

// if($charset == ""){
// 	$results = setlocale(LC_ALL, "{$lang}");
// }
// else{
// 	$results = setlocale(LC_ALL, "{$lang}.{$charset}");
// 	if (!$results) {
// 		exit ('putenv failed');
// 	}
// }

// if (!$results) {
//     exit ('setlocale failed: locale function is not available on this platform, or the given local does not exist in this environment');
// }
// $domain = "messages";

// $results = bindtextdomain($domain, "/var/www/html/locale");
// // echo 'new text domain is set: ' . $results. "\n";
// $results = textdomain($domain);
// if (!$results) {
// 	exit ('putenv failed');
// }
// $member 에 값을 직접 넘길 수 있음
$config = array();
$member = array();
$board  = array();
$group  = array();
$dcpwallet     = array();

// goto_url 함수
function goto_url($url) {
	$url = str_replace("&amp;", "&", $url);
	//echo "<script> location.replace('$url'); </script>";

	if (!headers_sent())
		header('Location: '.$url);
	else {
		echo '<script>';
		echo 'location.replace("'.$url.'");';
		echo '</script>';
		echo '<noscript>';
		echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
		echo '</noscript>';
	}
	exit;
}

function generateLongTextString($length=45) {
	$characters  = "0123456789";
	$characters .= "abcdefghijklmnopqrstuvwxyz";
	$characters .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$characters .= "_";
	$string_generated = "";
	$nmr_loops = $length;
	while ($nmr_loops--) {
		$string_generated .= $characters[mt_rand(0, strlen($characters) - 1)];
	}
	return '0x777'.$string_generated;
}

function generateRandomString($length = 24) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return '0x777'.$randomString;
}

function getEricBalance($address){


	if($eric_coin == null){
		if($erc20 == null){
			$geth = new EthereumRPC('54.180.89.83', 8545);
			$erc20 = new \ERC20\ERC20($geth);
			$eric_coin = $erc20->token('0x7230b0447dBa2e75F63a8064bf0252F3D7C649c3');

			return $eric_coin->balanceOf($address);

		}
		else{
			$eric_coin = $erc20->token('0x7230b0447dBa2e75F63a8064bf0252F3D7C649c3');

			return $eric_coin->balanceOf($address);
		}
		return 0;
	}
	else{
		return $eric_coin->balanceOf($address);
	}
}

// 지갑정보 가져오기
function getBalanceString($uid,$fname='address') {
	global $conn;
		
	if($fname == "balance"){
		$sqlBit = "SELECT address from users where username = '".$uid."'";
		$resultBit=mysqli_query($conn, $sqlBit);
		$bitCoin=mysqli_fetch_assoc($resultBit);
		if($bitCoin['address'] == "" || $bitCoin['address'] == null) {
			return $uid;
		} else {
			return getEricBalance($bitCoin['address']);
		}

	}
	else if($fname == "address"){
		$sqlBit = "SELECT ".$fname." from users where username = '".$uid."'";
		$resultBit=mysqli_query($conn, $sqlBit);
		$bitCoin=mysqli_fetch_assoc($resultBit);
		if($bitCoin[$fname] !="") {
			return $bitCoin[$fname];
		} else {
			return "";
		}
	}
	else{
		return "";
	}
}
?>
