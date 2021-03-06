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
                  <i class="nav-icon home "></i>???                </a>
              </li>

              
              <!-- ????????????1 ??????-->  
              <li class="nav-item">
                <a class="nav-link " href="#sidebarEthToken1" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="#sidebarEthToken1">
                  <i class="nav-icon wallet "></i><span class="textLITE pr-1">MDL</span> ??????                </a>
                <div class="collapse " id="sidebarEthToken1">
                  <ul class="nav nav-sm flex-column">
                    <li class="nav-item">
                      <a href="/transaction.php?coin=MDL" class="nav-link ">
                        <span class="textToken1 pr-1">MDL</span> ????????? &amp; ??????                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="/history.php?coin=MDL" class="nav-link ">
                        <span class="textToken1 pr-1">MDL</span> ?????? ??????                      </a>
                    </li>
                    <!-- <li class="nav-item">
                      <a href="/transfer.php?coin=MDL" class="nav-link ">
                        <span class="textToken1 pr-1">MDL</span> ??????                      </a>
                    </li> -->
                  </ul>
                </div>
              </li>
              <!-- ????????????1 ??????-->
             
              
              
              <!-- ETH?????? ??????-->
              <li class="nav-item">
                <a class="nav-link " href="#sidebarETH" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="sidebarETH">
                  <i class="nav-icon wallet "></i><span class="textETH pr-1">ETH</span> ??????                </a>
                <div class="collapse " id="sidebarETH">
                  <ul class="nav nav-sm flex-column"> 
                    <li class="nav-item">
                      <a href="/transaction.php?coin=ETH" class="nav-link ">
                        <span class="textETH pr-1">ETH</span> ????????? &amp; ??????                      </a>
                    </li> 
                    <li class="nav-item">
                      <a href="/history.php?coin=ETH" class="nav-link ">
                        <span class="textETH pr-1">ETH</span> ?????? ??????                      </a>
                    </li>
                  </ul>
                </div>
              </li>
              <!-- ETH?????? ??????-->
              
              <li class="nav-item">
                <a class="nav-link lnb_title_active" href="/profile.php">
                  <i class="nav-icon user profile_icon_active"></i>??? ?????????                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="/logout.php" onclick="return confirm('???????????? ???????????????????');">
                  <i class="nav-icon logout"></i>????????????                </a>
              </li>
            </ul>

            <!-- Divider -->
            <hr class="navbar-divider my-3">

            <!-- Heading -->
            <h6 class="navbar-heading">
              ?????? ??????            </h6>

              <!-- language select -->
              <select name="langSelect" id="langSelect" class="language-select" data-width="fit" onchange="langChange();">
