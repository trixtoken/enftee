<?php
include_once('_common.php');

session_start();

if(!isset($_SESSION["ss_member_id"])){
  header('Location:/login.php');  
}

$member_id = $_SESSION["ss_member_id"];
$sql = "SELECT * FROM members WHERE id = $member_id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

$username = $row["username"];
$phone_number = $row["phone_number"];
$eth_address = $row["eth_address"];

$frim = $row["frim"];
$eth = $row["eth"];
$usdt = $row["usdt"];
$tdos = $row["tdos"];


$url = "http://54.180.156.108:8545";

$data = array(
        "jsonrpc" => "2.0",
        "method" => "eth_getBalance",
        "params" => [$eth_address, "latest"],
        "id" => "1"
);

$json_encoded_data = json_encode($data);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_encoded_data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($json_encoded_data))
);

$resulti = json_decode(curl_exec($ch));
curl_close($ch);

$parsed = $resulti->result;
$ethbalance = decodeHex($parsed) / 1e18;



$url2 = 'http://54.180.156.108:8080/token/0x28cccacd3d981e562ab71f835170b6c1d73c01d9/'.$eth_address;
$contents = file_get_contents($url2);
//If $contents is not a boolean FALSE value.
if($contents !== false){
    // echo $contents;
    $content = json_decode($contents);
    $frimbalance =  round($content->balance, 6);
}
else{
    // echo $url2;
}

function decodeHex($input){
  if (substr($input, 0, 2) == '0x') {
      $input = substr($input, 2);
  }

  if (preg_match('/[a-f0-9]+/', $input)) {
      return hexdec($input);
  }

  return $input;
}

$coin = 'FRI';
if(isset($_GET["coin"])){
    $coin = $_GET["coin"];
}
if($coin == 'FRI'){
    $amount = $frimbalance - $row["frim_locked"] - $row["frim_sent"];
}
else if($coin == 'ETH'){
    $amount = $eth;
}
else if($coin == 'TDOS'){
    $amount = $tdos;
}
else if($coin == 'USDT'){
    $amount = $usdt;
}
else{
    $amount = 0;
}
// $address = $row["ethaddress"];

?>
<html lang="ko"><head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Favion -->
    

    <!-- Libs CSS -->
    <link rel="stylesheet" href="/inc/reformUser/assets/fonts/feather/feather.min.css">
    <link rel="stylesheet" href="/inc/reformUser/assets/libs/highlight.js/styles/vs2015.css">
    <link rel="stylesheet" href="/inc/reformUser/assets/libs/quill/dist/quill.core.css">
    <link rel="stylesheet" href="/inc/reformUser/assets/libs/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="/inc/reformUser/assets/libs/flatpickr/dist/flatpickr.min.css">

    <!-- Theme CSS -->     
    <link rel="stylesheet" href="/inc/reformUser/assets/css/theme.min.css" id="stylesheetLight">
    <link rel="stylesheet" href="/inc/reformUser/build/css/reform.css">
    <script src="/inc/user/build/js/common.js"></script>

    <link href="https://code.jquery.com/ui/1.10.3/themes/redmond/jquery-ui.css" rel="stylesheet" media="screen">
    <!-- jquery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script>window.jQuery||document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"><\/script>')</script>

    <!-- clipboard.js -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.6.1/clipboard.min.js"></script>

    <!-- toastr.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
   
    <title>FRI Wallet</title>
  </head>
<body>
<script type="text/javascript">function langChange(){var selectValue=document.getElementById('langSelect').value;window.location.href="/Auth/setLanguage?country="+selectValue;return true;}</script>
  <nav class="navbar navbar-vertical fixed-left navbar-expand-md navbar-light" id="sidebar">
        <div class="container-fluid">

          <!-- Toggler -->
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#sidebarCollapse" aria-controls="sidebarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <!-- Brand -->
          <a class="navbar-brand" href="/" style="margin:0 auto;">
            <script data-pagespeed-no-defer="">//<![CDATA[
