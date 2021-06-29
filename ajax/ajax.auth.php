<?php

include_once('_common.php');
require_once '/var/www/html/vendor/autoload.php';

use Twilio\Rest\Client;

$type = $_POST["type"];

if($type == "sendauth"){
    $ipaddress = getUserIpAddr();//'123.123.123.123';
    // getIPAddress();
    $phone = $_POST["phone"];
    $origin_phone = $_POST["origin_phone"];
    
    $time = new DateTime();
    $datetime = $time->format('Y-m-d H:i:s');

    $time->add(new DateInterval('PT' . 3 . 'M'));
    $expiretime = $time->format('Y-m-d H:i:s');

    $time3 = new DateTime();
    $time3->sub(new DateInterval('PT' . 3 . 'M'));
    $datetime3 = $time3->format('Y-m-d H:i:s');

    $sqlc = "SELECT * FROM members WHERE phone_number = '$origin_phone'";
    $resultc = mysqli_query($conn, $sqlc);
    // // $rowc = mysqli_fetch_assoc($resultc);
    // $countc = $rowc['cnt'];

    $sqld = "SELECT * FROM authentications WHERE phone = '$phone' and expire_at > '$datetime'";
    $resultd = mysqli_query($conn, $sqld);
    // $rowd = mysqli_fetch_assoc($resultd);
    // $countd = $rowd['cnt'];


    $sqle = "SELECT * FROM authentications WHERE ipaddress = '$ipaddress' and created_at >= '$datetime3'";
    $resulte = mysqli_query($conn, $sqle);
    // $rowe = mysqli_fetch_assoc($resulte);
    // $countde = $rowe['cnt'];

    if(mysqli_num_rows($resultc) > 0){
        echo json_encode(array("success"=>"failed", "reason"=>"used"));
    }
    else if(mysqli_num_rows($resultd) > 0){
        echo json_encode(array("success"=>"failed", "reason"=>"using"));
    }
    else if(mysqli_num_rows($resulte) > 2){
        echo json_encode(array("success"=>"failed", "reason"=>"ipduplicate"));
    }
    else{
        $sid    = "AC8b8025a2e24d15dd585c3926b349cec3";
        $token  = "00834929879d1ce663af25bfef22c26c";
        $twilio = new Client($sid, $token);
        
        $authcode = RandomNumberGenerator();

        $body = "MEDIWELL 지갑 가입 인증번호 [".$authcode."]";
        
        $message = $twilio->messages 
                        ->create($phone, // to
                        // ["body" => $body, "from" => "+12057515682"]
                            ["body" => $body, "from" => "+12027690618"]
                        );
        
        // print($message->sid);
        $sql = "INSERT INTO authentications (phone, authcode, created_at, expire_at, ipaddress) VALUES ('$phone', '$authcode', '$datetime', '$expiretime', '$ipaddress')";
        if($conn->query($sql)){
            echo json_encode(array("success"=>"success", "message"=>$message->sid));
        }
        else{
            echo json_encode(array("success"=>"failed", "reason"=>$sql));
        }
    }
}
else if($type == "sendauth2"){
    $phone = $_POST["phone"];
    
    $time = new DateTime();
    $datetime = $time->format('Y-m-d H:i:s');

    $time->add(new DateInterval('PT' . 3 . 'M'));
    $expiretime = $time->format('Y-m-d H:i:s');

    $phoneo = $_POST["phoneo"];
    $username = $_POST["username"];
    
    $sqlc = "SELECT count(*) as cnt FROM members WHERE phone_number = '$phoneo' and username = '$username'";
    $resultc = mysqli_query($conn, $sqlc);
    $rowc = mysqli_fetch_assoc($resultc);
    $countc = $rowc['cnt'];

    $sqld = "SELECT count(*) as cnt FROM authentications WHERE phone = '$phone' and expire_at > '$datetime'";
    $resultd = mysqli_query($conn, $sqld);
    $rowd = mysqli_fetch_assoc($resultd);
    $countd = $rowd['cnt'];

    if($countc == 0){
        echo json_encode(array("success"=>"failed", "reason"=>"nomember"));
    }
    else if($countd > 0){
        echo json_encode(array("success"=>"failed", "reason"=>"using"));
    }
    else{
        $sid    = "AC8b8025a2e24d15dd585c3926b349cec3";
        $token  = "00834929879d1ce663af25bfef22c26c";
        $twilio = new Client($sid, $token);
        
        $authcode = RandomNumberGenerator();

        $body = "MEDIWELL 지갑 비밀번호 찾기 인증번호 [".$authcode."]";
        
        $message = $twilio->messages
                        ->create($phone, // to
                        // ["body" => $body, "from" => "+12057515682"]
                            ["body" => $body, "from" => "+12027690618"]
                        );
        
        // print($message->sid);
        $sql = "INSERT INTO authentications (phone, authcode, created_at, expire_at) VALUES ('$phone', '$authcode', '$datetime', '$expiretime')";
        if($conn->query($sql)){
            echo json_encode(array("success"=>"success", "message"=>$message->sid));
        }
        else{
            echo json_encode(array("success"=>"failed", "reason"=>$sql));
        }
    }
}
else if($type == "sendauth3"){
    $phone = $_POST["phone"];
    
    $time = new DateTime();
    $datetime = $time->format('Y-m-d H:i:s');

    $time->add(new DateInterval('PT' . 3 . 'M'));
    $expiretime = $time->format('Y-m-d H:i:s');

    $phoneo = $_POST["phoneo"];
    $username = $_POST["username"];
    
    $sqlc = "SELECT count(*) as cnt FROM members WHERE phone_number = '$phoneo'";
    $resultc = mysqli_query($conn, $sqlc);
    $rowc = mysqli_fetch_assoc($resultc);
    $countc = $rowc['cnt'];

    $sqld = "SELECT count(*) as cnt FROM authentications WHERE phone = '$phone' and expire_at > '$datetime'";
    $resultd = mysqli_query($conn, $sqld);
    $rowd = mysqli_fetch_assoc($resultd);
    $countd = $rowd['cnt'];

    if($countc == 0){
        echo json_encode(array("success"=>"failed", "reason"=>"nomember"));
    }
    else if($countd > 0){
        echo json_encode(array("success"=>"failed", "reason"=>"using"));
    }
    else{
        $sid    = "AC8b8025a2e24d15dd585c3926b349cec3";
        $token  = "00834929879d1ce663af25bfef22c26c";
        $twilio = new Client($sid, $token);
        
        $authcode = RandomNumberGenerator();

        $body = "MEDIWELL 지갑 아이디 찾기 인증번호 [".$authcode."]";
        
        $message = $twilio->messages
                        ->create($phone, // to
                        // ["body" => $body, "from" => "+12057515682"]
                            ["body" => $body, "from" => "+12027690618"]
                        );
        
        // print($message->sid);
        $sql = "INSERT INTO authentications (phone, authcode, created_at, expire_at) VALUES ('$phone', '$authcode', '$datetime', '$expiretime')";
        if($conn->query($sql)){
            echo json_encode(array("success"=>"success", "message"=>$message->sid));
        }
        else{
            echo json_encode(array("success"=>"failed", "reason"=>$sql));
        }
    }
}
else if($type == "checkauth"){ 
    $phone = $_POST["phone"];
    $authcode = $_POST["auth"];

    $datetime = date('Y-m-d H:i:s');
    $sql = "SELECT count(*) as cnt FROM authentications WHERE phone = '$phone' and authcode = '$authcode' and expire_at > '$datetime' and is_authed = 0";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $count = $row['cnt'];

    if($count == 1){
        $sqlu = "UPDATE authentications SET is_authed = 1 WHERE phone = '$phone' and authcode = '$authcode'";
        if($conn->query($sqlu)){
            echo json_encode(array("success"=>"success"));
        }
    }
    else{
        echo json_encode(array("success"=>"failed"));
    }


}

function RandomNumberGenerator(){
    $six_digit_random_number = mt_rand(100000, 999999);

    return $six_digit_random_number;
}
function getUserIpAddr(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        //ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        //ip pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
?>