<option value="ko" selected="selected">?????????</option>
<option value="en">English</option>
<option value="cn">????????????</option>
<option value="hk">????????????</option>
<option value="jp">?????????</option>
<option value="vn">Ti???ng Vi???t</option>
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
              alert("??????????????? ????????????????????? ???????????? ????????????.");
              return false;
          }
          if(confirm("?????????????????????????")){
              pwFrm.action="/ajax/ajax.member.php";
              pwFrm.submit();
            }
            else{
                return false;
            }
        }
        else if(mode=="pin"){
            var pinFrm=document.pinWriteFrm;
            if(confirm("?????????????????????????")){
                pinFrm.action="/Main/actMyProfilePinWrite";
                pinFrm.submit();
            }
            else{
                return false;
            }
        }
    }
    function popOtpMethod(){$("#useOtpMethodModal").modal();}function saveOtpUsable(){$("#selectNextPg").val('checkOptUsable');$("#modalPw").val('');$("#modalPwTxt").html('');$("#checkPwModal").modal();}function popCurrentOtpInfo(){$("#selectNextPg").val('currentOtpInfo');$("#modalPw").val('');$("#modalPwTxt").html('');$("#checkPwModal").modal();}function popNewOtpInfo(){$("#selectNextPg").val('newOtpInfo');$("#modalPw").val('');$("#modalPwTxt").html('');$("#checkPwModal").modal();}function checkModalPw(){var otpFrm=document.otpWriteFrm;var mIdx=$("#mIdx").val();var isPassword=false;$.ajax({type:'post',url:'/common/GoogleOtp/ajaxCheckPw',data:{mIdx:mIdx,mPassword:modalPwFrm.modalPw.value},cache:false,async:false,dataType:'json',success:function(data){if(data.isResult==true){isPassword=true;$("#modalPwTxt").html('');return true;}else{isPassword=false;$("#modalPwTxt").html(data.data.message);return false;}}});if(isPassword==true){if($("#selectNextPg").val()=='checkOptUsable'){if(otpFrm.googleOtpUsed.value=="1"){$.ajax({type:'post',url:'/common/GoogleOtp/ajaxCheckExistOtpKey',data:{mIdx:mIdx},cache:false,async:false,dataType:'json',success:function(data){if(data.isResult==true){if(data.data.isExist==true){$('#checkPwModal').modal('hide');$("#nowOtpNumber").val('');$('#nowOtpNumberTxt').html("");$("#checkOtpModal").modal();return true;}else{$('#checkPwModal').modal('hide');setNewOtpPop();$("#newOtpModal").modal();return true;}}}});}else if(otpFrm.googleOtpUsed.value=="2"){setOtpUsable();return true;}}else if($("#selectNextPg").val()=='currentOtpInfo'){$('#checkPwModal').modal('hide');setCurrentOtpPop();$("#currentOtpModal").modal();return true;}else if($("#selectNextPg").val()=='newOtpInfo'){$('#checkPwModal').modal('hide');setNewOtpPop();$("#newOtpModal").modal();return true;}}}function checkNowOtpNumber(){var nowOtpCheckFrm=document.nowOtpCheckFrm;var mId=$("#mId").val();$.ajax({type:'post',url:'/common/GoogleOtp/ajaxCheckIdAndOtpInfo',data:{mId:mId,otpNumber:nowOtpCheckFrm.nowOtpNumber.value},cache:false,async:false,dataType:'json',success:function(data){if(data.isResult==true){$('#nowOtpNumberTxt').html("");$('#checkOtpModal').modal('hide');setOtpUsable();return true;}else{$('#nowOtpNumberTxt').html(data.data.message);nowOtpCheckFrm.nowOtpNumber.focus();return false;}}});}function setNewOtpPop(){var mIdx=$("#mIdx").val();$.ajax({type:'post',url:'/common/GoogleOtp/ajaxNewOtpInfo',data:{mIdx:mIdx},cache:false,async:false,dataType:'json',success:function(data){if(data.isResult==true){$('#newOtpNumber').val("");$('#newhiddenOtpKey').val("");$('#newOtpNumberTxt').html("");$('#newOtpKey').html(data.data.newOtpKeyStr);$('#newhiddenOtpKey').val(data.data.newOtpKeyStr);$("#newOtpQrCode").attr("src",data.data.qrCodeImgUrl);return true;}}});}function setCurrentOtpPop(){var mIdx=$("#mIdx").val();$.ajax({type:'post',url:'/common/GoogleOtp/ajaxCurrentOtpInfo',data:{mIdx:mIdx},cache:false,async:false,dataType:'json',success:function(data){if(data.isResult==true){$('#currentOtpKey').html("");$('#currentOtpQrCode').show();$('#currentOtpKey').html(data.data.googleOtpKeyStr);$("#currentOtpQrCode").attr("src",data.data.qrCodeImgUrl);return true;}else{$('#currentOtpKey').html(data.data.message);$('#currentOtpQrCode').hide();return false;}}});}function checkNewOtpNumber(){var newOtpCheckFrm=document.newOtpCheckFrm;var checkNextAct=$("#selectNextPg").val();$.ajax({type:'post',url:'/common/GoogleOtp/ajaxCheckOtpInfo',data:{otpKey:newOtpCheckFrm.newhiddenOtpKey.value,otpNumber:newOtpCheckFrm.newOtpNumber.value},cache:false,async:false,dataType:'json',success:function(data){if(data.isResult==true){$('#newOtpNumberTxt').html("");setNewOtpNumber();if(checkNextAct=='checkOptUsable'){setOtpUsable();}alert("????????? OTP????????? ?????????????????????.");$('#newOtpModal').modal('hide');return true;}else{$('#newOtpNumberTxt').html(data.data.message);newOtpCheckFrm.newOtpNumber.focus();return false;}}});}function setNewOtpNumber(){var newOtpCheckFrm=document.newOtpCheckFrm;var mIdx=$("#mIdx").val();$.ajax({type:'post',url:'/common/GoogleOtp/ajaxSetNewOtp',data:{mIdx:mIdx,otpKey:newOtpCheckFrm.newhiddenOtpKey.value},cache:false,async:false,dataType:'json',success:function(data){if(data.isResult==true){return true;}else{alert(data.data.message);return false;}}});}function setOtpUsable(){var otpFrm=document.otpWriteFrm;otpFrm.action="/Main/actMyOtpWrite";otpFrm.submit();}function setLang(){var langFrm=document.langFrm;langFrm.action="/Main/actMemberLangChange";langFrm.submit();}</script>

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
                        ???????????? <small class="text-red"> (?????? ???????????? ????????? ???????????? ???????????????.)</small>
                      </h2>
                    </div>
                  </div> 
                </div>

                <div class="card-body">
                  <!-- ?????? ?????? -->
                  <div class="row">
                    <div class="col-12 col-xl-6 mb-3">
                      <div class="card-inside">
                        <div class="card-body">
                        <div class="mb-3">
                          <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                              <span class="h4 mb-0 font-weight-bold">?????????</span> <span class="h4 mb-0"><?=$username?></span>
                            </li>
                            <!-- <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                              <span class="h4 mb-0 font-weight-bold">??????</span> <span class="h4 mb-0">?????????</span>
                            </li> -->
                            <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                              <span class="h4 mb-0 font-weight-bold">????????? ??????</span> <span class="h4 mb-0"><?=$phone_number?></span>
                            </li>
                            <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                              <span class="h4 mb-0 font-weight-bold">?????????</span> <span class="h4 mb-0"><?=$created_at?></span>
                            </li>
                            <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                <span class="h4 mb-0 font-weight-bold">?????? ?????? </span>
                                <span class="h4 mb-0">
                                  <form name="langFrm" method="post">
                                    <div class="form-row align-items-center">
                                      <div class="input-group input-group-merge">
                                        <select name="language" class="language-select" data-width="fit">
