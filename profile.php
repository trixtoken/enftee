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
$creawted_at = $row["created_at"];

$frim = $row["frim"];
$eth = $row["eth"];
$game_money = $row["game_money"];

$r = '';
if(isset($_GET["r"])){
    $r = $_GET["r"];
}
?>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Favion -->
    

    <!-- Libs CSS -->
    <link rel="stylesheet" href="/inc/reformUser/assets/fonts/feather/feather.min.css">
    <link rel="stylesheet" href="/inc/reformUser/assets/libs/highlight.js/styles/vs2015.css">
    <link rel="stylesheet" href="/inc/reformUser/assets/libs/quill/dist/quill.core.css">
    <link rel="stylesheet" href="/inc/reformUser/assets/libs/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="/inc/reformUser/assets/libs/flatpickr/dist/A.flatpickr.min.css">

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
   
    <title>MDL Wallet</title>
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
            
<img src="/inc/user/build/images/logo/logo.png?v=3" class="navbar-brand-img mx-auto" alt="logo">
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
                  <i class="nav-icon wallet "></i><span class="textLITE pr-1">MDL</span> 지갑                </a>
                <div class="collapse " id="sidebarEthToken1">
                  <ul class="nav nav-sm flex-column">
                    <li class="nav-item">
                      <a href="/transaction.php?coin=MDL" class="nav-link ">
                        <span class="textToken1 pr-1">MDL</span> 보내기 &amp; 받기                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="/history.php?coin=MDL" class="nav-link ">
                        <span class="textToken1 pr-1">MDL</span> 거래 목록                      </a>
                    </li>
                    <!-- <li class="nav-item">
                      <a href="/transfer.php?coin=MDL" class="nav-link ">
                        <span class="textToken1 pr-1">MDL</span> 환전                      </a>
                    </li> -->
                  </ul>
                </div>
              </li>
              <!-- 토큰지갑1 종료-->
             
              
              
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
                <a class="nav-link lnb_title_active" href="/profile.php">
                  <i class="nav-icon user profile_icon_active"></i>내 프로필                </a>
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
  <script type="text/javascript">$(document).ready(function(){$(".btn-copy2").each(function(){var n="#"+$(this).attr('id');var defval=$(this).attr('defval');console.log(n);console.log(defval);new Clipboard(n).on('success',function(e){toastr.info(e.text,'Copied',{"closeButton":true,"progressBar":false,"timeOut":"3000","positionClass":"toast-bottom-right",});$(e.trigger).attr('title','Copied').tooltip('show');}).on('error',function(e){console.error('Action:',e.action);console.error('Trigger:',e.trigger);window.prompt('Copy with Ctrl+C',defval);});});});function ignorePwEnter(event){if(event.keyCode==13){checkModalPw();return false;}}function ignoreNowOtpEnter(event){if(event.keyCode==13){checkNowOtpNumber();return false;}}function ignoreNewOtpEnter(event){if(event.keyCode==13){checkNewOtpNumber();return false;}}
  function commGoAct(mode){
      if(mode=="password"){
          var pwFrm=document.pwWriteFrm;
          var newPwCheck = pwFrm.newPwCheck.value;
          var newPw = pwFrm.newPw.value;

          if(newPwCheck != newPw){
              alert("비밀번호와 비밀번호확인이 일치하지 않습니다.");
              return false;
          }
          if(confirm("저장하시겠습니까?")){
              pwFrm.action="/ajax/ajax.member.php";
              pwFrm.submit();
            }
            else{
                return false;
            }
        }
        else if(mode=="pin"){
            var pinFrm=document.pinWriteFrm;
            if(confirm("저장하시겠습니까?")){
                pinFrm.action="/Main/actMyProfilePinWrite";
                pinFrm.submit();
            }
            else{
                return false;
            }
        }
    }
    function popOtpMethod(){$("#useOtpMethodModal").modal();}function saveOtpUsable(){$("#selectNextPg").val('checkOptUsable');$("#modalPw").val('');$("#modalPwTxt").html('');$("#checkPwModal").modal();}function popCurrentOtpInfo(){$("#selectNextPg").val('currentOtpInfo');$("#modalPw").val('');$("#modalPwTxt").html('');$("#checkPwModal").modal();}function popNewOtpInfo(){$("#selectNextPg").val('newOtpInfo');$("#modalPw").val('');$("#modalPwTxt").html('');$("#checkPwModal").modal();}function checkModalPw(){var otpFrm=document.otpWriteFrm;var mIdx=$("#mIdx").val();var isPassword=false;$.ajax({type:'post',url:'/common/GoogleOtp/ajaxCheckPw',data:{mIdx:mIdx,mPassword:modalPwFrm.modalPw.value},cache:false,async:false,dataType:'json',success:function(data){if(data.isResult==true){isPassword=true;$("#modalPwTxt").html('');return true;}else{isPassword=false;$("#modalPwTxt").html(data.data.message);return false;}}});if(isPassword==true){if($("#selectNextPg").val()=='checkOptUsable'){if(otpFrm.googleOtpUsed.value=="1"){$.ajax({type:'post',url:'/common/GoogleOtp/ajaxCheckExistOtpKey',data:{mIdx:mIdx},cache:false,async:false,dataType:'json',success:function(data){if(data.isResult==true){if(data.data.isExist==true){$('#checkPwModal').modal('hide');$("#nowOtpNumber").val('');$('#nowOtpNumberTxt').html("");$("#checkOtpModal").modal();return true;}else{$('#checkPwModal').modal('hide');setNewOtpPop();$("#newOtpModal").modal();return true;}}}});}else if(otpFrm.googleOtpUsed.value=="2"){setOtpUsable();return true;}}else if($("#selectNextPg").val()=='currentOtpInfo'){$('#checkPwModal').modal('hide');setCurrentOtpPop();$("#currentOtpModal").modal();return true;}else if($("#selectNextPg").val()=='newOtpInfo'){$('#checkPwModal').modal('hide');setNewOtpPop();$("#newOtpModal").modal();return true;}}}function checkNowOtpNumber(){var nowOtpCheckFrm=document.nowOtpCheckFrm;var mId=$("#mId").val();$.ajax({type:'post',url:'/common/GoogleOtp/ajaxCheckIdAndOtpInfo',data:{mId:mId,otpNumber:nowOtpCheckFrm.nowOtpNumber.value},cache:false,async:false,dataType:'json',success:function(data){if(data.isResult==true){$('#nowOtpNumberTxt').html("");$('#checkOtpModal').modal('hide');setOtpUsable();return true;}else{$('#nowOtpNumberTxt').html(data.data.message);nowOtpCheckFrm.nowOtpNumber.focus();return false;}}});}function setNewOtpPop(){var mIdx=$("#mIdx").val();$.ajax({type:'post',url:'/common/GoogleOtp/ajaxNewOtpInfo',data:{mIdx:mIdx},cache:false,async:false,dataType:'json',success:function(data){if(data.isResult==true){$('#newOtpNumber').val("");$('#newhiddenOtpKey').val("");$('#newOtpNumberTxt').html("");$('#newOtpKey').html(data.data.newOtpKeyStr);$('#newhiddenOtpKey').val(data.data.newOtpKeyStr);$("#newOtpQrCode").attr("src",data.data.qrCodeImgUrl);return true;}}});}function setCurrentOtpPop(){var mIdx=$("#mIdx").val();$.ajax({type:'post',url:'/common/GoogleOtp/ajaxCurrentOtpInfo',data:{mIdx:mIdx},cache:false,async:false,dataType:'json',success:function(data){if(data.isResult==true){$('#currentOtpKey').html("");$('#currentOtpQrCode').show();$('#currentOtpKey').html(data.data.googleOtpKeyStr);$("#currentOtpQrCode").attr("src",data.data.qrCodeImgUrl);return true;}else{$('#currentOtpKey').html(data.data.message);$('#currentOtpQrCode').hide();return false;}}});}function checkNewOtpNumber(){var newOtpCheckFrm=document.newOtpCheckFrm;var checkNextAct=$("#selectNextPg").val();$.ajax({type:'post',url:'/common/GoogleOtp/ajaxCheckOtpInfo',data:{otpKey:newOtpCheckFrm.newhiddenOtpKey.value,otpNumber:newOtpCheckFrm.newOtpNumber.value},cache:false,async:false,dataType:'json',success:function(data){if(data.isResult==true){$('#newOtpNumberTxt').html("");setNewOtpNumber();if(checkNextAct=='checkOptUsable'){setOtpUsable();}alert("새로운 OTP정보가 등록되었습니다.");$('#newOtpModal').modal('hide');return true;}else{$('#newOtpNumberTxt').html(data.data.message);newOtpCheckFrm.newOtpNumber.focus();return false;}}});}function setNewOtpNumber(){var newOtpCheckFrm=document.newOtpCheckFrm;var mIdx=$("#mIdx").val();$.ajax({type:'post',url:'/common/GoogleOtp/ajaxSetNewOtp',data:{mIdx:mIdx,otpKey:newOtpCheckFrm.newhiddenOtpKey.value},cache:false,async:false,dataType:'json',success:function(data){if(data.isResult==true){return true;}else{alert(data.data.message);return false;}}});}function setOtpUsable(){var otpFrm=document.otpWriteFrm;otpFrm.action="/Main/actMyOtpWrite";otpFrm.submit();}function setLang(){var langFrm=document.langFrm;langFrm.action="/Main/actMemberLangChange";langFrm.submit();}</script>

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
                        개인정보 <small class="text-red"> (개인 고유정보 수정은 관리자에 문의하세요.)</small>
                      </h2>
                    </div>
                  </div> 
                </div>

                <div class="card-body">
                  <!-- 가입 정보 -->
                  <div class="row">
                    <div class="col-12 col-xl-6 mb-3">
                      <div class="card-inside">
                        <div class="card-body">
                        <div class="mb-3">
                          <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                              <span class="h4 mb-0 font-weight-bold">아이디</span> <span class="h4 mb-0"><?=$username?></span>
                            </li>
                            <!-- <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                              <span class="h4 mb-0 font-weight-bold">이름</span> <span class="h4 mb-0">임연택</span>
                            </li> -->
                            <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                              <span class="h4 mb-0 font-weight-bold">휴대폰 번호</span> <span class="h4 mb-0"><?=$phone_number?></span>
                            </li>
                            <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                              <span class="h4 mb-0 font-weight-bold">가입일</span> <span class="h4 mb-0"><?=$created_at?></span>
                            </li>
                            <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                <span class="h4 mb-0 font-weight-bold">기본 언어 </span>
                                <span class="h4 mb-0">
                                  <form name="langFrm" method="post">
                                    <div class="form-row align-items-center">
                                      <div class="input-group input-group-merge">
                                        <select name="language" class="language-select" data-width="fit">