(function(){for(var g="function"==typeof Object.defineProperties?Object.defineProperty:function(b,c,a){if(a.get||a.set)throw new TypeError("ES3 does not support getters and setters.");b!=Array.prototype&&b!=Object.prototype&&(b[c]=a.value)},h="undefined"!=typeof window&&window===this?this:"undefined"!=typeof global&&null!=global?global:this,k=["String","prototype","repeat"],l=0;l<k.length-1;l++){var m=k[l];m in h||(h[m]={});h=h[m]}var n=k[k.length-1],p=h[n],q=p?p:function(b){var c;if(null==this)throw new TypeError("The 'this' value for String.prototype.repeat must not be null or undefined");c=this+"";if(0>b||1342177279<b)throw new RangeError("Invalid count value");b|=0;for(var a="";b;)if(b&1&&(a+=c),b>>>=1)c+=c;return a};q!=p&&null!=q&&g(h,n,{configurable:!0,writable:!0,value:q});
var t=this;function u(b,c){var a=b.split("."),d=t;a[0]in d||!d.execScript||d.execScript("var "+a[0]);for(var e;a.length&&(e=a.shift());)a.length||void 0===c?d[e]?d=d[e]:d=d[e]={}:d[e]=c};function v(b){var c=b.length;if(0<c){for(var a=Array(c),d=0;d<c;d++)a[d]=b[d];return a}return[]};function w(b){var c=window;if(c.addEventListener)c.addEventListener("load",b,!1);else if(c.attachEvent)c.attachEvent("onload",b);else{var a=c.onload;c.onload=function(){b.call(this);a&&a.call(this)}}};
var x;function y(b,c,a,d,e){this.h=b;this.j=c;this.l=a;this.f=e;this.g={height:window.innerHeight||document.documentElement.clientHeight||document.body.clientHeight,width:window.innerWidth||document.documentElement.clientWidth||document.body.clientWidth};this.i=d;this.b={};this.a=[];this.c={}}function z(b,c){var a,d,e=c.getAttribute("data-pagespeed-url-hash");if(a=e&&!(e in b.c))if(0>=c.offsetWidth&&0>=c.offsetHeight)a=!1;else{d=c.getBoundingClientRect();
var f=document.body;a=d.top+("pageYOffset"in window?window.pageYOffset:(document.documentElement||f.parentNode||f).scrollTop);d=d.left+("pageXOffset"in window?window.pageXOffset:(document.documentElement||f.parentNode||f).scrollLeft);f=a.toString()+","+d;b.b.hasOwnProperty(f)?a=!1:(b.b[f]=!0,a=a<=b.g.height&&d<=b.g.width)}a&&(b.a.push(e),b.c[e]=!0)}y.prototype.checkImageForCriticality=function(b){b.getBoundingClientRect&&z(this,b)};u("pagespeed.CriticalImages.checkImageForCriticality",function(b){x.checkImageForCriticality(b)});u("pagespeed.CriticalImages.checkCriticalImages",function(){A(x)});function A(b){b.b={};for(var c=["IMG","INPUT"],a=[],d=0;d<c.length;++d)a=a.concat(v(document.getElementsByTagName(c[d])));if(a.length&&a[0].getBoundingClientRect){for(d=0;c=a[d];++d)z(b,c);a="oh="+b.l;b.f&&(a+="&n="+b.f);if(c=!!b.a.length)for(a+="&ci="+encodeURIComponent(b.a[0]),d=1;d<b.a.length;++d){var e=","+encodeURIComponent(b.a[d]);131072>=a.length+e.length&&(a+=e)}b.i&&(e="&rd="+encodeURIComponent(JSON.stringify(B())),131072>=a.length+e.length&&(a+=e),c=!0);C=a;if(c){d=b.h;b=b.j;
var f;if(window.XMLHttpRequest)f=new XMLHttpRequest;else if(window.ActiveXObject)try{f=new ActiveXObject("Msxml2.XMLHTTP")}catch(r){try{f=new ActiveXObject("Microsoft.XMLHTTP")}catch(D){}}f&&(f.open("POST",d+(-1==d.indexOf("?")?"?":"&")+"url="+encodeURIComponent(b)),f.setRequestHeader("Content-Type","application/x-www-form-urlencoded"),f.send(a))}}}function B(){var b={},c;c=document.getElementsByTagName("IMG");if(!c.length)return{};
var a=c[0];if(!("naturalWidth"in a&&"naturalHeight"in a))return{};for(var d=0;a=c[d];++d){var e=a.getAttribute("data-pagespeed-url-hash");e&&(!(e in b)&&0<a.width&&0<a.height&&0<a.naturalWidth&&0<a.naturalHeight||e in b&&a.width>=b[e].o&&a.height>=b[e].m)&&(b[e]={rw:a.width,rh:a.height,ow:a.naturalWidth,oh:a.naturalHeight})}return b}var C="";u("pagespeed.CriticalImages.getBeaconData",function(){return C});u("pagespeed.CriticalImages.Run",function(b,c,a,d,e,f){var r=new y(b,c,a,e,f);x=r;d&&w(function(){window.setTimeout(function(){A(r)},0)})});})();pagespeed.CriticalImages.Run('/mod_pagespeed_beacon','https://new.likrwallet.com/transaction.php','8Xxa2XQLv9',true,false,'ETX2m80ZdnY');
//]]></script><img src="/inc/user/build/images/logo/logo.png?v=3" class="navbar-brand-img mx-auto" alt="logo" data-pagespeed-url-hash="915892273" onload="pagespeed.CriticalImages.checkImageForCriticality(this);">
          </a>

          <!-- Collapse -->
          <div class="collapse navbar-collapse" id="sidebarCollapse">
      
            <!-- Navigation -->
            <ul class="navbar-nav">
              <li class="nav-item">
                <a class="nav-link " href="/main.php">
                  <i class="nav-icon home "></i>홈                </a>
              </li>

              

              <!-- 토큰지갑1 시작-->  
              <li class="nav-item">
                <a class="nav-link " href="#sidebarEthToken1" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="#sidebarEthToken1">
                  <i class="nav-icon wallet "></i><span class="textLITE pr-1">FRI</span> 지갑                </a>
                <div class="collapse " id="sidebarEthToken1">
                  <ul class="nav nav-sm flex-column">
                    <li class="nav-item">
                      <a href="/transaction.php?coin=FRI" class="nav-link ">
                        <span class="textToken1 pr-1">FRI</span> 보내기 &amp; 받기                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="/history.php?coin=FRI" class="nav-link ">
                        <span class="textToken1 pr-1">FRI</span> 거래 목록                      </a>
                    </li>
                    <!-- <li class="nav-item">
                      <a href="/transfer.php?coin=FRI" class="nav-link ">
                        <span class="textToken1 pr-1">FRI</span> 환전                      </a>
                    </li> -->
                  </ul>
                </div>
              </li>
              <!-- 토큰지갑1 종료-->
             

              
                            <!-- BTC지갑 시작-->
              <!-- <li class="nav-item">
                <a class="nav-link " href="#sidebarBTC" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="sidebarBTC">
                  <i class="nav-icon wallet "></i><span class="textBTC pr-1">BTC</span> 지갑                </a>
                <div class="collapse " id="sidebarBTC">
                  <ul class="nav nav-sm flex-column">
                    <li class="nav-item">
                      <a href="/Main/btcSendReceive" class="nav-link ">
                        <span class="textBTC pr-1">BTC</span> 보내기 &amp; 받기                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="/Main/btcTransactionList" class="nav-link ">
                        <span class="textBTC pr-1">BTC</span> 거래 목록                      </a>
                    </li>
                  </ul>
                </div>
              </li> -->
              <!-- BTC지갑 종료-->
              
                            <!-- ETH지갑 시작-->
              <li class="nav-item">
                <a class="nav-link " href="#sidebarETH" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="sidebarETH">
                  <i class="nav-icon wallet "></i><span class="textETH pr-1">ETH</span> 지갑                </a>
                <div class="collapse " id="sidebarETH">
                  <ul class="nav nav-sm flex-column"> 
                    <li class="nav-item">
                      <a href="/transaction.php?coin=ETH" class="nav-link ">
                        <span class="textETH pr-1">ETH</span> 보내기 &amp; 받기                      </a>
                    </li> 
                    <li class="nav-item">
                      <a href="/history.php?coin=ETH" class="nav-link ">
                        <span class="textETH pr-1">ETH</span> 거래 목록                      </a>
                    </li>
                  </ul>
                </div>
              </li>
              <!-- ETH지갑 종료-->
              
              
              <li class="nav-item">
                <a class="nav-link " href="/profile.php">
                  <i class="nav-icon user "></i>내 프로필                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="/logout.php" onclick="return confirm('로그아웃 하시겠습니까?');">
                  <i class="nav-icon logout"></i>로그아웃                </a>
              </li>
            </ul>

            <!-- Divider -->
            <hr class="navbar-divider my-3">

            <!-- Heading -->
            <h6 class="navbar-heading">
              언어 선택            </h6>

              <!-- language select -->
              <select name="langSelect" id="langSelect" class="language-select" data-width="fit" onchange="langChange();">
