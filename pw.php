<html lang="kor"><head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>Forgot Password</title>
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
  <script src="/inc/user/build/js/common.js"></script>
</head>

<body>

  
<script type="text/javascript">var setTime;var time_flag=false;var auth_flag=false;function learnMorePop(){$("#learMore").modal();}function contactConfirmCheck(){var joinFrm=document.joinFrm;if(!joinFrm.memberNumber.value){alert("모바일번호를 확인해주세요.");joinFrm.memberNumber.focus();return false;}return true;}function sendAuthCode(){var hiddenFrm=document.hiddenFrm;var joinFrm=document.joinFrm;if($('#isDuplicateCheck').val()!='1'){alert("아이디를 먼저 체크해주세요.");return false;}$('#isAuthCheck').val('0');$('#countryNumber').val('');$('#memberMobile').val('');$('#certifyNum').val('');if(!contactConfirmCheck()){return;}$.ajax({type:'post',url:'/common/Sms/ajaxReWriteSendAuthCode',data:{memberId:joinFrm.memberId.value,countryNumber:joinFrm.selectCountry.value,mobileNumber:joinFrm.memberNumber.value,replyNumber:hiddenFrm.replyNumber.value,},cache:false,async:false,dataType:'json',success:function(data){if(data.isResult==true){console.log(data.message);alert("인증번호가 전송되었습니다.");$("#certifyNumEnc").val(data.data.confrmCode);if(setTime!=''){clearInterval(setTime);}time_flag=true;$('#countdownText').html('');$('#countdownTimer').html('');startTimer(180,'#countdownTimer');$('#countdownTimer').show();$('#authMobileCheck').attr('disabled',false);$('#authMobileCheck').val('');$('#authConfirmBtn').attr('disabled',false);return true;}else{console.log(data.message);if(data.data.message){alert(data.data.message);joinFrm.memberNumber.focus();return false;}}}});}function checkAuthCode(){var joinFrm=document.joinFrm;var hiddenFrm=document.hiddenFrm;if(!auth_flag){if(time_flag){$.ajax({type:'post',url:'/common/Sms/ajaxCheckAuthCode',data:{certifyNum:joinFrm.authMobileCheck.value,certifyNumEnc:joinFrm.certifyNumEnc.value},cache:false,async:false,dataType:'json',success:function(data){if(data.isResult==true){alert(data.data.message);joinFrm.authMobileCheck.focus();$('#countryNumber').val(joinFrm.selectCountry.value);$('#memberMobile').val(joinFrm.memberNumber.value);$('#certifyNum').val(joinFrm.authMobileCheck.value);$('#isAuthCheck').val('1');$('#countdownTimer').hide();$('#countdownText').html(data.data.message).removeClass('time-over-red').addClass('time-over-green');$('#memberNumber').attr('disabled',true);$('#authSendBtn').attr('disabled',true);$('#authMobileCheck').attr('disabled',true);$('#authConfirmBtn').attr('disabled',true);$('#selectCountry').attr('disabled',true);clearInterval(setTime);time_flag=false;auth_flag=true;}else{alert(data.data.message);joinFrm.authMobileCheck.focus();}}});}}}function startTimer(duration,callback_selector){var timer=duration,minutes=0,seconds=0;var joinFrm=document.joinFrm;setTime=setInterval(function(){minutes=parseInt(timer/60,10);seconds=parseInt(timer%60,10);minutes=minutes<10?"0"+minutes:minutes;seconds=seconds<10?"0"+seconds:seconds;var str="(Time remaining "+minutes+":"+seconds+")";$(callback_selector).html(str);$('#authMobileCheck').show();$('#authConfirmBtn').show();if(--timer<0){alert("입력시간이 초과되었습니다.");clearInterval(setTime);$('#countdownText').html("인증 번호 입력시간이 초과되었습니다. <br> 다시 인증해주세요.").removeClass('time-over-green').addClass('time-over-red');$('#countdownTimer').html('');$('#authConfirmBtn').hide();$('#authMobileCheck').hide();time_flag=false;return;}},1000);}function checkId(){var joinFrm=document.joinFrm;var hiddenFrm=document.hiddenFrm;$.ajax({type:'post',url:'/Auth/ajaxExistId',data:{memberId:joinFrm.memberId.value},cache:false,async:false,dataType:'json',success:function(data){if(data.isResult==true){$('#isDuplicateCheck').val('1');$('#idCheckTxt').html(data.data.message).removeClass('id-over-red').addClass('id-over-green');joinFrm.memberName.focus();}else{$('#idCheckTxt').html(data.data.message).removeClass('id-over-green').addClass('id-over-red');joinFrm.memberId.focus();}}});}function resetCheckId(){$('#isDuplicateCheck').val('0');$('#idCheckTxt').html("사용가능한 아이디인지 확인해주세요. (확인 버튼 누르기)").removeClass('id-over-green').addClass('id-over-red');clearInterval(setTime);$('#countryNumber').val('');$('#memberMobile').val('');$('#certifyNum').val('');if($('#isAuthCheck').val()=='1'){$('#isAuthCheck').val('0');$('#countdownText').html("ID 정보가 변경되었습니다. <br> 다시 인증해 주십시요.").removeClass('time-over-green').addClass('time-over-red');$('#memberNumber').attr('disabled',false);$('#authSendBtn').attr('disabled',false);$('#authMobileCheck').attr('disabled',false);$('#authConfirmBtn').attr('disabled',false);$('#selectCountry').attr('disabled',false);$('#countdownTimer').html('');$('#authConfirmBtn').hide();$('#authMobileCheck').hide();time_flag=true;auth_flag=false;}}function commGoAct(){var frm=document.joinFrm;var mode=frm.mode.value;var agreeCheck=$("input[name='agreeCheck']");var checkPass=false;if(comInputCheck('text','memberId')===true){alert("아이디를 입력해주세요.");$('#memberId').focus();return false;}if(comInputCheck('text','memberNumber')===true){alert("모바일번호를 확인해주세요.");$('#memberNumber').focus();return false;}if(comInputCheck('text','memberPw')===true){alert("비밀번호를 입력해주세요.");$('#memberPw').focus();return false;}$.ajax({type:'post',url:'/common/SendCheck/ajaxCheckPassword',data:{password:joinFrm.memberPw.value},cache:false,async:false,dataType:'json',success:function(data){if(data.isResult==true){checkPass=true;}else{alert(data.data.message);checkPass=false;}}});if(checkPass==false){$('#memberPw').focus();return false;}if(comInputCheck('text','confirmMemberPw')===true){alert("비밀번호가 비밀번호 확인과 불일치 합니다.");$('#confirmMemberPw').focus();return false;}if($('#memberPw').val()!=$('#confirmMemberPw').val()){alert("비밀번호가 비밀번호 확인과 불일치 합니다.");$('#confirmMemberPw').focus();return false;}if($('#isDuplicateCheck').val()!='1'){alert("사용가능한 아이디인지 확인해주세요.");return false;}if($('#isAuthCheck').val()!='1'){alert("모바일 인증을 진행해주세요.");return false;}if(confirm("저장하시겠습니까?")){frm.action="/Auth/actMemberPwWrite";frm.submit();}else{return false;}}function langChange(){var selectValue=document.getElementById('langSelect').value;window.location.href="/Auth/setLanguage?country="+selectValue;return true;}</script>


  <div class="bg_image2 hv-100" style="background:url(/inc/reformUser/build/img/userAuthBg.png?v=2) no-repeat center;background-size:cover">
      <div class="register_wrap">
          <div class="card-body">

          	<div class="lr_icon text-center">
          	<script data-pagespeed-no-defer="">//<![CDATA[