<option value="ko" selected="selected">한국어</option>
<option value="en">English</option>
<option value="cn">简体中文</option>
<option value="hk">繁體中文</option>
<option value="jp">日本語</option>
<option value="vn">Tiếng Việt</option>
</select>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-primary" onclick="setLang();">저장</button>
                                        </div>
                                    </div>
                                    </div>
                                  </form>
                              </span>
                            </li>
                          </ul>
                        </div>
                        </div>
                      </div>
                    </div>
                    <!-- /가입 정보 -->

                    <!-- 지갑 정보 -->
                    <div class="col-12 col-xl-6 mb-3">
                      <div class="card-inside">
                        <div class="card-body">
                          <div class="mb-3">
                            <div class="row align-items-center mb-3">
                              <div class="col">                                
                                <h4 class="card-title mb-2 mt-3">
                                  지갑 주소                                                                  </h4>  
                              </div>
                            </div> 
                            <div class="mb-3">
                              <div class="input-group input-group-merge">
                                <input type="text" class="form-control form-control-appended input-border-grey" id="ethWalletaddress" value="<?=$eth_address?>" readonly="">
                                <div class="input-group-append">
                                  <button type="button" class="btn-copy2 btn btn-primary" id="ethWalletaddress" data-clipboard-target="#ethWalletaddress" defval="">Copy</button>
                                </div>
                              </div>
                            </div>
                            <!-- <div class="row align-items-center mb-3">
                              <div class="col">
                                <h4 class="card-title mb-2 mt-3">
                                  추천 URL
                                  <small class="text-red"> (지인에게 URL을 전달해주세요.)</small>
                                </h4>  
                              </div>
                            </div>  -->
                            <!-- <div class="mb-3">
                              <div class="input-group input-group-merge">
                                <input type="text" class="form-control form-control-appended input-border-grey" id="recommendUrl" value="http://3.34.48.186/Auth/joinThroughReferral?recommenderIdx=71" readonly="">
                                <div class="input-group-append">
                                  <button type="button" class="btn-copy2 btn btn-primary" id="recommendUrl" data-clipboard-target="#recommendUrl" defval="">Copy</button>
                                </div>
                              </div>
                            </div> -->

                          </div>
                        </div>
                      </div>
                    </div>
                    <!-- /지갑 정보 -->
                  </div>
                </div>
            </div>
          </div>
        </div>
        <!-- /.row -->

        <!-- row -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                  
                  <div class="card-header">
                    <div class="row align-items-center">
                      <div class="col">
                        <!-- Title -->
                        <h2 class="card-header-title">
                          개인정보 재설정                        </h2>
                      </div>
                    </div> 
                  </div>
  
                  <div class="card-body">
                    <!-- 비밀번호 재설정 -->
                    <div class="row">
                        <div class="col-12 col-xl-12 mb-3"> 
                            <form name="pwWriteFrm" method="post">
                                <input type="hidden" name="type" value="changePW">
                                <div class="card-inside card-tab">
                                    <div class="card-header">비밀번호 재설정 
                                    </div> 
                                    <div class="card-body">
                                        <div class="mb-4">
                                            <div class="row align-items-center mb-3">
                                                <div class="col">
                                                <!-- Title -->
                                                <h4 class="card-title mb-2">
                                                    현재 비밀번호                                                </h4>  
                                                </div>
                                            </div>                
                                            <div class="mb-3">
                                                <div class="input-group">
                                                <input type="password" class="form-control input-border-grey" name="currentPw" placeholder="현재 비밀번호">
                                                </div>
                                            </div>   

                                            <div class="row align-items-center mb-3">
                                                <div class="col">
                                                <!-- Title -->
                                                <h4 class="card-title mb-2 mt-3">
                                                    새로운 비밀번호                                                    <small class="text-red">(8자이상, 특수문자포함, 숫자포함)</small>
                                                </h4>  
                                                </div>
                                            </div> 
                                            <div class="mb-3">
                                                <div class="input-group">
                                                <input type="password" class="form-control form-control-appended input-border-grey" name="newPw" placeholder="새로운 비밀번호">
                                                </div>
                                            </div>    

                                            <div class="row align-items-center mb-3">
                                                <div class="col">
                                                <!-- Title -->
                                                <h4 class="card-title mb-2 mt-3">
                                                    새로운 비밀번호 확인                                                </h4>  
                                                </div>
                                            </div> 
                                            <div class="mb-3">
                                                <div class="input-group">
                                                    <input type="password" class="form-control form-control-appended input-border-grey" name="newPwCheck" placeholder="새로운 비밀번호 확인">
                                                </div>
                                            </div> 
                                        </div>
                                        <div class="row align-items-center text-right">
                                            <div class="col">
                                                <button type="button" class="btn btn-primary" onclick="commGoAct('password');">비밀번호 재설정</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                      <!-- /비밀번호 재설정 -->
  
                      <!-- PIN번호 재설정 -->
                                            <!-- /PIN번호 재설정 -->
                    </div>
                  </div>
              </div>
            </div>
          </div>
          <!-- /.row -->

          
          <!-- row -->
          <!-- <div class="row">
              <div class="col-12">
                  <div class="card">

                      <div class="card-header">
                          <div class="row align-items-center">
                              <div class="col">
                                  <h2 class="card-header-title" id="otpCard">
                                      구글 OTP 설정                                   </h2>
                              </div>
                          </div>
                      </div>

                      <div class="card-body">
                          <div class="row">
                              <div class="col-12 col-xl-12 mb-3">
                                  <form name="otpWriteFrm" method="post">
                                  <input type="hidden" name="mIdx" id="mIdx" value="71">
                                  <input type="hidden" name="mId" id="mId" value="lyt217@naver.com">
                                      <div class="card-inside card-tab">
                                          <div class="card-header">OTP 사용여부 설정</div>
                                          <div class="card-body">
                                              <div class="row align-items-center">
                                                  <div class="col-6">
                                                      <div class="custom-control custom-radio custom-control-inline pl-0">
                                                          <input type="radio" id="useOtp" name="googleOtpUsed" class="custom-control-input" value="1">
                                                          <label class="custom-control-label" for="useOtp">사용함</label>
                                                      </div>
                                                      <div class="custom-control custom-radio custom-control-inline pl-0">
                                                          <input type="radio" id="notUseOtp" name="googleOtpUsed" class="custom-control-input" value="2" checked="">
                                                          <label class="custom-control-label" for="notUseOtp">사용안함</label>
                                                      </div>
                                                  </div>
                                                  <div class="col-6 text-right">
                                                  <button type="button" class="btn btn-primary" onclick="saveOtpUsable();">저장</button>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </form>
                              </div>
                              <div class="col-12 col-xl-6 mb-3">
                                      <div class="card-inside card-tab">
                                          <div class="card-header">OTP 관리</div>
                                          <div class="card-body">
                                              <div class="">
                                                  <div class="row align-items-center">
                                                      <div class="col">
                                                          <div class="row align-items-center">
                                                              <div class="col">
                                                                  <button type="button" class="btn btn-outline-primary btn-responsive mb-3 mb-md-0 mr-2" onclick="popOtpMethod();">
                                                                    사용방법 보기                                                                  </button>
                                                                  <button type="button" class="btn btn-outline-primary btn-responsive mb-3 mb-md-0  mr-2" onclick="popCurrentOtpInfo();">
                                                                    현재 OTP 정보                                                                  </button>
                                                                  <button type="button" class="btn btn-outline-primary btn-responsive mb-3 mb-md-0 " onclick="popNewOtpInfo();">
                                                                  OTP 갱신하기                                                                  </button>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>

                                              </div>
                                          </div>
                                      </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div> -->
                    <!-- /.row -->
           <!-- Modal 비밀번호 확인 -->
           <div class="modal fade" id="checkPwModal" role="document">
               <div class="modal-dialog modal-otp">
                   <div class="modal-content">
                       <div class="modal-header border-color-grey">
                           <h3 class="modal-title font-weight-bold">비밀번호 확인</h3>
                           <button type="button" class="close" data-dismiss="modal">×</button>
                       </div>                       
                       <div class="modal-body">
                            <form name="hiddenFrm" method="post">
                                <input type="hidden" id="selectNextPg" name="selectNextPg" value="">
                            </form>
                            <form name="modalPwFrm" method="post">
                                <div class="mt-2">
                                    <input type="password" class="form-control input-border-grey" id="modalPw" name="modalPw" placeholder="비밀번호를 입력해주세요." onkeydown="return ignorePwEnter(event);">
                                    <small id="modalPwTxt" class="otp-text-red"></small>
                                </div>
                                <div class="mb-3">
                                    <button type="button" class="btn btn-primary w-100" onclick="checkModalPw();">확인</button>
                                </div>
                            </form>
                       </div>
                   </div>
               </div>
           </div>
         <!-- Modal 비밀번호 확인 End -->

         <!-- Modal 구글 OTP 번호 확인 -->
          <div class="modal fade" id="checkOtpModal" role="document">
              <div class="modal-dialog modal-otp">
                  <div class="modal-content">
                      <div class="modal-header border-color-grey">
                          <h3 class="modal-title font-weight-bold">구글 OTP 인증 확인</h3>
                          <button type="button" class="close" data-dismiss="modal">×</button>
                      </div>
                      <div class="modal-body">
                          <form name="nowOtpCheckFrm" method="post">                              
                              <div class="mt-2">
                                  <input type="text" class="form-control input-border-grey" id="nowOtpNumber" name="nowOtpNumber" placeholder="OTP번호를 입력해주세요." onkeydown="return ignoreNowOtpEnter(event);" autocomplete="off">
                                  <small id="nowOtpNumberTxt" class="otp-text-red"></small>
                              </div>
                              <div class="mb-3">
                                  <button type="button" class="btn btn-primary w-100" onclick="checkNowOtpNumber();">확인</button>
                              </div>
                          </form>
                      </div>
                  </div>
              </div>
          </div>
         <!-- Modal 구글 OTP 번호 확인 End -->

         <!-- Modal 현재 OTP 정보 -->
          <div class="modal fade" id="currentOtpModal" role="document">
              <div class="modal-dialog modal-otp">
                  <div class="modal-content">
                      <div class="modal-header border-color-grey">
                          <h3 class="modal-title font-weight-bold">현재 OTP 인증 정보</h3>
                          <button type="button" class="close" data-dismiss="modal">×</button>
                      </div>
                      <div class="modal-body">
                          <div class="text-center border border-color-grey py-3" style="border-radius:.375rem;">
                              <img id="currentOtpQrCode" src="" alt="currentOtpKey" data-pagespeed-url-hash="469765034" onload="pagespeed.CriticalImages.checkImageForCriticality(this);">
                              <div class="mt-3">
                                  <span class="font-weight-bold pr-3 otp-info">OTP 키</span>
                                  <span id="currentOtpKey" class="otp-info text-primary"></span>
                                  <small class="otp-text-green">OTP 앱에 키를 등록하면 인증번호가 확인가능합니다.</small>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
         <!-- Modal 현재 OTP 정보 End -->

         <!-- Modal 새로운 OTP 등록 -->
          <div class="modal fade" id="newOtpModal" role="document">
              <div class="modal-dialog modal-otp">
                  <div class="modal-content">
                      <div class="modal-header border-color-grey">
                          <h3 class="modal-title font-weight-bold">새로운 OTP 인증 등록</h3>
                          <button type="button" class="close" data-dismiss="modal">×</button>
                      </div>
                      <div class="modal-body">
                          <form name="newOtpCheckFrm" method="post">
                              <input type="hidden" id="newhiddenOtpKey" name="newhiddenOtpKey" value="">
                              <div class="text-center border border-color-grey py-3" style="border-radius:.375rem;">
                                  <img id="newOtpQrCode" src="" alt="newOtpKey" data-pagespeed-url-hash="469765034" onload="pagespeed.CriticalImages.checkImageForCriticality(this);">
                                  <div class="mt-3">
                                      <span class="font-weight-bold pr-3 otp-info">OTP 키</span>
                                      <span id="newOtpKey" class="otp-info text-primary"></span>
                                      <small class="otp-text-green">OTP 앱에 키를 등록하면 인증번호가 확인가능합니다.</small>
                                  </div>
                              </div>
                              <div class="mt-5">
                                  <input type="text" class="form-control input-border-grey" id="newOtpNumber" name="newOtpNumber" placeholder="OTP번호를 입력해주세요." onkeydown="return ignoreNewOtpEnter(event);" autocomplete="off">
                                  <small id="newOtpNumberTxt" class="otp-text-red"></small>
                              </div>
                              <div class="mb-3">
                                  <button type="button" class="btn btn-primary w-100" onclick="checkNewOtpNumber();">확인</button>
                              </div>
                          </form>
                      </div>
                  </div>
              </div>
          </div>
         <!-- Modal 새로운 OTP 등록 End -->
        
         <!-- Modal 사용법보기 -->
         <div class="modal fade" id="useOtpMethodModal" role="document">
              <div class="modal-dialog modal-otp">
                  <div class="modal-content popup">
                      <div class="modal-header border-color-grey">
                          <h3 class="modal-title font-weight-bold">OTP 사용방법 보기</h3>
                          <button type="button" class="close" data-dismiss="modal">×</button>
                      </div>
                      <div class="popNoticeBody">
                        <img class="modal-image" id="popNoticeImg" style="border-radius:0 0 .5rem .5rem;" src="/inc/reformUser/build/img/explainOtp.png" data-pagespeed-url-hash="828628758" onload="pagespeed.CriticalImages.checkImageForCriticality(this);">
                      </div>
                  </div>
              </div>
          </div>
          <!-- Modal 사용법보기 End -->

      </div>

    </div> <!-- / .main-content -->



    <footer>
    </footer>

    <script>
    $(document).ready(function(){
        var r = '<?=$r?>';
        if(r.length > 0){
            if(r == 'success'){
                alert("비밀번호가 정상적으로 변경되었습니다.");
                location.href = '/profile.php';
            }
            else if(r == 'success'){
                alert("비밀번호 변경에 실패했습니다.");
                location.href = '/profile.php';

            }
        }
    });
    </script>
    <script src="/inc/reformUser/assets/libs/jquery/dist/jquery.min.js.pagespeed.jm.r0B4QCxeCQ.js"></script>
    <script src="/inc/reformUser/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/inc/reformUser/assets/libs/highlightjs/highlight.pack.min.js"></script>
    <script src="/inc/reformUser/assets/libs/jquery-mask-plugin/dist/jquery.mask.min.js"></script>
    <script src="/inc/reformUser/assets/libs/quill/dist/quill.min.js"></script>
    <script src="/inc/reformUser/assets/libs/dropzone/dist/min/dropzone.min.js"></script>
    <script src="/inc/reformUser/assets/libs/select2/dist/js/select2.min.js"></script>

    

  
</body></html>