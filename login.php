<?php
include_once('_common.php');

session_start();
if(isset($_SESSION["ss_member_id"])){
    header('Location:/main.php');
}

$r = '';
if(isset($_GET["r"])){
    $r = $_GET["r"];
}
?>
<!DOCTYPE html>
<html lang="kor"><head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>LogIn</title>     
  <!-- Favicon Icon -->
  <link rel="shortcut icon" type="image/x-icon" href="/inc/user/template/images/favicon.png">
  <!-- Bootstrap core CSS-->
  <link href="/inc/user/template/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom fonts for this template-->
  <link href="/inc/user/template/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css?family=Noto+Sans+KR" rel="stylesheet">
  <!-- Icon Fonts-->
  <link href="/inc/user/template/vendor/webfont/css/cryptocoins.css" rel="stylesheet" type="text/css">
  <link href="/inc/user/template/vendor/webfont/css/simple-line-icons.css" rel="stylesheet" type="text/css">
  <!-- Custom styles for this template-->
  <link href="/inc/user/template/css/admin.css" rel="stylesheet">  
  <!-- Build -->   
  <link href="/inc/user/build/css/buildyj.css" rel="stylesheet">

  <link href="/inc/user/template/css/popup.css" rel="stylesheet">  

  <!-- Bootstrap core JavaScript-->
  <script src="/inc/user/template/vendor/jquery/jquery.min.js"></script>
  <script src="/inc/user/template/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- Core plugin JavaScript-->
  <script src="/inc/user/template/vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="/inc/user/build/js/common.js"></script>

<script type="text/javascript">
function enterkey(){if(window.event.keyCode==13){commGoAct();}}
function ignoreEnter(event){if(event.keyCode==13){otpCheck();return false;}}
function commGoAct(){
    var frm=document.loginFrm;
    var checkOtp=false;
    var checkIdPw=false;
    if(comInputCheck('text','memberId')===true){
        alert("아이디를 입력해주세요.");
        $('#memberId').focus();
        return false;
    }
    if(comInputCheck('text','memberPw')===true){
        alert("비밀번호를 입력해주세요.");
        $('#memberPw').focus();
        return false;
    }
    // if($('#isUsedOtp').val()=='1'){
    //     $.ajax({
    //         type:'post',
    //         url:'/common/GoogleOtp/ajaxCheckIdPw',
    //         data:{
    //             mId:frm.memberId.value,mPassword:frm.memberPw.value
    //         },
    //         cache:false,
    //         async:false,
    //         dataType:'json',
    //         success:function(data){
    //             if(data.isResult==true){
    //                 checkOtp=data.data.isCheckOtp;
    //                 checkIdPw=true;
    //                 return true;
    //             }
    //             else{
    //                 alert(data.data.message);
    //                 return false;
    //             }
    //         }
    //     });
    //     if(checkIdPw==false){
    //         return false;
    //     }
    // }
    // if(checkOtp==true){
    //     $("#googleOtpCheck").modal();
    //     return true;
    // }
        // frm.action="/Auth/actLogin";
    frm.submit();
}
function otpCheck(){var loginFrm=document.loginFrm;var otpFrm=document.otpFrm;var isCorrectNumber=false;$.ajax({type:'post',url:'/common/GoogleOtp/ajaxCheckIdAndOtpInfo',data:{mId:loginFrm.memberId.value,otpNumber:otpFrm.otpNumber.value},cache:false,async:false,dataType:'json',success:function(data){if(data.isResult==true){$('#otpTxt').html("");$('#otpValue').val(otpFrm.otpNumber.value);alert(data.data.message);loginFrm.action="/Auth/actLogin";loginFrm.submit();return false;}else{$('#otpTxt').html(data.data.message);otpFrm.otpNumber.focus();return false;}}});}function langChange(){var selectValue=document.getElementById('langSelect').value;window.location.href="/Auth/setLanguage?country="+selectValue;return true;}</script></head>



<body>

  <div class="bg_login" style="background:url('/inc/reformUser/build/img/userAuthBg.png?v=2') no-repeat center; background-size:cover;">
    
        <div class="sign_wrap col-12 py-4">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-md-6 pt-1  position-relative">
                        <div class="position-center d-none d-md-block">
                            <div class="">
                                <!-- <script data-pagespeed-no-defer="">//<![CDATA[