(function(){for(var g="function"==typeof Object.defineProperties?Object.defineProperty:function(b,c,a){if(a.get||a.set)throw new TypeError("ES3 does not support getters and setters.");b!=Array.prototype&&b!=Object.prototype&&(b[c]=a.value)},h="undefined"!=typeof window&&window===this?this:"undefined"!=typeof global&&null!=global?global:this,k=["String","prototype","repeat"],l=0;l<k.length-1;l++){var m=k[l];m in h||(h[m]={});h=h[m]}var n=k[k.length-1],p=h[n],q=p?p:function(b){var c;if(null==this)throw new TypeError("The 'this' value for String.prototype.repeat must not be null or undefined");c=this+"";if(0>b||1342177279<b)throw new RangeError("Invalid count value");b|=0;for(var a="";b;)if(b&1&&(a+=c),b>>>=1)c+=c;return a};q!=p&&null!=q&&g(h,n,{configurable:!0,writable:!0,value:q});var t=this;function u(b,c){var a=b.split("."),d=t;a[0]in d||!d.execScript||d.execScript("var "+a[0]);for(var e;a.length&&(e=a.shift());)a.length||void 0===c?d[e]?d=d[e]:d=d[e]={}:d[e]=c};function v(b){var c=b.length;if(0<c){for(var a=Array(c),d=0;d<c;d++)a[d]=b[d];return a}return[]};function w(b){var c=window;if(c.addEventListener)c.addEventListener("load",b,!1);else if(c.attachEvent)c.attachEvent("onload",b);else{var a=c.onload;c.onload=function(){b.call(this);a&&a.call(this)}}};var x;function y(b,c,a,d,e){this.h=b;this.j=c;this.l=a;this.f=e;this.g={height:window.innerHeight||document.documentElement.clientHeight||document.body.clientHeight,width:window.innerWidth||document.documentElement.clientWidth||document.body.clientWidth};this.i=d;this.b={};this.a=[];this.c={}}function z(b,c){var a,d,e=c.getAttribute("data-pagespeed-url-hash");if(a=e&&!(e in b.c))if(0>=c.offsetWidth&&0>=c.offsetHeight)a=!1;else{d=c.getBoundingClientRect();var f=document.body;a=d.top+("pageYOffset"in window?window.pageYOffset:(document.documentElement||f.parentNode||f).scrollTop);d=d.left+("pageXOffset"in window?window.pageXOffset:(document.documentElement||f.parentNode||f).scrollLeft);f=a.toString()+","+d;b.b.hasOwnProperty(f)?a=!1:(b.b[f]=!0,a=a<=b.g.height&&d<=b.g.width)}a&&(b.a.push(e),b.c[e]=!0)}y.prototype.checkImageForCriticality=function(b){b.getBoundingClientRect&&z(this,b)};u("pagespeed.CriticalImages.checkImageForCriticality",function(b){x.checkImageForCriticality(b)});u("pagespeed.CriticalImages.checkCriticalImages",function(){A(x)});function A(b){b.b={};for(var c=["IMG","INPUT"],a=[],d=0;d<c.length;++d)a=a.concat(v(document.getElementsByTagName(c[d])));if(a.length&&a[0].getBoundingClientRect){for(d=0;c=a[d];++d)z(b,c);a="oh="+b.l;b.f&&(a+="&n="+b.f);if(c=!!b.a.length)for(a+="&ci="+encodeURIComponent(b.a[0]),d=1;d<b.a.length;++d){var e=","+encodeURIComponent(b.a[d]);131072>=a.length+e.length&&(a+=e)}b.i&&(e="&rd="+encodeURIComponent(JSON.stringify(B())),131072>=a.length+e.length&&(a+=e),c=!0);C=a;if(c){d=b.h;b=b.j;var f;if(window.XMLHttpRequest)f=new XMLHttpRequest;else if(window.ActiveXObject)try{f=new ActiveXObject("Msxml2.XMLHTTP")}catch(r){try{f=new ActiveXObject("Microsoft.XMLHTTP")}catch(D){}}f&&(f.open("POST",d+(-1==d.indexOf("?")?"?":"&")+"url="+encodeURIComponent(b)),f.setRequestHeader("Content-Type","application/x-www-form-urlencoded"),f.send(a))}}}function B(){var b={},c;c=document.getElementsByTagName("IMG");if(!c.length)return{};var a=c[0];if(!("naturalWidth"in a&&"naturalHeight"in a))return{};for(var d=0;a=c[d];++d){var e=a.getAttribute("data-pagespeed-url-hash");e&&(!(e in b)&&0<a.width&&0<a.height&&0<a.naturalWidth&&0<a.naturalHeight||e in b&&a.width>=b[e].o&&a.height>=b[e].m)&&(b[e]={rw:a.width,rh:a.height,ow:a.naturalWidth,oh:a.naturalHeight})}return b}var C="";u("pagespeed.CriticalImages.getBeaconData",function(){return C});u("pagespeed.CriticalImages.Run",function(b,c,a,d,e,f){var r=new y(b,c,a,e,f);x=r;d&&w(function(){window.setTimeout(function(){A(r)},0)})});})();pagespeed.CriticalImages.Run('/mod_pagespeed_beacon','https://new.likrwallet.com/Auth/memberPwWrite','8Xxa2XQLv9',true,false,'c_6vYzFNJ20');
//]]></script><img src="/inc/user/build/images/logo/logo.png?v=3" alt="logo" data-pagespeed-url-hash="540885933" onload="pagespeed.CriticalImages.checkImageForCriticality(this);">
            </div>
            <h5 class="my-4 text-center text-uppercase bold">비밀번호 재설정</h5>

            <form name="hiddenFrm" method="post">
                <input type="hidden" name="replyNumber" id="replyNumber" value="12176892800">
                <input type="hidden" name="isAuthCheck" id="isAuthCheck" value="0">
                <input type="hidden" name="isDuplicateCheck" id="isDuplicateCheck" value="0">
            </form>

            <form name="joinFrm" method="post">
            <input type="hidden" name="mode" value="upd">
            <input type="hidden" id="countryNumber" name="countryNumber" value="">
            <input type="hidden" id="memberMobile" name="memberMobile" value="">
            <input type="hidden" id="certifyNum" name="certifyNum" value="">
            <input type="hidden" id="certifyNumEnc" name="certifyNumEnc" value="">

            <div class="form-group-id">
                   <label for="exampleInputEmail1" class="bold label-name">아이디</label>
                   <div class="append_wrap">
                      <input type="email" id="memberId" name="memberId" class="form-control-yj2" placeholder="이메일 (ex ***@google.com)" onkeyup="resetCheckId();">
                      <div class="btn-append">
                          <button type="button" class="send-button" onclick="checkId();">확인</button>
                      </div>
                      <div id="idCheckTxt" class="id-over-red smaller"></div>
                   </div>
               </div>

              <div class="form-group">
                  <label for="old-password" class="bold label-name">휴대폰 번호</label>
                  <select name="selectCountry" class="yj_select" id="selectCountry">