<option value="ko" selected="selected">한국어</option>
<option value="en">English</option>
<option value="cn">简体中文</option>
<option value="hk">繁體中文</option>
<option value="jp">日本語</option>
<option value="vn">Tiếng Việt</option>
</select>
              <!-- /language select -->

            <!-- <div class="d-flex justify-content-center mt-4 mb-4">
                <a href="https://play.google.com/store/apps/details?id=kr.likr.wallet.android" class="d-block d-md-inline-block"><img src="/inc/user/build/images/icon/google-play-badge.svg" width="100" data-pagespeed-url-hash="3408597403" onload="pagespeed.CriticalImages.checkImageForCriticality(this);"></a>
            </div> -->
      
            <!-- Push content down -->
            <div class="mt-auto"></div>
      
          </div> <!-- / .navbar-collapse -->

        </div>
      </nav>
  <!-- Navigation End -->
<script type="text/javascript">
$(document).ready(function(){
    $(".btn-copy2").each(function(){
        var n="#"+$(this).attr('id');
        var defval=$(this).attr('defval');
        console.log(n);
        console.log(defval);
        new Clipboard(n).on('success',function(e){
            toastr.info(e.text,'Copied',{"closeButton":true,"progressBar":false,"timeOut":"3000","positionClass":"toast-bottom-right",});
            $(e.trigger).attr('title','Copied').tooltip('show');
        }).on('error',function(e){
            console.error('Action:',e.action);
            console.error('Trigger:',e.trigger);
            window.prompt('Copy with Ctrl+C',defval);
        });
    });
});
function callQrCamera(){
    var json_parameter='{"tip":"QR 코드를 네모안에 스캔해 주세요.","capture_load_url":"javascript:input_value(\'|+val+|\')"}';
    window.web_view_bridge.callQrCamera(json_parameter);
}
function input_value(val){
    var sendFrm=document.sendFrm;
    document.sendFrm.walletAddress.value=val;
}
function displayAddress(){
    $('#address_wrap').removeClass('address_wrap off').addClass('address_wrap');
    $('#phone_wrap').removeClass('phone_wrap').addClass('phone_wrap off');
    $('#isSendCheck').val('0');
    $('#mobileNumber').val('');
    $('#checkMobileTxt').html('');
}
function displayMobile(){
    $('#address_wrap').removeClass('address_wrap').addClass('address_wrap off');
    $('#phone_wrap').removeClass('phone_wrap off').addClass('phone_wrap');
    $('#isSendCheck').val('0');
    $('#walletAddress').val('');
    $('#checkWAddressTxt').html('');
}
function memberAddressBookPop(){
    refreshAddressBook();
    $("#addressBook").modal();
}
function addAddressBook(){
    if(comInputCheck('text','nickName')===true){
        alert("이름을 입력해주세요.");
        $('#nickName').focus();
        return false;
    }
    if(comInputCheck('text','adbook_walletAddress')===true){
        alert("지갑 주소를 입력해주세요.");
        $('#adbook_walletAddress').focus();
        return false;
    }
    var mode='ins';
    var addressFrm=document.addressFrm;
    $.ajax({
        type:'post',
        url:'/ajax/ajax.addressbook.php',
        data:{
            type:mode,
            walletName:addressFrm.walletName.value,
            nickName:addressFrm.nickName.value,
            walletAddress:addressFrm.adbook_walletAddress.value
        },
        cache:false,
        async:false,
        dataType:'json',
        success:function(data){
            if(data.success=="success"){
                alert("주소록에 주소가 등록되었습니다.");
                $("#nickName").val('');
                $("#adbook_walletAddress").val('');
                refreshAddressBook();
                return true;
            }
            else{
                alert(data.message);
                location.href="/transaction.php?coin=<?=$coin?>";
                return false;
            }
        }
    });
}
function delAddressBook(idx){
    var mode='del';
    var adbookIdx=idx;
    $.ajax({
        type:'post',
        url:'/ajax/ajax.addressbook.php',
        data:{
            type:mode,
            adbookIdx:adbookIdx
        },
        cache:false,
        async:false,
        dataType:'json',
        success:function(data){
            if(data.success=="success"){
                alert("주소가 삭제되었습니다.");
                refreshAddressBook();
                return true;
            }
            else{
                alert("주소 삭제에 실패했습니다.");
                location.href="/transaction.php?coin=<?=$coin?>";
                return false;
            }
        }
    });
}
function refreshAddressBook(){
    var addressFrm=document.addressFrm;
    $("#addressBookTxt").html('');
    $.ajax({
        type:'post',
        url:'/ajax/ajax.addressbook.php',
        data:{
            type:"getList",
            walletName:addressFrm.walletName.value
        },
        cache:false,
        async:false,
        dataType:'json',
        success:function(data){
            if(data.success=="success"){
                var addressListHtml="";
                if(data.addressList.length>0){
                    for(var $i=0;$i<data.addressList.length;$i++){
                        addressListHtml+="<tr>";
                        addressListHtml+="<td>"+data.addressList[$i].nickName+"</td>";
                        addressListHtml+="<td>";
                        addressListHtml+="<a href=\"javascript:getInAddress('"+data.addressList[$i].walletAddress+"')\">"+data.addressList[$i].walletAddress+"</a>";
                        addressListHtml+="</td>";
                        addressListHtml+="<td><button type='button' class='close addbtn' onclick='delAddressBook("+data.addressList[$i].id+");'>&times;</button></td>";
                        addressListHtml+="</tr>";
                    }
                }
                $('#addressBookTxt').append(addressListHtml);
            }
        },
        error:function(error){
            console.log(error);
        }
    });
}
function getInAddress(address){
    if(confirm("해당주소를 선택하시겠습니까?")){
        $("#walletAddress").val('');
        $("#walletAddress").val(address);
        $("#addressBook").modal('hide');
        checkWalletAddress();
    }
}
function checkWalletAddress(){
    var sendFrm=document.sendFrm;
    var hiddenFrm=document.hiddenFrm;
    $.ajax({
        type:'post',
        url:'/ajax/ajax.addressbook.php',
        data:{
            type:"validateAddress",
            walletType:'<?=$coin?>',
            walletAddress:sendFrm.walletAddress.value
        },
        cache:false,
        async:false,
        dataType:'json',
        success:function(data){
            if(data.success=="success"){
                $('#isSendCheck').val('1');
                $('#checkWAddressTxt').html("사용가능한 주소입니다.").removeClass('text-danger').addClass('text-green');
                sendFrm.amount.focus();
            }
            else{
                $('#checkWAddressTxt').html(data.message).removeClass('text-green').addClass('text-danger');
                sendFrm.walletAddress.focus();
            }
        }
    });
}
function checkMobile(){
    var sendFrm=document.sendFrm;
    var hiddenFrm=document.hiddenFrm;
    $.ajax({
        type:'post',
        url:'/common/SendCheck/ajaxCheckMobile',
        data:{
            countryNumber:sendFrm.selectCountry.value,mobileNumber:sendFrm.mobileNumber.value
        },
        cache:false,
        async:false,
        dataType:'json',
        success:function(data){
            if(data.isResult==true){
                $('#isSendCheck').val('1');
                $('#checkMobileTxt').html(data.data.userName).removeClass('text-danger').addClass('text-green');
                sendFrm.amount.focus();
            }
            else{
                $('#checkMobileTxt').html(data.data.message).removeClass('text-green').addClass('text-danger');
                sendFrm.memberId.focus();
            }
        }
    });
}
function resetCheckAddress(){
    $('#isSendCheck').val('0');
    $('#checkWAddressTxt').html("사용가능한 지갑주소인지 확인해주세요. (확인 버튼 누르기)").removeClass('text-green').addClass('text-danger');
}
function resetCheckMobile(){
    $('#isSendCheck').val('0');
    $('#checkMobileTxt').html("전송가능한 모바일 번호인지 확인해주세요. (확인 버튼 누르기)").removeClass('text-green').addClass('text-danger');
}
function commGoAct(){
    var frm=document.sendFrm;
    var checkPass=false;
    if($('#ableSend').val()!='1'){
        alert("전송할 수 없습니다. 관리자에게 문의해주세요.");
        return false;
    }
    if(comInputCheck('text','amount')===true){
        alert("전송할 수량을 입력해주세요.");
        $('#amount').focus();
        return false;
    }
    $.ajax({
        type:'post',
        url:'/ajax/ajax.member.php',
        data:{
            type:"checkAmount",
            coin:'<?=$coin?>',
            amount:sendFrm.amount.value,
            maxamount:<?=$amount?>
        },
        cache:false,
        async:false,
        dataType:'json',
        success:function(data){
            if(data.success=="success"){
                checkPass=true;
            }
            else{
                checkPass=false;
            }
        }
    });
    if(checkPass==false){
        alert("출금 가능한 수량을 입력해주세요.");
        $('#amount').focus();
        return false;
    }
    if($('#isSendCheck').val()!='1'){
        alert("지갑주소를 확인해주세요.");
        return false;
    }
    if($('#isUsedPin').val()=='1'){
        pinNumberCheckPop();
        return false;
    }
    if(confirm("전송하시겠습니까?")){
        // frm.action="/Main/actEthTokenOneSendReceive";
        frm.submit();
    }
    else{
        return false;
    }
}
function pinNumberCheckPop(){
    shufflePinNumber();
    $("#pinNumberCheck").modal();
}
function shufflePinNumber(){
    $.ajax({
        type:'post',
        url:'/common/SendCheck/ajaxShufflePinNumber',
        data:{},
        cache:false,
        async:false,
        dataType:'json',
        success:function(data){
            if(data.isResult==true){
                $("#pinNumber_0").html('');
                $("#pinNumber_0").html(data.data.pinNumberArr[0]);
                $("#pinNumber_0").attr("href","javascript:inputPinNumber('"+data.data.pinNumberArr[0]+"');");
                $("#pinNumber_1").html('');
                $("#pinNumber_1").html(data.data.pinNumberArr[1]);
                $("#pinNumber_1").attr("href","javascript:inputPinNumber('"+data.data.pinNumberArr[1]+"');");
                $("#pinNumber_2").html('');
                $("#pinNumber_2").html(data.data.pinNumberArr[2]);
                $("#pinNumber_2").attr("href","javascript:inputPinNumber('"+data.data.pinNumberArr[2]+"');");
                $("#pinNumber_3").html('');
                $("#pinNumber_3").html(data.data.pinNumberArr[3]);
                $("#pinNumber_3").attr("href","javascript:inputPinNumber('"+data.data.pinNumberArr[3]+"');");
                $("#pinNumber_4").html('');
                $("#pinNumber_4").html(data.data.pinNumberArr[4]);
                $("#pinNumber_4").attr("href","javascript:inputPinNumber('"+data.data.pinNumberArr[4]+"');");
                $("#pinNumber_5").html('');
                $("#pinNumber_5").html(data.data.pinNumberArr[5]);
                $("#pinNumber_5").attr("href","javascript:inputPinNumber('"+data.data.pinNumberArr[5]+"');");
                $("#pinNumber_6").html('');
                $("#pinNumber_6").html(data.data.pinNumberArr[6]);
                $("#pinNumber_6").attr("href","javascript:inputPinNumber('"+data.data.pinNumberArr[6]+"');");
                $("#pinNumber_7").html('');
                $("#pinNumber_7").html(data.data.pinNumberArr[7]);
                $("#pinNumber_7").attr("href","javascript:inputPinNumber('"+data.data.pinNumberArr[7]+"');");
                $("#pinNumber_8").html('');
                $("#pinNumber_8").html(data.data.pinNumberArr[8]);
                $("#pinNumber_8").attr("href","javascript:inputPinNumber('"+data.data.pinNumberArr[8]+"');");
                $("#pinNumber_9").html('');
                $("#pinNumber_9").html(data.data.pinNumberArr[9]);
                $("#pinNumber_9").attr("href","javascript:inputPinNumber('"+data.data.pinNumberArr[9]+"');");
                return true;
            }
        }
    });
}
function inputPinNumber(str){
    var frm=document.pinForm;
    var preNumber=frm.pinNumber.value;
    var plusNumber=str;
    var pinNumber=preNumber+plusNumber;
    var checkPass=false;
    if(pinNumber.length>4){

    }
    else{
        $('#pinNumber').val(pinNumber);
        if(pinNumber.length==4){
            $.ajax({
                type:'post',
                url:'/common/SendCheck/ajaxMemberPinNumber',
                data:{mIdx:$('#mIdx').val(),pinNumber:$('#pinNumber').val()},
                cache:false,
                async:false,
                dataType:'json',
                success:function(data){
                    if(data.isResult==true){
                        console.log(data.message);
                        alert(data.data.message);
                        $('#checkPinNumber').val(pinNumber);checkPass=true;
                    }
                    else{
                        console.log(data.message);
                        alert(data.data.message);
                        $('#checkPinNumber').val("");
                        $('#pinNumber').val("");
                        checkPass=false;
                    }
                }
            });
            if(checkPass){
                var sendFrm=document.sendFrm;
                sendFrm.action="/Main/actEthTokenOneSendReceive";
                sendFrm.submit();
            }
        }
    }
}
function removeLastPinNumber(){var frm=document.pinForm;
    var preNumber=frm.pinNumber.value;
    var pinNumber=preNumber.slice(0,-1);
    if(preNumber.length>0){
        $('#pinNumber').val(pinNumber);
    }
}
function resetPinNumber(){
    shufflePinNumber();
    $('#pinNumber').val('');
}
</script>