(function(){for(var g="function"==typeof Object.defineProperties?Object.defineProperty:function(b,c,a){if(a.get||a.set)throw new TypeError("ES3 does not support getters and setters.");b!=Array.prototype&&b!=Object.prototype&&(b[c]=a.value)},h="undefined"!=typeof window&&window===this?this:"undefined"!=typeof global&&null!=global?global:this,k=["String","prototype","repeat"],l=0;l<k.length-1;l++){var m=k[l];m in h||(h[m]={});h=h[m]}var n=k[k.length-1],p=h[n],q=p?p:function(b){var c;if(null==this)throw new TypeError("The 'this' value for String.prototype.repeat must not be null or undefined");c=this+"";if(0>b||1342177279<b)throw new RangeError("Invalid count value");b|=0;for(var a="";b;)if(b&1&&(a+=c),b>>>=1)c+=c;return a};q!=p&&null!=q&&g(h,n,{configurable:!0,writable:!0,value:q});var t=this;function u(b,c){var a=b.split("."),d=t;a[0]in d||!d.execScript||d.execScript("var "+a[0]);for(var e;a.length&&(e=a.shift());)a.length||void 0===c?d[e]?d=d[e]:d=d[e]={}:d[e]=c};function v(b){var c=b.length;if(0<c){for(var a=Array(c),d=0;d<c;d++)a[d]=b[d];return a}return[]};function w(b){var c=window;if(c.addEventListener)c.addEventListener("load",b,!1);else if(c.attachEvent)c.attachEvent("onload",b);else{var a=c.onload;c.onload=function(){b.call(this);a&&a.call(this)}}};var x;function y(b,c,a,d,e){this.h=b;this.j=c;this.l=a;this.f=e;this.g={height:window.innerHeight||document.documentElement.clientHeight||document.body.clientHeight,width:window.innerWidth||document.documentElement.clientWidth||document.body.clientWidth};this.i=d;this.b={};this.a=[];this.c={}}function z(b,c){var a,d,e=c.getAttribute("data-pagespeed-url-hash");if(a=e&&!(e in b.c))if(0>=c.offsetWidth&&0>=c.offsetHeight)a=!1;else{d=c.getBoundingClientRect();var f=document.body;a=d.top+("pageYOffset"in window?window.pageYOffset:(document.documentElement||f.parentNode||f).scrollTop);d=d.left+("pageXOffset"in window?window.pageXOffset:(document.documentElement||f.parentNode||f).scrollLeft);f=a.toString()+","+d;b.b.hasOwnProperty(f)?a=!1:(b.b[f]=!0,a=a<=b.g.height&&d<=b.g.width)}a&&(b.a.push(e),b.c[e]=!0)}y.prototype.checkImageForCriticality=function(b){b.getBoundingClientRect&&z(this,b)};u("pagespeed.CriticalImages.checkImageForCriticality",function(b){x.checkImageForCriticality(b)});u("pagespeed.CriticalImages.checkCriticalImages",function(){A(x)});function A(b){b.b={};for(var c=["IMG","INPUT"],a=[],d=0;d<c.length;++d)a=a.concat(v(document.getElementsByTagName(c[d])));if(a.length&&a[0].getBoundingClientRect){for(d=0;c=a[d];++d)z(b,c);a="oh="+b.l;b.f&&(a+="&n="+b.f);if(c=!!b.a.length)for(a+="&ci="+encodeURIComponent(b.a[0]),d=1;d<b.a.length;++d){var e=","+encodeURIComponent(b.a[d]);131072>=a.length+e.length&&(a+=e)}b.i&&(e="&rd="+encodeURIComponent(JSON.stringify(B())),131072>=a.length+e.length&&(a+=e),c=!0);C=a;if(c){d=b.h;b=b.j;var f;if(window.XMLHttpRequest)f=new XMLHttpRequest;else if(window.ActiveXObject)try{f=new ActiveXObject("Msxml2.XMLHTTP")}catch(r){try{f=new ActiveXObject("Microsoft.XMLHTTP")}catch(D){}}f&&(f.open("POST",d+(-1==d.indexOf("?")?"?":"&")+"url="+encodeURIComponent(b)),f.setRequestHeader("Content-Type","application/x-www-form-urlencoded"),f.send(a))}}}function B(){var b={},c;c=document.getElementsByTagName("IMG");if(!c.length)return{};var a=c[0];if(!("naturalWidth"in a&&"naturalHeight"in a))return{};for(var d=0;a=c[d];++d){var e=a.getAttribute("data-pagespeed-url-hash");e&&(!(e in b)&&0<a.width&&0<a.height&&0<a.naturalWidth&&0<a.naturalHeight||e in b&&a.width>=b[e].o&&a.height>=b[e].m)&&(b[e]={rw:a.width,rh:a.height,ow:a.naturalWidth,oh:a.naturalHeight})}return b}var C="";u("pagespeed.CriticalImages.getBeaconData",function(){return C});u("pagespeed.CriticalImages.Run",function(b,c,a,d,e,f){var r=new y(b,c,a,e,f);x=r;d&&w(function(){window.setTimeout(function(){A(r)},0)})});})();pagespeed.CriticalImages.Run('/mod_pagespeed_beacon','https://new.likrwallet.com/','8Xxa2XQLv9',true,false,'3LxxbMwWReA');
//]]></script> -->
<img src="/inc/user/build/images/logo/logo.png?v=3" alt="logo" style="width:20rem;" class="" data-pagespeed-url-hash="540885933">
                                
                                <h6 class="white my-md-3 mt-3 mt-md-0 text-center">Blockchain Commerceware</h6>
                            </div>
                        </div>
                        <div class="d-block d-md-none">
                            <div class="text-center">
                                <img src="/inc/user/build/images/logo/logo.png?v=3" alt="logo" style="width:14rem; padding-top:1rem;" data-pagespeed-url-hash="540885933">
                                <h6 class="white my-md-3 mt-2 mt-md-0">Blockchain Commerceware</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 px-6">
                        <div class="card-body p-0 p-md-5">
                            
                            <h2 class="white m-0 d-none d-md-block">Log-IN</h2>
                            <form name="loginFrm" method="post" action="/ajax/ajax.member.php">
                            <input type="hidden" name="type" value="login">
                            <input type="hidden" name="isUsedOtp" id="isUsedOtp" value="1">
                            <input type="hidden" name="otpValue" id="otpValue" value="">
                            <div class="form-group mt-2">
                                <input class="form-control-yj" name="memberId" id="memberId" type="text" placeholder="아이디" autocomplete="off">
                            </div>
                            <div class="form-group">
                                <input class="form-control-yj" name="memberPw" id="memberPw" type="password" placeholder="비밀번호" onkeyup="enterkey();">
                            </div>
                            <button type="button" class="btn btn-block bg-likrpink white" onclick="commGoAct();">로그인</button>
                            </form>
                            <div class="text-center mt-3 justify-content-between d-flex">
                            <!-- <a class="register small white" href="#" onclick="alert('서비스 점검 중입니다.')">회원가입</a> -->
                            <a class="register small white" href="/join.php">회원가입</a>
                            <a class="find-password small white" href="/pw.php">비밀번호 찾기</a>
                            </div>

                            
                            
                            

                            <!-- language select -->
                            <div class="row">
                                <div class="col language-select-wrapper d-flex justify-content-center white">
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
                                </div>
                            </div>
                            <!-- /language select -->

                            <!-- <div class="d-flex mt-4 justify-content-center">
                                <a href="https://play.google.com/store/apps/details?id=kr.likr.wallet.android" class="d-inline-block ml-2"><img src="/inc/user/build/images/icon/google-play-badge.svg" height="40" data-pagespeed-url-hash="3408597403" onload="pagespeed.CriticalImages.checkImageForCriticality(this);"></a>
                            </div> -->
                                        
                        </div>
                    </div>
                </div>
            </div>

  </div>

  <div>
    <!-- Modal -->
    <div class="modal fade v-align-center" id="googleOtpCheck" role="document">
        <div class="modal-dialog modal-otp">
            <div class="modal-content">
                <div class="border-bottom border-color-grey align-items-center py-3">
                    <h4 class="modal-title text-center font-weight-bold">구글 OTP 인증</h4>  
                </div>
                <div class="modal-body">
                    <form name="otpFrm" method="post">
                        <div class="form-group-id mt-3">
                            <div class="append_wrap">
                                <input type="text" id="otpNumber" name="otpNumber" class="form-control" placeholder="OTP번호를 입력해주세요." autocomplete="off" onkeydown="return ignoreEnter(event);">
                                <div id="otpTxt" class="otp-text-red smaller"></div>                                        
                            </div>
                        </div>
                        <div class="mb-3">
                            <button id="otpCheckBtn" type="button" class="btn btn-block btn-login" onclick="otpCheck();">확인</button>
                        </div>
                        </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal End-->




</div></div>
<script>
$(document).ready(function(){
    var r = '<?=$r?>';
    if(r.length > 0){
        if(r == 'success'){
            alert("회원가입에 성공했습니다.");
            location.href = '/login.php';
        }
        else if(r == 'loggedout'){
            alert("로그아웃 되었습니다.");
            location.href = '/login.php';
        }
    }
});
</script>
</body></html>