<option value="852">HKG +852</option>
<option value="66">THA +66</option>
<option value="81">JPN +81</option>
<option value="82" selected="selected">KOR +82</option>
<option value="1">USA +1</option>
<option value="84">VNM +84</option>
<option value="86">CHN +86</option>
</select>
                   
                </div>
                <div class="form-group">
                    <div class="append_wrap">
                       <input type="tel" name="memberNumber" id="memberNumber" class="form-control-yj2" placeholder="휴대폰 번호 ( '-'없이 )" value="">
                       <div class="btn-append">
                           <button type="button" id="authSendBtn" class="send-button" onclick="sendAuthCode();">인증번호 요청</button>
                       </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="append_wrap">
                       <input type="text" name="certifyNum" id="authMobileCheck" class="form-control-yj2" placeholder="인증번호" value="" style="display:none">
                       <span class="time-left" id="countdownTimer"></span>
                       <div class="btn-append">
                           <button type="button" id="authConfirmBtn" class="send-button" onclick="checkAuthCode();" style="display:none">완료</button>
                       </div>
                    </div>
                    <div id="countdownText" class="time-over smaller"></div>
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1" class="bold label-name">새로운 비밀번호 <small class="text-danger"> ( 8자이상, 특수문자포함, 숫자포함 )</small></label>
                    <input class="form-control-yj" type="password" name="memberPw" id="memberPw" placeholder="비밀번호">
                </div>
                <div class="form-group">
                    <input class="form-control-yj" type="password" name="confirmMemberPw" id="confirmMemberPw" placeholder="비밀번호 확인">
                </div>
                <div class="form-group">
                
                </div>
                <button type="button" class="btn btn-block btn-login" onclick="commGoAct();">비밀번호 재설정</button>
            </form>
            <div class="text-center mt-3">
              <a class="d-block small" href="/"><i class="icon-arrow-left-circle icons"></i> 로그인으로 돌아가기</a>
            </div>

              <!-- language select -->
              <div class="row">
                  <div class="col language-select-wrapper d-flex justify-content-center">
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

          </div>

        </div>
           
  <!-- Bootstrap core JavaScript-->
    <script src="/inc/user/template/vendor/jquery/jquery.min.js"></script>
    <script src="/inc/user/template/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Core plugin JavaScript-->
    <script src="/inc/user/template/vendor/jquery-easing/jquery.easing.min.js"></script>
    <!-- Custom scripts for all pages-->
    <script src="/inc/user/template/js/admin.js"></script>



</div></body></html>