<option value="ko" selected="selected">?????????</option>
<option value="en">English</option>
<option value="cn">????????????</option>
<option value="hk">????????????</option>
<option value="jp">?????????</option>
<option value="vn">Ti???ng Vi???t</option>
</select>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-primary" onclick="setLang();">??????</button>
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
                    <!-- /?????? ?????? -->

                    <!-- ?????? ?????? -->
                    <div class="col-12 col-xl-6 mb-3">
                      <div class="card-inside">
                        <div class="card-body">
                          <div class="mb-3">
                            <div class="row align-items-center mb-3">
                              <div class="col">                                
                                <h4 class="card-title mb-2 mt-3">
                                  ?????? ??????                                                                  </h4>  
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
                                  ?????? URL
                                  <small class="text-red"> (???????????? URL??? ??????????????????.)</small>
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
                    <!-- /?????? ?????? -->
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
                          ???????????? ?????????                        </h2>
                      </div>
                    </div> 
                  </div>
  
                  <div class="card-body">
                    <!-- ???????????? ????????? -->
                    <div class="row">
                        <div class="col-12 col-xl-12 mb-3"> 
                            <form name="pwWriteFrm" method="post">
                                <input type="hidden" name="type" value="changePW">
                                <div class="card-inside card-tab">
                                    <div class="card-header">???????????? ????????? 
                                    </div> 
                                    <div class="card-body">
                                        <div class="mb-4">
                                            <div class="row align-items-center mb-3">
                                                <div class="col">
                                                <!-- Title -->
                                                <h4 class="card-title mb-2">
                                                    ?????? ????????????                                                </h4>  
                                                </div>
                                            </div>                
                                            <div class="mb-3">
                                                <div class="input-group">
                                                <input type="password" class="form-control input-border-grey" name="currentPw" placeholder="?????? ????????????">
                                                </div>
                                            </div>   

                                            <div class="row align-items-center mb-3">
                                                <div class="col">
                                                <!-- Title -->
                                                <h4 class="card-title mb-2 mt-3">
                                                    ????????? ????????????                                                    <small class="text-red">(8?????????, ??????????????????, ????????????)</small>
                                                </h4>  
                                                </div>
                                            </div> 
                                            <div class="mb-3">
                                                <div class="input-group">
                                                <input type="password" class="form-control form-control-appended input-border-grey" name="newPw" placeholder="????????? ????????????">
                                                </div>
                                            </div>    

                                            <div class="row align-items-center mb-3">
                                                <div class="col">
                                                <!-- Title -->
                                                <h4 class="card-title mb-2 mt-3">
                                                    ????????? ???????????? ??????                                                </h4>  
                                                </div>
                                            </div> 
                                            <div class="mb-3">
                                                <div class="input-group">
                                                    <input type="password" class="form-control form-control-appended input-border-grey" name="newPwCheck" placeholder="????????? ???????????? ??????">
                                                </div>
                                            </div> 
                                        </div>
                                        <div class="row align-items-center text-right">
                                            <div class="col">
                                                <button type="button" class="btn btn-primary" onclick="commGoAct('password');">???????????? ?????????</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                      <!-- /???????????? ????????? -->
  
                      <!-- PIN?????? ????????? -->
                                            <!-- /PIN?????? ????????? -->
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
                                      ?????? OTP ??????                                   </h2>
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
                                          <div class="card-header">OTP ???????????? ??????</div>
                                          <div class="card-body">
                                              <div class="row align-items-center">
                                                  <div class="col-6">
                                                      <div class="custom-control custom-radio custom-control-inline pl-0">
                                                          <input type="radio" id="useOtp" name="googleOtpUsed" class="custom-control-input" value="1">
                                                          <label class="custom-control-label" for="useOtp">?????????</label>
                                                      </div>
                                                      <div class="custom-control custom-radio custom-control-inline pl-0">
                                                          <input type="radio" id="notUseOtp" name="googleOtpUsed" class="custom-control-input" value="2" checked="">
                                                          <label class="custom-control-label" for="notUseOtp">????????????</label>
                                                      </div>
                                                  </div>
                                                  <div class="col-6 text-right">
                                                  <button type="button" class="btn btn-primary" onclick="saveOtpUsable();">??????</button>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </form>
                              </div>
                              <div class="col-12 col-xl-6 mb-3">
                                      <div class="card-inside card-tab">
                                          <div class="card-header">OTP ??????</div>
                                          <div class="card-body">
                                              <div class="">
                                                  <div class="row align-items-center">
                                                      <div class="col">
                                                          <div class="row align-items-center">
                                                              <div class="col">
                                                                  <button type="button" class="btn btn-outline-primary btn-responsive mb-3 mb-md-0 mr-2" onclick="popOtpMethod();">
                                                                    ???????????? ??????                                                                  </button>
                                                                  <button type="button" class="btn btn-outline-primary btn-responsive mb-3 mb-md-0  mr-2" onclick="popCurrentOtpInfo();">
                                                                    ?????? OTP ??????                                                                  </button>
                                                                  <button type="button" class="btn btn-outline-primary btn-responsive mb-3 mb-md-0 " onclick="popNewOtpInfo();">
                                                                  OTP ????????????                                                                  </button>
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
           <!-- Modal ???????????? ?????? -->
           <div class="modal fade" id="checkPwModal" role="document">
               <div class="modal-dialog modal-otp">
                   <div class="modal-content">
                       <div class="modal-header border-color-grey">
                           <h3 class="modal-title font-weight-bold">???????????? ??????</h3>
                           <button type="button" class="close" data-dismiss="modal">??</button>
                       </div>                       
                       <div class="modal-body">
                            <form name="hiddenFrm" method="post">
                                <input type="hidden" id="selectNextPg" name="selectNextPg" value="">
                            </form>
                            <form name="modalPwFrm" method="post">
                                <div class="mt-2">
                                    <input type="password" class="form-control input-border-grey" id="modalPw" name="modalPw" placeholder="??????????????? ??????????????????." onkeydown="return ignorePwEnter(event);">
                                    <small id="modalPwTxt" class="otp-text-red"></small>
                                </div>
                                <div class="mb-3">
                                    <button type="button" class="btn btn-primary w-100" onclick="checkModalPw();">??????</button>
                                </div>
                            </form>
                       </div>
                   </div>
               </div>
           </div>
         <!-- Modal ???????????? ?????? End -->

         <!-- Modal ?????? OTP ?????? ?????? -->
          <div class="modal fade" id="checkOtpModal" role="document">
              <div class="modal-dialog modal-otp">
                  <div class="modal-content">
                      <div class="modal-header border-color-grey">
                          <h3 class="modal-title font-weight-bold">?????? OTP ?????? ??????</h3>
                          <button type="button" class="close" data-dismiss="modal">??</button>
                      </div>
                      <div class="modal-body">
                          <form name="nowOtpCheckFrm" method="post">                              
                              <div class="mt-2">
                                  <input type="text" class="form-control input-border-grey" id="nowOtpNumber" name="nowOtpNumber" placeholder="OTP????????? ??????????????????." onkeydown="return ignoreNowOtpEnter(event);" autocomplete="off">
                                  <small id="nowOtpNumberTxt" class="otp-text-red"></small>
                              </div>
                              <div class="mb-3">
                                  <button type="button" class="btn btn-primary w-100" onclick="checkNowOtpNumber();">??????</button>
                              </div>
                          </form>
                      </div>
                  </div>
              </div>
          </div>
         <!-- Modal ?????? OTP ?????? ?????? End -->

         <!-- Modal ?????? OTP ?????? -->
          <div class="modal fade" id="currentOtpModal" role="document">
              <div class="modal-dialog modal-otp">
                  <div class="modal-content">
                      <div class="modal-header border-color-grey">
                          <h3 class="modal-title font-weight-bold">?????? OTP ?????? ??????</h3>
                          <button type="button" class="close" data-dismiss="modal">??</button>
                      </div>
                      <div class="modal-body">
                          <div class="text-center border border-color-grey py-3" style="border-radius:.375rem;">
                              <img id="currentOtpQrCode" src="" alt="currentOtpKey" data-pagespeed-url-hash="469765034" onload="pagespeed.CriticalImages.checkImageForCriticality(this);">
                              <div class="mt-3">
                                  <span class="font-weight-bold pr-3 otp-info">OTP ???</span>
                                  <span id="currentOtpKey" class="otp-info text-primary"></span>
                                  <small class="otp-text-green">OTP ?????? ?????? ???????????? ??????????????? ?????????????????????.</small>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
         <!-- Modal ?????? OTP ?????? End -->

         <!-- Modal ????????? OTP ?????? -->
          <div class="modal fade" id="newOtpModal" role="document">
              <div class="modal-dialog modal-otp">
                  <div class="modal-content">
                      <div class="modal-header border-color-grey">
                          <h3 class="modal-title font-weight-bold">????????? OTP ?????? ??????</h3>
                          <button type="button" class="close" data-dismiss="modal">??</button>
                      </div>
                      <div class="modal-body">
                          <form name="newOtpCheckFrm" method="post">
                              <input type="hidden" id="newhiddenOtpKey" name="newhiddenOtpKey" value="">
                              <div class="text-center border border-color-grey py-3" style="border-radius:.375rem;">
                                  <img id="newOtpQrCode" src="" alt="newOtpKey" data-pagespeed-url-hash="469765034" onload="pagespeed.CriticalImages.checkImageForCriticality(this);">
                                  <div class="mt-3">
                                      <span class="font-weight-bold pr-3 otp-info">OTP ???</span>
                                      <span id="newOtpKey" class="otp-info text-primary"></span>
                                      <small class="otp-text-green">OTP ?????? ?????? ???????????? ??????????????? ?????????????????????.</small>
                                  </div>
                              </div>
                              <div class="mt-5">
                                  <input type="text" class="form-control input-border-grey" id="newOtpNumber" name="newOtpNumber" placeholder="OTP????????? ??????????????????." onkeydown="return ignoreNewOtpEnter(event);" autocomplete="off">
                                  <small id="newOtpNumberTxt" class="otp-text-red"></small>
                              </div>
                              <div class="mb-3">
                                  <button type="button" class="btn btn-primary w-100" onclick="checkNewOtpNumber();">??????</button>
                              </div>
                          </form>
                      </div>
                  </div>
              </div>
          </div>
         <!-- Modal ????????? OTP ?????? End -->
        
         <!-- Modal ??????????????? -->
         <div class="modal fade" id="useOtpMethodModal" role="document">
              <div class="modal-dialog modal-otp">
                  <div class="modal-content popup">
                      <div class="modal-header border-color-grey">
                          <h3 class="modal-title font-weight-bold">OTP ???????????? ??????</h3>
                          <button type="button" class="close" data-dismiss="modal">??</button>
                      </div>
                      <div class="popNoticeBody">
                        <img class="modal-image" id="popNoticeImg" style="border-radius:0 0 .5rem .5rem;" src="/inc/reformUser/build/img/explainOtp.png" data-pagespeed-url-hash="828628758" onload="pagespeed.CriticalImages.checkImageForCriticality(this);">
                      </div>
                  </div>
              </div>
          </div>
          <!-- Modal ??????????????? End -->

      </div>

    </div> <!-- / .main-content -->



    <footer>
    </footer>

    <script>
    $(document).ready(function(){
        var r = '<?=$r?>';
        if(r.length > 0){
            if(r == 'success'){
                alert("??????????????? ??????????????? ?????????????????????.");
                location.href = '/profile.php';
            }
            else if(r == 'success'){
                alert("???????????? ????????? ??????????????????.");
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