<link rel="stylesheet" href="/inc/user/build/renewal/css/fontawesome/css/all.css">

     <div class="main-content pt-6">
      
      <!-- CARDS -->
      <div class="container-fluid">
        <!-- row -->
        <div class="row">
          <div class="col-12">
            <div class="card">
                
                <div class="card-header">
                  <div class="row align-items-center">
                    <div class="col">
                      <!-- Title -->
                      <h2 class="card-header-title">
                        <?=$coin?> 보내기 &amp; 받기                                              </h2>
                    </div>
                  </div> 
                </div>

                <ul role="tablist" id="pills-tab" class="nav nav_tab">
                    <li class="nav-item">
                        <a href="#tab1" data-toggle="tab" class="nav-link active show">보내기</a>
                    </li>
                    <li class="nav-item">
                        <a href="#tab2" data-toggle="tab" class="nav-link">받기</a>
                    </li>
                </ul>
                
                <!-- 보내기 -->
                <div class="tab-content card-body">
                    <div role="tabpanel" id="tab1" class="tab-pane fade active show">
                        <form name="hiddenFrm">
                                <input type="hidden" name="isSendCheck" id="isSendCheck" value="0">
                                <input type="hidden" name="ableSend" id="ableSend" value="1">
                                <input type="hidden" name="isUsedPin" id="isUsedPin" value="2">
                                <input type="hidden" name="mIdx" id="mIdx" value="<?=$member_id?>">
                            </form>
                            <form name="sendFrm" method="post" action="/ajax/ajax.withdraw.php">
                                <input type="hidden" name="type" value="withdraw">
                                <input type="hidden" name="coin" value="<?=$coin?>">
                                <input type="hidden" name="checkPinNumber" id="checkPinNumber" value="">
                        <!-- 발송 방법 -->
                        <div class="row">
                            <div class="col-12 col-xl-6 mb-3">
                            <div class="card-inside card-tab">
                                <div class="card-header">발송 방법</div> 
                                <div class="card-body">
                                    <div class="mb-4">
                                        <div class="mb-3">
                                            <input type="radio" id="test1" name="sendType" onclick="displayAddress();" value="1" checked="">
                                            <label for="test1" class="font-weight-bold">지갑 주소</label>
                                        </div>
                                        <!-- <div class="mb-3">
                                            <input type="radio" id="test2" name="sendType" onclick="displayMobile();" value="2">
                                            <label for="test2" class="font-weight-bold">휴대폰 번호</label>
                                        </div> -->
                                    </div>

                                    <div class="card-body" style="border-top:1px solid #e6e6e6; border-radius:.5rem;">
                                       <div class="row">
                                           <div class="col">
                                               <!-- Title -->
                                               <h4 class="card-title">
                                                   <?=$coin?> 수량 정보                                               </h4>
                                           </div>
                                       </div>
                                       <div class="row align-items-center py-1">
                                           <div class="col">
                                               <span class="h5">현재 보유 수량</span>
                                           </div>
                                           <div class="col-auto">
                                               <span class="h5 text-blue"><?=$amount?> <?=$coin?></span>
                                           </div>
                                       </div>
                                       <!--
                                       <div class="row align-items-center border-bottom py-1">
                                           <div class="col">
                                               <span class="h5">필수 보유 수량</span>
                                               <small class="d-block text-muted">(락업된 수량)</small>
                                           </div>
                                           <div class="col-auto">
                                               <span class="h5 text-blue">0 FRI</span>
                                           </div>
                                       </div>
                                       <div class="row align-items-center py-1">
                                           <div class="col">
                                               <span class="h5">사용가능 수량</span>                                               
                                           </div>
                                           <div class="col-auto">
                                               <span class="h5 text-blue">0 FRI</span>
                                           </div>
                                       </div>
                                       -->
                                   </div>
                                   <small class="text-red">
                                   ※※ 토큰 전송시, 이더리움 수수료가 발생합니다. ( 최대 0.00288 ETH )
                                   </small>

                                </div>
                            </div>
                            </div>
                            <!-- /발송 방법 -->

                            <!-- 발송 정보 -->
                            <div class="col-12 col-xl-6 mb-3">  
                                    <div class="card-inside card-tab">
                                        <div class="card-header">발송 정보 </div> 
                                        <div class="card-body">
                                            <div class="mb-6">

                                                <div id="address_wrap" class="address_wrap">

                                                <div class="row align-items-center mb-3">
                                                    <div class="col">
                                                        <!-- Title -->
                                                        <h4 class="card-title mb-0">
                                                        지갑 주소                                                        <span></span>
                                                        </h4>                                                          
                                                    </div>     
                                                    <div class="col-auto">
                                                                                                        <button type="button" class="btn btn-sm btn-white" onclick="memberAddressBookPop();">주소록 보기</button>
                                                    </div>
                                                </div>                
                                                <div class="mb-3">
                                                    <div class="input-group input-group-merge">
                                                        <input type="text" id="walletAddress" name="walletAddress" class="form-control form-control-appended input-border-grey" placeholder="지갑 주소를 입력해주세요." onkeyup="resetCheckAddress();">
                                                        <div class="input-group-append">
                                                        <button type="button" class="btn btn-primary" onclick="checkWalletAddress();">Check</button>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-1">
                                                        <div class="col"><small id="checkWAddressTxt" class="text-green"></small></div>
                                                    </div>
                                                </div>

                                                </div>

                                                <div id="phone_wrap" class="phone_wrap off">

                                                <div class="row align-items-center mb-3">
                                                    <div class="col">
                                                        <!-- Title -->
                                                        <h4 class="card-title mb-2 mt-2">
                                                        휴대폰 번호                                                        </h4>  
                                                    </div>
                                                </div> 
                                                <div class="mb-3">
                                                    <div class="form-row">
                                                        <div class="form-group col-md-4 mb-3 mb-xl-0">
                                                            <select name="countryNumber" class="form-control input-border-grey" id="selectCountry">
