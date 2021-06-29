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

$coin = 'MDL';
if(isset($_GET["coin"])){
    $coin = $_GET["coin"];
}
if($coin == 'MDL'){
    $amount = $frim;
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

$r = '';
if(isset($_GET["r"])){
  $r = $_GET["r"];
}
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
            <script data-pagespeed-no-defer="">//<![CDATA[
(function(){for(var g="function"==typeof Object.defineProperties?Object.defineProperty:function(b,c,a){if(a.get||a.set)throw new TypeError("ES3 does not support getters and setters.");b!=Array.prototype&&b!=Object.prototype&&(b[c]=a.value)},h="undefined"!=typeof window&&window===this?this:"undefined"!=typeof global&&null!=global?global:this,k=["String","prototype","repeat"],l=0;l<k.length-1;l++){var m=k[l];m in h||(h[m]={});h=h[m]}var n=k[k.length-1],p=h[n],q=p?p:function(b){var c;if(null==this)throw new TypeError("The 'this' value for String.prototype.repeat must not be null or undefined");c=this+"";if(0>b||1342177279<b)throw new RangeError("Invalid count value");b|=0;for(var a="";b;)if(b&1&&(a+=c),b>>>=1)c+=c;return a};q!=p&&null!=q&&g(h,n,{configurable:!0,writable:!0,value:q});var t=this;function u(b,c){var a=b.split("."),d=t;a[0]in d||!d.execScript||d.execScript("var "+a[0]);for(var e;a.length&&(e=a.shift());)a.length||void 0===c?d[e]?d=d[e]:d=d[e]={}:d[e]=c};function v(b){var c=b.length;if(0<c){for(var a=Array(c),d=0;d<c;d++)a[d]=b[d];return a}return[]};function w(b){var c=window;if(c.addEventListener)c.addEventListener("load",b,!1);else if(c.attachEvent)c.attachEvent("onload",b);else{var a=c.onload;c.onload=function(){b.call(this);a&&a.call(this)}}};var x;function y(b,c,a,d,e){this.h=b;this.j=c;this.l=a;this.f=e;this.g={height:window.innerHeight||document.documentElement.clientHeight||document.body.clientHeight,width:window.innerWidth||document.documentElement.clientWidth||document.body.clientWidth};this.i=d;this.b={};this.a=[];this.c={}}function z(b,c){var a,d,e=c.getAttribute("data-pagespeed-url-hash");if(a=e&&!(e in b.c))if(0>=c.offsetWidth&&0>=c.offsetHeight)a=!1;else{d=c.getBoundingClientRect();var f=document.body;a=d.top+("pageYOffset"in window?window.pageYOffset:(document.documentElement||f.parentNode||f).scrollTop);d=d.left+("pageXOffset"in window?window.pageXOffset:(document.documentElement||f.parentNode||f).scrollLeft);f=a.toString()+","+d;b.b.hasOwnProperty(f)?a=!1:(b.b[f]=!0,a=a<=b.g.height&&d<=b.g.width)}a&&(b.a.push(e),b.c[e]=!0)}y.prototype.checkImageForCriticality=function(b){b.getBoundingClientRect&&z(this,b)};u("pagespeed.CriticalImages.checkImageForCriticality",function(b){x.checkImageForCriticality(b)});u("pagespeed.CriticalImages.checkCriticalImages",function(){A(x)});function A(b){b.b={};for(var c=["IMG","INPUT"],a=[],d=0;d<c.length;++d)a=a.concat(v(document.getElementsByTagName(c[d])));if(a.length&&a[0].getBoundingClientRect){for(d=0;c=a[d];++d)z(b,c);a="oh="+b.l;b.f&&(a+="&n="+b.f);if(c=!!b.a.length)for(a+="&ci="+encodeURIComponent(b.a[0]),d=1;d<b.a.length;++d){var e=","+encodeURIComponent(b.a[d]);131072>=a.length+e.length&&(a+=e)}b.i&&(e="&rd="+encodeURIComponent(JSON.stringify(B())),131072>=a.length+e.length&&(a+=e),c=!0);C=a;if(c){d=b.h;b=b.j;var f;if(window.XMLHttpRequest)f=new XMLHttpRequest;else if(window.ActiveXObject)try{f=new ActiveXObject("Msxml2.XMLHTTP")}catch(r){try{f=new ActiveXObject("Microsoft.XMLHTTP")}catch(D){}}f&&(f.open("POST",d+(-1==d.indexOf("?")?"?":"&")+"url="+encodeURIComponent(b)),f.setRequestHeader("Content-Type","application/x-www-form-urlencoded"),f.send(a))}}}function B(){var b={},c;c=document.getElementsByTagName("IMG");if(!c.length)return{};var a=c[0];if(!("naturalWidth"in a&&"naturalHeight"in a))return{};for(var d=0;a=c[d];++d){var e=a.getAttribute("data-pagespeed-url-hash");e&&(!(e in b)&&0<a.width&&0<a.height&&0<a.naturalWidth&&0<a.naturalHeight||e in b&&a.width>=b[e].o&&a.height>=b[e].m)&&(b[e]={rw:a.width,rh:a.height,ow:a.naturalWidth,oh:a.naturalHeight})}return b}var C="";u("pagespeed.CriticalImages.getBeaconData",function(){return C});u("pagespeed.CriticalImages.Run",function(b,c,a,d,e,f){var r=new y(b,c,a,e,f);x=r;d&&w(function(){window.setTimeout(function(){A(r)},0)})});})();pagespeed.CriticalImages.Run('/mod_pagespeed_beacon','https://new.likrwallet.com/Main/ethTokenOneTransactionList','8Xxa2XQLv9',true,false,'h-8L-YZtNto');
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
              <!-- 토큰지갑2 시작-->  
              <!-- <li class="nav-item">
                <a class="nav-link " href="#sidebarEthToken3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="#sidebarEthToken3">
                  <i class="nav-icon wallet "></i><span class="textLITE pr-1">USDT</span> 지갑                </a>
                <div class="collapse " id="sidebarEthToken3">
                  <ul class="nav nav-sm flex-column">
                    <li class="nav-item">
                      <a href="/transaction.php?coin=USDT" class="nav-link ">
                        <span class="textToken1 pr-1">USDT</span> 보내기 &amp; 받기                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="/history.php?coin=USDT" class="nav-link ">
                        <span class="textToken1 pr-1">USDT</span> 거래 목록                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="/transfer.php?coin=USDT" class="nav-link ">
                        <span class="textToken1 pr-1">USDT</span> 환전                      </a>
                    </li>
                  </ul>
                </div>
              </li> -->
              <!-- 토큰지갑2 종료-->
              <!-- 토큰지갑3 시작-->  
              <!-- <li class="nav-item">
                <a class="nav-link " href="#sidebarEthToken2" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="#sidebarEthToken2">
                  <i class="nav-icon wallet "></i><span class="textLITE pr-1">TDOS</span> 지갑                </a>
                <div class="collapse " id="sidebarEthToken2">
                  <ul class="nav nav-sm flex-column">
                    <li class="nav-item">
                      <a href="/transaction.php?coin=TDOS" class="nav-link ">
                        <span class="textToken1 pr-1">TDOS</span> 보내기 &amp; 받기                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="/history.php?coin=TDOS" class="nav-link ">
                        <span class="textToken1 pr-1">TDOS</span> 거래 목록                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="/transfer.php?coin=TDOS" class="nav-link ">
                        <span class="textToken1 pr-1">TDOS</span> 환전                      </a>
                    </li>
                  </ul>
                </div>
              </li> -->
              <!-- 토큰지갑3 종료-->


              
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
<script type="text/javascript">function openEthScan(txid){var str="https://etherscan.io/tx/"+txid;window.open(str);}function openNewPage(txid){var str="https://etherscan.io/tx/"+txid;var json_parameter='{"title": "Transaction", "url": "'+str+'", "isOnResume": false, "isToolBar": true, "isCanGoBack": true}';window.web_view_bridge.openNewPage(json_parameter);}</script>

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
                                    <?=$coin?> 거래 목록                                    <small class="text-red"> ( 거래를 클릭하시면 상세정보를 볼 수 있습니다. )</small>
                                </h2>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="mb-3">Total : 0 Count (1/0)Page</h5>
                        <div class="table-responsive table-transaction">
                            <table class="table mb-0" id="tokenOneTransTable">
                                <thead>
                                <tr>
                                    <th scope="col">
                                        타입                                    </th>
                                    <th scope="col">
                                        상태                                    </th>
                                    <th scope="col">
                                        수량 <i id="typeSortU2" class="icon_sort up" onclick="sortTD(2);"></i><i id="typeSortD2" class="icon_sort down" onclick="reverseTD(2);"></i>
                                    </th>
                                    <th scope="col">
                                        거래일시(KST) <i id="typeSortU3" class="icon_sort up" onclick="sortTD(3);"></i><i id="typeSortD3" class="icon_sort down" onclick="reverseTD(3);"></i>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr><td colspan="4">등록된 정보가 없습니다.</td></tr>                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                                                    </div>
                    </div>
                </div>
            </div>
        </div><!-- /.row -->
    </div>

    <script type="text/javascript">var myTable=document.getElementById("tokenOneTransTable");var replace=replacement(myTable);function sortTD(index){if(index==2){$('#typeSortU2').removeClass('up').addClass('cup');$('#typeSortD2').removeClass('cdown').addClass('down');$('#typeSortU3').removeClass('cup').addClass('up');$('#typeSortD3').removeClass('cdown').addClass('down');}else if(index==3){$('#typeSortU2').removeClass('cup').addClass('up');$('#typeSortD2').removeClass('cdown').addClass('down');$('#typeSortU3').removeClass('up').addClass('cup');$('#typeSortD3').removeClass('cdown').addClass('down');}replace.ascending(index);}function reverseTD(index){if(index==2){$('#typeSortU2').removeClass('cup').addClass('up');$('#typeSortD2').removeClass('down').addClass('cdown');$('#typeSortU3').removeClass('cup').addClass('up');$('#typeSortD3').removeClass('cdown').addClass('down');}else if(index==3){$('#typeSortU2').removeClass('cup').addClass('up');$('#typeSortD2').removeClass('cdown').addClass('down');$('#typeSortU3').removeClass('cup').addClass('up');$('#typeSortD3').removeClass('down').addClass('cdown');}replace.descending(index);}</script>

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

    <script>
    $(document).ready(function(){
      var r = '<?=$r?>';
      if(r.length > 0){
        if(r == 'success'){
          alert("출금 신청에 성공했습니다.");
          location.href = '/history.php?coin=<?=$coin?>';
        }
      }
    });
    </script>
  
</body></html>