<option value="852">HKG +852</option>
<option value="81">JPN +81</option>
<option value="82" selected="selected">KOR +82</option>
<option value="66">THA +66</option>
<option value="1">USA +1</option>
<option value="84">VNM +84</option>
<option value="86">CHN +86</option>
</select>
                                                        </div>
                                                        <div class="form-group col-md-8 mb-0">
                                                            <div class="input-group input-group-merge">
                                                                <input type="text" id="mobileNumber" name="mobileNumber" class="form-control form-control-appended input-border-grey" placeholder="모바일 번호를 입력해주세요." onkeyup="resetCheckMobile();">
                                                                <div class="input-group-append">
                                                                <button type="button" class="btn btn-primary" onclick="checkMobile();">Check</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col mt-1"><small id="checkMobileTxt" class="text-green"></small></div>
                                                    </div>
                                                </div>

                                                </div>

                                            
                                                <div class="row align-items-center mb-3">
                                                    <div class="col">
                                                    <!-- Title -->
                                                    <h4 class="card-title mb-2 mt-3">
                                                        수량                                                    </h4>  
                                                    </div>
                                                </div> 
                                                <div class="mb-3">
                                                    <div class="input-group">
                                                    <input type="text" id="amount" name="amount" class="form-control form-control-appended input-border-grey" placeholder="전송할 수량을 입력해주세요.">
                                                    </div>
                                                </div>    
                                            </div>
                                            <div class="row align-items-center text-right"> 
                                                <div class="col">
                                                  <button type="button" class="btn btn-primary" onclick="commGoAct();"><?=$coin?> 보내기</button> 
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            <!-- /발송 정보 -->
                        </div>
                        </form>
                    </div>

                    <!-- 받기 -->
                    <div role="tabpanel" id="tab2" class="tab-pane fade">
                        <div class="row">
                            <!-- 지갑 주소 -->
                            <div class="col-12 col-xl-6 mb-3">                      
                                <div class="card-inside card-tab">
                                    <div class="card-header">지갑정보</div> 
                                    <div class="card-body">
                                        <div class="mb-4">
                                            <div class="row align-items-center mb-3">
                                                <div class="col">
                                                    <!-- Title -->
                                                    <h4 class="card-title mb-2">
                                                    지갑 주소                                                    </h4>  
                                                </div>
                                            </div>                
                                            <div class="mb-3">
                                              <div class="input-group input-group-merge">
                                                <input type="text" class="form-control form-control-appended input-border-grey" id="btcWalletaddress" value="<?=$eth_address?>" readonly="">
                                                <div class="input-group-append">
                                                  <button type="button" class="btn-copy2 btn btn-primary" id="btcWalletaddress" data-clipboard-target="#btcWalletaddress" defval="">Copy</button>
                                                </div>
                                              </div>
                                            </div>
                                        
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /지갑 주소 -->

                            <!-- QR코드 -->
                            <div class="col-12 col-xl-6 mb-3">                      
                                <div class="card-inside card-tab">
                                    <div class="card-header">QR Code</div> 
                                    <div class="card-body">
                                        <div class="mb-4">
                                            <div class="row align-items-center mb-3">
                                                <div class="col text-center">
                                                    <img src="http://3.34.48.186/qrcode/genQrcode.php?str=<?=$eth_address?>" alt="qrcode" height="200" width="200" data-pagespeed-url-hash="1049162840" onload="pagespeed.CriticalImages.checkImageForCriticality(this);">
                                                </div>
                                            </div>                  
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /QR코드 -->
                        </div>
                    </div>
                </div>
            </div>
          </div>
        </div>
        <!-- /.row -->        
                
      </div>

       <!-- Modal -->
           <div class="modal fade" id="addressBook" role="document">
               <div class="modal-dialog modal-address">
                   <div class="modal-content">
                       <div class="modal-header border-color-grey">
                           <h3 class="modal-title font-weight-bold">Address Book</h3>
                           <button type="button" class="close" data-dismiss="modal">×</button>
                       </div>
                       <div class="modal-body">
                            <form name="addressFrm" method="post">
                                <input type="hidden" id="walletName" name="walletName" value="<?=$coin?>">
                                <div class="card border-color-grey">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="nickName">Name</label>
                                            <input type="text" class="form-control input-border-grey" id="nickName" name="nickName" placeholder="">
                                        </div>
                                        <div class="mb-4">
                                            <label for="adbook_walletAddress">Wallet Address</label>
                                            <input type="text" class="form-control input-border-grey" id="adbook_walletAddress" name="adbook_walletAddress" placeholder="">
                                        </div>
                                        <div class="row">
                                            <div class="col text-right"><button type="button" class="btn btn-primary" onclick="addAddressBook();">저장</button></div>
                                        </div>
                                    </div>
                                </div>
                             </form>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th class="table-middle">Name</th>
                                        <th>Wallet Address</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="addressBookTxt">

                                </tbody>
                            </table>
                        </div>
                       </div>
                       <div class="modal-footer border-color-grey">
                           <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                       </div>
                   </div>
               </div>
           </div>
         <!-- Modal End-->
          <!-- Modal -->
                        <div class="modal fade" id="pinNumberCheck" role="document">
                            <div class="modal-dialog modal-address">
                                <div class="modal-content">
                                    <div class="modal-header pin_header">
                                        <h4 class="modal-title">PIN 번호 확인</h4>
                                        <button type="button" class="close" data-dismiss="modal" onclick="resetPinNumber();">×</button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- PIN -->
                                        <div id="checkPinNumber">
                                            <div class="row">
                                                <div class="col-xl-12 col-sm-12">

                                                    <div class="number-input text-center text_white">
                                                        <strong>PIN 번호</strong>
                                                        <p>숫자 4자리를 입력해주세요.</p>
                                                        <div class="input_wrap">
                                                        <form id="pinForm" name="pinForm" method="post">
                                                            <input type="password" class="pin_pw" id="pinNumber" name="pinNumber" value="" disabled="">
                                                        </form>
                                                        </div>
                                                    </div>

                                                    <ul class="number-btn">
                                                        <li>
                                                            <a id="pinNumber_0" href="javascript:inputPinNumber('1');">1</a>
                                                        </li>
                                                        <li>
                                                            <a id="pinNumber_1" href="javascript:inputPinNumber('2');">2</a>
                                                        </li>
                                                        <li>
                                                            <a id="pinNumber_2" href="javascript:inputPinNumber('3');">3</a>
                                                        </li>
                                                        <li>
                                                            <a id="pinNumber_3" href="javascript:inputPinNumber('4');">4</a>
                                                        </li>
                                                        <li>
                                                            <a id="pinNumber_4" href="javascript:inputPinNumber('5');">5</a>
                                                        </li>
                                                        <li>
                                                            <a id="pinNumber_5" href="javascript:inputPinNumber('6');">6</a>
                                                        </li>
                                                        <li>
                                                            <a id="pinNumber_6" href="javascript:inputPinNumber('7');">7</a>
                                                        </li>
                                                        <li>
                                                            <a id="pinNumber_7" href="javascript:inputPinNumber('8');">8</a>
                                                        </li>
                                                        <li>
                                                            <a id="pinNumber_8" href="javascript:inputPinNumber('9');">9</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:resetPinNumber();">
                                                                <i class="fa fa-eraser"></i>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a id="pinNumber_9" href="javascript:inputPinNumber('0');">0</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:removeLastPinNumber();">
                                                                <i class="fa fa-backspace"></i>
                                                            </a>
                                                        </li>

                                                    </ul>

                                                </div>
                                            </div>

                                            <!-- PIN -->

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Modal End-->

    </div> <!-- / .main-content -->
    <footer>
    </footer>

    
    <script src="/inc/reformUser/assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="/inc/reformUser/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/inc/reformUser/assets/libs/highlightjs/highlight.pack.min.js"></script>
    <script src="/inc/reformUser/assets/libs/jquery-mask-plugin/dist/jquery.mask.min.js"></script>
    <script src="/inc/reformUser/assets/libs/quill/dist/quill.min.js"></script>
    <script src="/inc/reformUser/assets/libs/dropzone/dist/min/dropzone.min.js"></script>
    <script src="/inc/reformUser/assets/libs/select2/dist/js/select2.min.js"></script>

    

  
</div></body></html>