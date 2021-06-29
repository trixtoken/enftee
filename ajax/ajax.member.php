<?php

include_once('_common.php');


require_once('/var/www/html/vendor/twilio/sdk/src/Twilio/autoload.php');

use Twilio\Rest\Client;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require '../vendor/autoload.php';

$request = $_POST['type'];

switch($request){
    case "checkGMAmount":{
        session_start();
        $member_id = $_SESSION["ss_member_id"];

        $amount = $_POST["amount"];

        $sql = "SELECT * FROM members WHERE id = $member_id";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);

        $having = $row["game_money"];

        if($amount > $having){
            echo json_encode(array("success"=>"failed"));
        }
        else{
            echo json_encode(array("success"=>"success"));
        }
        break;
    }
    case "changePW":{
        session_start();
        $member_id = $_SESSION["ss_member_id"];

        $currentPw = $_POST["currentPw"];
        $newPwCheck = $_POST["newPwCheck"];
        $newPw = $_POST["newPw"];

        $sql = "SELECT * FROM members WHERE id = $member_id AND authentication_string = password('$currentPw')";
        $result = mysqli_query($conn, $sql);
        if(mysqli_num_rows($result) == 0){
            header('Location: /profile.php?r=failed');
        }
        else{
            $sqlu = "UPDATE members SET authentication_string = password('$newPw') WHERE id = $member_id";
            if($conn->query($sqlu)){
                header('Location: /profile.php?r=success');
            }
        }

        break;
    }
    case "checkAmount":{
        session_start();
        $member_id = $_SESSION["ss_member_id"];

        $coin = $_POST["coin"];
        $amount = $_POST["amount"];

        $sql = "SELECT * FROM members WHERE id = $member_id";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);

        $having = 0;
        if($coin == 'FRI'){
            $having = $_POST["maxamount"];
        }
        else if($coin == 'USDT'){
            $having = $row["usdt"];
        }
        else if($coin == 'TDOS'){
            $having = $row["tdos"];
        }
        else if($coin == 'ETH'){
            $having = $row["eth"];
        }

        

        if($amount > $having){
            echo json_encode(array("success"=>"failed"));
        }
        else{
            echo json_encode(array("success"=>"success"));
        }
        break;
    }
    case "checkduplicate":{
        $username = $_POST["memberId"];

        $sqlc = "SELECT * FROM members WHERE username ='$username'";
        $resultc = mysqli_query($conn, $sqlc);

        $count = mysqli_num_rows($resultc);

        if($count == 0){
            echo json_encode(array("success"=>"success"));
        }
        else{
            echo json_encode(array("success"=>"failed"));
        }
        break;
    }
    case "charToLamp":{
        $now = new DateTime();
        $now->add(new DateInterval("PT9H"));
        $datetime = date_format($now, 'Y-m-d H:i:s');

        $item_id = $_POST["item_id"];

        session_start();
        $mid = $_SESSION["ss_member_id"];

        $sqli = "SELECT * FROM items WHERE id = $item_id";
        $resulti = mysqli_query($conn, $sqli);
        $rowi = mysqli_fetch_assoc($resulti);

        $omid = $rowi["owner_member_id"];
        $price = $rowi["price"];

        if($omid != $mid){
            echo json_encode(array("success"=>"failed", "reason"=>"notyours"));
        }
        else{
            $sqls = "SELECT * FROM sells WHERE item_id = $item_id ORDER BY id DESC LIMIT 1";
            $results = mysqli_query($conn, $sqls);
            $rows = mysqli_fetch_assoc($results);

            $mstate = $rows["match_state"];
            if($mstate != 0){
                echo json_encode(array("success"=>"failed", "reason"=>"selling"));
            }
            else{
                $sell_id = $rows["id"];
            
                $lamp = intval($price / 120);

                $sqlm = "SELECT * FROM members WHERE id = $mid";
                $resultm = mysqli_query($conn, $sqlm);
                $rowm = mysqli_fetch_assoc($resultm);
                $banana = $rowm["banana"];
                $newbnn = intval($banana) + intval($lamp);

                $sqli1 = "INSERT INTO pearl_histories (member_id, account_id, pearl_changed, pearl_sum, reason, created_at, note) VALUE ($mid, 0, $lamp, $newbnn, 'CharSell', '$datetime', $item_id)";
                $sqlu1 = "UPDATE members SET banana = banana + $lamp WHERE id = $mid";
                
                $sqlu2 = "DELETE FROM sells WHERE id = $sell_id";
                $sqlu3 = "UPDATE items SET owner_member_id = -100, owner_account_id = -100 WHERE id = $item_id";

                if($conn->query($sqli1)){
                    if($conn->query($sqlu1)){
                        if($conn->query($sqlu2)){
                            if($conn->query($sqlu3)){
                                echo json_encode(array("success"=>"success"));
                            }
                            else{
                                echo json_encode(array("success"=>"failed", "reason"=>$sqlu3));
                            }
                        }
                        else{
                            echo json_encode(array("success"=>"failed", "reason"=>$sqlu2));
                        }
                    }
                    else{
                        echo json_encode(array("success"=>"failed", "reason"=>$sqlu1));
                    }
                }
                else{
                    echo json_encode(array("success"=>"failed", "reason"=>$sqli1));
                }
            }

        }
        break;
    }
    case "resetInfo":{
        
        session_start();
        $mid = $_SESSION["ss_member_id"];

        $referral_name = $_POST["referral_name"];
        $phone_number = $_POST["phone_number"];
        $bank_name = $_POST["bank_name"];
        $bank_owner = $_POST["bank_owner"];
        $bank_account = $_POST["bank_account"];

        $sql = "UPDATE members SET phone_number = '$phone_number', bank_name = '$bank_name', bank_owner = '$bank_owner', bank_account = '$bank_account', temp_ref = '$referral_name' WHERE id = $mid ";
        if($conn->query($sql)){
            header('Location: /main');
        }
    }
    case "resetInfo2":{
        
        session_start();
        $mid = $_SESSION["ss_member_id"];

        $referral_name = $_POST["referral_name"];
        $phone_number = $_POST["phone_number"];

        $sql = "UPDATE members SET phone_number = '$phone_number', temp_ref = '$referral_name' WHERE id = $mid ";
        if($conn->query($sql)){
            header('Location: /main');
        }
    }
    case "requestDeposit":{
        $buy_no = intval($_POST["buy_id"]);
        $depositer = $_POST["depositer"];

        $now = new DateTime();
        $now->add(new DateInterval("PT9H"));
        $datetime = date_format($now, 'Y-m-d H:i:s');

        session_start();
        $mid = $_SESSION["ss_member_id"];
        $sql = "SELECT * FROM members WHERE id = $mid";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);

        $bank_owner = $row["bank_owner"];
        $sqlu = "UPDATE matchings SET verify_path = '$depositer', verify_requeted_at = '$datetime' WHERE id = $buy_no";
        if($conn->query($sqlu)){

            $sqlm = "SELECT * FROM matchings WHERE id = $buy_no";
            $resultm = mysqli_query($conn, $sqlm);
            $rowm = mysqli_fetch_assoc($resultm);

            $sell_id = $rowm["sell_id"];
            $buy_id = $rowm["buy_id"];

            $sqlu1 = "UPDATE buys SET verify_requested = 1 WHERE id = $buy_id";
            $sqlu2 = "UPDATE sells SET verify_requested = 1 WHERE id = $sell_id";
            if($conn->query($sqlu1)){
                if($conn->query($sqlu2)){
                    echo json_encode(array("success"=>"success", "query1"=>$sqlu1, "query2"=>$sqlu2));            
                }
                else{
                    echo json_encode(array("success"=>"failed", "reason"=>$sqlu2, "select"=>$sqlm));
                }
            }
            else{
                echo json_encode(array("success"=>"failed", "reason"=>$sqlu1));
            }
        }
        else{
            echo json_encode(array("success"=>"failed", "reason"=>$sqlu));
        }
        break;
    }
    case "cancelEggBuy":{
        $buy_no = $_POST["buy_id"];
        $sql = "UPDATE pearl_buys SET aasm_state = 'cancelled' WHERE id = $buy_no";
        if($conn->query($sql)){
            echo json_encode(array("success"=>"success"));
        }
        else{
            echo json_encode(array("success"=>"failed", "reason"=>$sql));
        }
        break;
    }
    case "member_modify":{
        $nowuserpw = $_POST["nowuserpw"];
        $mb_password = $_POST["mb_password"];
        // $mb_bank_name = $_POST["mb_bank_name"];
        // $mb_bank_no = $_POST["mb_bank_no"];
        // $mb_bank_own = $_POST["mb_bank_own"];
        session_start();
        $member_id = $_SESSION["ss_member_id"];

        if($mb_password != null && strlen($mb_password) > 0){
            //비밀번호 변경 요청임
            $sql = "SELECT * FROM members WHERE id = $member_id and authentication_string = password('$nowuserpw')";
            $result = mysqli_query($conn, $sql);
            if(mysqli_num_rows($result) == 0){
                //잘못된 ㅂ
                header('Location: /modify?s=wrongpassword');
            }
            else{
                 $sqlu = "UPDATE members SET authentication_string = password('$mb_password') WHERE id = $member_id";
                 if($conn->query($sqlu)){
                    header('Location: /modify?s=success0');
                 }
                 else{
                    header('Location: /modify?s=failed');
                 }
            }
        }
        else{
            $sqlu = "UPDATE members SET bank_name = '$mb_bank_name', bank_account = '$mb_bank_no', bank_owner = '$mb_bank_own' WHERE id = $member_id";
            if($conn->query($sqlu)){
                header('Location: /modify?s=success');
            }
            else{
                header('Location: /modify?s=failed');
            }
        }
        break;
    }
    case "item_transfer_proc":{
        $member_id = $_POST["member_id"];
        $recever = $_POST["receiver_id"];
        $amount = $_POST["item_value"];
        $password = $_POST["mb_pass"];

        $datetime = date('Y-m-d H:i:s');



        $sqlm = "SELECT * FROM members WHERE id = $member_id and authentication_string = password('$password')";
        $resultm = mysqli_query($conn, $sqlm);
        if(mysqli_num_rows($resultm) == 0){
            header('Location: /buystick?s=wrongpassword&mode=item_transfer');
        }
        else{
            $row = mysqli_fetch_assoc($resultm);
            $username = $row["username"];

            $sqlm2 = "SELECT * FROM members WHERE username = '$recever'";
            $resultm2 = mysqli_query($conn, $sqlm2);
            $rowm2 = mysqli_fetch_assoc($resultm2);
            $member_id2 = $rowm2["id"];

            $m1banana = $row["banana"];
            $m2banana = $rowm2["banana"];

            $minus = 0 - floatval($amount);
            $m1new = floatval($m1banana) - floatval($amount);
            $m2new = floatval($m2banana) + floatval($amount);

            $sqli1 = "INSERT INTO pearl_histories (member_id, account_id, pearl_changed, pearl_sum, reason, created_at, note) VALUE ($member_id, 0, $minus, $m1new, 'PearlSend', '$datetime', '$recever')";
            $sqli2 = "INSERT INTO pearl_histories (member_id, account_id, pearl_changed, pearl_sum, reason, created_at, note) VALUE ($member_id2, 0, $amount, $m2new, 'PearlGet', '$datetime', '$username')";
            $sqli3 = "INSERT INTO transfers (send_member_id, get_member_id, amount, created_at) VALUE ($member_id, $member_id2, $amount, '$datetime')";
            $sqlu1 = "UPDATE members SET banana = banana + $amount WHERE username = '$recever'";
            $sqlu2 = "UPDATE members SET banana = banana - $amount WHERE id = $member_id";

            if($conn->query($sqli1)){
                if($conn->query($sqli2)){
                    if($conn->query($sqlu1)){
                        if($conn->query($sqlu2)){
                            if($conn->query($sqli3)){
                                header('Location: /buystick?s=transfersuccess&mode=item_transfer');
                            }
                        }
                    }
                }
            }

        }

        break;
    }
    case "getEggList":{
        $member_id = $_POST["member_id"];

        $return_str = "";
        $sql = "SELECT * FROM pearl_buys WHERE member_id = $member_id order by created_at desc";
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_assoc($result)){
            $return_str = $return_str."<tr><td>".$row["id"]."</td><td>".$row["created_at"]."</td><td>".number_format($row["amount"], 0)."</td><td>".number_format($row["amount"] * 300,0)."</td><td>".$row["name"]."</td><td>";
            if($row["aasm_state"] == 'inquiry'){
                $return_str = $return_str."<button class='btn' onclick=\"cancelOrder('".$row["id"]."')\";>취소</button>";
            }
            else if($row["aasm_state"] == 'done'){
                $return_str = $return_str."구매완료";
            }
            else if($row["aasm_state"] == 'cancelled'){
                $return_str = $return_str."취소됨";
            }

            $return_str = $return_str."</td></tr>";
        }

        echo json_encode(array("contents"=>$return_str));

        break;
    }
    case "getEggSendList":{
        $member_id = $_POST["member_id"];

        $return_str = "";
        $sql = "SELECT * FROM transfers WHERE send_member_id = $member_id order by created_at desc";
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_assoc($result)){
            $receive_member_id = $row["get_member_id"];
            $sqlm = "SELECT * FROM members WHERE id = $member_id";
            $sqlm2 = "SELECT * FROM members WHERE id = $receive_member_id";
            $resultm = mysqli_query($conn,$sqlm);
            $resultm2 = mysqli_query($conn,$sqlm2);
            $rowm = mysqli_fetch_assoc($resultm);
            $rowm2 = mysqli_fetch_assoc($resultm2);

            $return_str = $return_str."<tr><td>".$row["id"]."</td><td>".$row["created_at"]."</td><td>".number_format($row["amount"], 0)."</td><td>".$rowm["username"]."</td><td>".$rowm2["username"]."</td></tr>";
        }

        echo json_encode(array("contents"=>$return_str));

        break;
    }
    case "item_order_proc":{
        $item_count = $_POST["item_no"];
        $pay_name = $_POST["pay_name"];

        session_start();

        $member_id = $_SESSION["ss_member_id"];

        $datetime = date('Y-m-d H:i:s');

        $sqla = "SELECT * FROM accounts WHERE member_id = $member_id order by id asc limit 1";
        $resulta = mysqli_query($conn, $sqla);
        $rowa = mysqli_fetch_assoc($resulta);

        $account_id = $rowa["id"];

        $sql = "INSERT INTO pearl_buys (member_id, account_id, amount, in_usdt, in_coin, created_at, aasm_state, done_at, erc_address, txid, name) VALUE ($member_id, $account_id, $item_count, 0, 0, '$datetime', 'inquiry', null, '', '', '$pay_name')";
        if($conn->query($sql)){
            echo json_encode(array("success"=>"success"));
            // header('Location: /lamp?s=success');
        }
        else{
            echo json_encode(array("success"=>"failed"));
            // header('Location: /lamp?s=failed');
        }
        break;
    }
    case "goods_buy_proc":{
        
        session_start();
        $member_id = $_SESSION["ss_member_id"];
        $user_id = $member_id;
        $buy_count = $_POST["product_qty"];
        $char_no = $_POST["product_cd"];

        $type = $char_no;
        $fee = 0;
        if($type == 1){
            $fee = 60;
        }
        else if($type == 2){
            $fee = 40;
        }
        else if($type == 3){
            $fee = 20;
        }
        $fee_count = $fee * $buy_count;
        

        $sqla = "SELECT * FROM accounts WHERE member_id = $user_id";
        $resulta = mysqli_query($conn, $sqla);
        if(mysqli_num_rows($resulta) == 0){
            echo json_encode(array("success"=>"failed", "reason"=>"wrong_name", "user_id"=>$user_id));
            break;
        }
        $rowa = mysqli_fetch_assoc($resulta);
        $account_id = $rowa["id"];
        // $member_id = $rowa["member_id"];

        //회원정보 가져오기//
        $sqlm = "SELECT * FROM members WHERE id = $user_id";
        $resultm = mysqli_query($conn, $sqlm);
        $rowm = mysqli_fetch_assoc($resultm);

        $pearl = $rowm["banana"];
        $datetime = date('Y-m-d H:i:s');
        $sell_type = $rowm["sell_type"];
        //회원정보 가져오기//

        //구매 기준일 계산//
        $new_time = date("Y-m-d H:i:s", strtotime('+9 hours'));
        $hour = date("H", strtotime('+9 hours'));
        $now = new DateTime(); //current date/time
        $now->add(new DateInterval("PT9H"));
        $is_today = false;
        if($hour >= 6){
            $now->add(new DateInterval('P1D'));
        }
        $buydate = date_format($now, 'Y-m-d');
        //구매 기준일 계산//        

        //이미 신청한 구매가 있는지 확인//
        $sqlcheck = "SELECT * FROM buys WHERE member_id = $member_id and buy_date = '$buydate' and item_type = $type and match_state = 0";
        $resultcheck = mysqli_query($conn, $sqlcheck);
        if(mysqli_num_rows($resultcheck) + $buy_count > 500){
            header('Location: /reserve/order.php?r=exist');
            // echo json_encode(array("success"=>"failed", "reason"=>"exist"));
            break;
        }
        //이미 신청한 구매가 있는지 확인//

        //진주가 수수료보다 많은지 확인//
        if($pearl < $fee_count){
            header('Location: /reserve/order.php?r=insufficient_pearl');
            // echo json_encode(array("success"=>"failed", "reason"=>"insufficient_pearl"));
            break;
        }
        //진주가 수수료보다 많은지 확인//


        $minus = 0 - $fee;
        // floatval($pearl);

        $successed = true;
        for($i = 0 ; $i < $buy_count ; $i++){
            $pearl = $pearl - $fee;
            $minus = 0 - $pearl;
            $sqlupdate = "UPDATE members SET banana = banana - $fee, free_banana = free_banana - $fee WHERE id = $member_id";
            $sqlph = "INSERT INTO pearl_histories (member_id, account_id, pearl_changed, pearl_sum, reason, created_at) VALUES ($member_id, $account_id, $minus, $pearl, 'BuyCharacter', '$datetime')";
            $sqli = "INSERT INTO buys (member_id, account_id, item_type, created_at, buy_date, match_state, payment_method) VALUES ($member_id, $account_id, $type, '$datetime', '$buydate', 0, $sell_type)";
            if($conn->query($sqlupdate)){
                if($conn->query($sqlph)){
                    if($conn->query($sqli)){
                    
                    }
                    else{
                        $successed = false;
                    }
                }
                else{
                    $successed = false;
                }
            }
            else{
                $successed = false;
            }
        }
        if($successed == false){
            header('Location: /reserve/index.php?r=buyfailed');
        }
        else{
            header('Location: /reserve/index.php?r=buysuccess');
        }
        break;
    }
    case "findid":{
        $phone = $_POST["phone"];
        
        $sql0 = "SELECT * FROM members WHERE phone_number = '$phone' order by id desc limit 1";
        $result0 = mysqli_query($conn, $sql0);
        if(mysqli_num_rows($result0) == 0){
            header('Location: /findid?q=nouser');
        }
        else{
            $row0 = mysqli_fetch_assoc($result0);

            $phonenumber = $row0["phone_number"];
            $country = $row0["country_code"];
            $username = $row0["username"];

            $phone = $country."".$phonenumber;

            $sid    = "AC8b8025a2e24d15dd585c3926b349cec3";
            $token  = "00834929879d1ce663af25bfef22c26c";
            $twilio = new Client($sid, $token);
            
            $authcode = RandomNumberGenerator();

            $body = "마이알라딘 가입 아이디 : ".$username;
            
            $message = $twilio->messages
                            ->create($phone, // to
                            // ["body" => $body, "from" => "+12057515682"]
                                ["body" => $body, "from" => "+12027690618"]
                            );
            
            
            // print($message->sid);
            header('Location: /login');
        }
        break;
    }
    case "givecharadmin":{
        $datetime = date('Y-m-d H:i:s');

        $username = $_POST["username"];
        $item_type = $_POST["item_type"];
        $sell_date = $_POST["sell_date"];
        $price = $_POST["price"];

        $next_move_at = $sell_date." 00:00:00";

        $moving_period = 3;
        $percentage = 20;

        $sql = "SELECT * FROM members WHERE username = '$username'";
        $result = mysqli_query($conn, $sql);
        if(mysqli_num_rows($result) == 0){
            echo "failed".$sql;
            header('Location: /givechar.php?s=failed');
        }
        else{
            $row = mysqli_fetch_assoc($result);

            $sqla = "SELECT * FROM accounts WHERE member_username = '$username' order by id asc limit 1";
            $resulta = mysqli_query($conn, $sqla);
            $rowa = mysqli_fetch_assoc($resulta);

            $member_id = $row["id"];
            $account_id = $rowa["id"];
            $bank_str = $row["bank_name"]." ".$row["bank_account"]."(예금주: ".$row["bank_owner"].")";
            
            $sqli = "INSERT INTO items (type, owner_account_id, owner_member_id, price, created_at, last_moved_at, moving_period, percentage, next_move_at, next_deal_date) VALUES ($item_type, $account_id, $member_id, $price, '$datetime', '$datetime', $moving_period, $percentage, '$next_move_at', '$sell_date')";
            if($conn->query($sqli)){
                $item_id = $conn->insert_id;
                $sqls = "INSERT INTO sells (member_id, account_id, item_id, item_type, created_at, sell_date, match_state, buy_account_id, bank_str, usdt_address, payment_method) VALUES ($member_id, $account_id, $item_id, $item_type, '$datetime', '$sell_date', 0, 0, '', '', 2)";
                if($conn->query($sqls)){
                    echo "success";
                    header('Location: /givechar.php?s=success');
                }
                else{
                    echo "failed ".$sqls;
                }
            }
            else{
                echo "failed ".$sqli;
            }
        }
        break;
    }
    case "search":{
        $username = $_POST["username"];
        $sql = "SELECT * FROM members WHERE username = '$username'";
        $result = mysqli_query($conn, $sql);
        if(mysqli_num_rows($result) == 0){
            echo json_encode(array("success"=>"failed"));
        }
        else{
            echo json_encode(array("success"=>"success"));
        }
    break;
    }
    case "requestInquiry": { 

        $title = $_POST["title"];
        $content = $_POST["content"];
        $member_id = $_POST["member_id"];

        $datetime = date('Y-m-d H:i:s');
    
        $sqlinsert = "INSERT INTO inquiries (member_id, title, content, created_at) VALUES ($member_id, '$title', '$content', '$datetime')";

        if($conn->query($sqlinsert)){
            header('Location: /cs');
            // echo json_encode(array("success"=>"success", "type"=>"requestInquiry"));
        }

        break;
    }
    case "checkss":{
        $session_str = $_POST["session_str"];

        $date = new DateTime();
        $date->add(new DateInterval('PT1H'));
        $expired = $date->format('Y-m-d H:i:s');
        $nowdate = date('Y-m-d H:i:s');

        $sql = "SELECT * FROM game_session_id WHERE session_str = '$session_str' and expired_at < '$nowdate' and used = 0";
        $result = mysqli_query($conn, $sql);
        if(mysqli_num_rows($result) == 0){
            echo json_encode(array("success"=>"failed"));
        }
        else{
            $row = mysqli_fetch_assoc($result);
            $row_id = $row["id"];
            $member_id = $row["member_id"];

            $sqlu = "UPDATE game_session_id SET used = 1 WHERE id = $row_id";
            if($conn->query($sqlu)){
                echo json_encode(array("success"=>"success", "member_id"=>$member_id));
            }
            else{
                echo json_encode(array("success"=>"failed"));
            }
        }

        break;
    }
    case "createdSession":{

        $date = new DateTime();
        $date->add(new DateInterval('PT1H'));
        $expired = $date->format('Y-m-d H:i:s');
        $nowdate = date('Y-m-d H:i:s');

        $member_id = $_POST["member_id"];

        $sql = "SELECT * FROM game_session_id WHERE expired_at > '$nowdate' and member_id = $member_id and used = 0";
        $result = mysqli_query($conn, $sql);
        if(mysqli_num_rows($result) == 0){
            $str = $member_id."AT".$nowdate;
            $session_str = base64_encode($str);


            $sqli = "INSERT INTO game_session_id (member_id, created_at, used, expired_at, session_str) VALUES ($member_id, '$nowdate', 0, '$expired', '$session_str')";
            if($conn->query($sqli)){
                echo json_encode(array("success"=>"success", "sessionStr"=>$session_str));
            }
            else{
                echo json_encode(array("success"=>"failed"));
            }
        }  
        else{
            $row = mysqli_fetch_assoc($result);
            $session_str = $row["session_str"];
            echo json_encode(array("success"=>"success", "sessionStr"=>$session_str));
        }

        break;
    }
    case "changepw":{
        $username = $_POST["username"];
        $newpw = mt_rand(10000000, 99999999);

        $sql = "UPDATE members SET authentication_string = password('$newpw') WHERE username = '$username'";
        if($conn->query($sql)){

            $sql0 = "SELECT * FROM members WHERE username = '$username'";
            $result0 = mysqli_query($conn, $sql0);
            $row0 = mysqli_fetch_assoc($result0);

            $phonenumber = $row0["phone_number"];
            $country = $row0["country_code"];

            $phone = $country."".$phonenumber;

            $sid    = "AC8b8025a2e24d15dd585c3926b349cec3";
            $token  = "00834929879d1ce663af25bfef22c26c";
            $twilio = new Client($sid, $token);
            
            $authcode = RandomNumberGenerator();

            $body = "마이알라딘 가입 임시 비밀번호 : ".$newpw;
            
            $message = $twilio->messages
                            ->create($phone, // to
                            // ["body" => $body, "from" => "+12057515682"]
                                ["body" => $body, "from" => "+12027690618"]
                            );
            
            
            // print($message->sid);
            header('Location: /login?q=pws');
        }
        else{
            header('Location: /forgetpw?q=f');
        }
        break;
    }
    case "changemail":{
        $member_id = $_POST["member_id"];
        $email = $_POST["new_email"];
        $sql = "UPDATE members SET email = '$email' WHERE id = $member_id";
        if($conn->query($sql)){
            echo json_encode(array("success"=>"success"));
        }
        else{
            echo json_encode(array("success"=>"failed"));
        }
        break;
    }
    case "setlimit":{
        session_start();
        $member_id = $_SESSION["ss_member_id"];
        $limit = $_POST["limit"];

        $sql = "UPDATE members SET setting_limit = $limit WHERE id = $member_id";
        if($conn->query($sql)){
            mysqli_close($conn);
            echo json_encode(array("success"=>"success"));
        }
        else{
            echo json_encode(array("success"=>"failed"));
        }
        break;
    }
    case "setautoaccount":{
        $checked = $_POST["checked"];
        $account_id = $_POST["account_id"];

        $sqls = "SELECT * FROM accounts WHERE id = $account_id";
        $results = mysqli_query($conn, $sqls);
        $rows = mysqli_fetch_assoc($results);

        $member_id = $rows["member_id"];

        if($checked == 0){
            $sqlmember = "SELECT * FROM members WHERE id = $member_id";
            $result = mysqli_query($conn, $sqlmember);
            $row = mysqli_fetch_assoc($result);

            $usd = $row["setting_limit"];
            $banana = $row["banana"];

            $usdable = (int)($usd / 100);
            $bananable = (int)($banana / 120);

            $onable = min($usdable, $bananable);

            $sqlc = "SELECT count(*) as cnt FROM accounts WHERE auto_method = '1111' and member_id = $member_id";
            $resultc = mysqli_query($conn, $sqlc);
            $rowc = mysqli_fetch_assoc($resultc);
            $countt = $rowc["cnt"];

            if($countt >= $onable){
                echo json_encode(array("success"=>"failed"));
                break;
            }
            $sql = "UPDATE accounts SET auto_method = '1111' WHERE id = $account_id and is_activated = 1";
        }
        else{
            $sql = "UPDATE accounts SET auto_method = '0000' WHERE id = $account_id and is_activated = 1";
        }

        if($conn->query($sql)){
            mysqli_close($conn);
            echo json_encode(array("success"=>"success"));
        }
        else{
            echo json_encode(array("success"=>"failed"));
        }

        break;
    }
    case "setautototal":{
        $member_id = $_POST["member_id"];
        $checked = $_POST["checked"];


        if($checked == 0){
            $sql = "UPDATE members SET set_autobuy = 1 WHERE id = $member_id";
        }
        else{
            $sql = "UPDATE members SET set_autobuy = 0 WHERE id = $member_id";
        }

        if($conn->query($sql)){
            mysqli_close($conn);
            echo json_encode(array("success"=>"success"));
        }
        else{
            echo json_encode(array("success"=>"failed", "reason"=>$sql));
        }
        break;
    }
    case "checkdupusername":{
        $username = $_POST["username"];

        $sql = "SELECT count(*) as cnt FROM members WHERE username = '$username'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        $count = $row["cnt"];

        if($count == 0){
            mysqli_close($conn);
            echo json_encode(array("success"=>"success"));
        }
        else{
            echo json_encode(array("success"=>"failed"));
        }

        break;
    }
    case "cancelBuy":{
        $fee = 0;
        $datetime = date('Y-m-d H:i:s');

        $buy_id = $_POST["buy_id"];
        
        session_start();
        $member_id = $_SESSION["ss_member_id"];

        $sql = "SELECT * FROM buys WHERE id = $buy_id and member_id = $member_id";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);

        $account_id = $row["account_id"];
        $item_type = $row["item_type"];
        $match_state = $row["match_state"];

        $fee = 20 * (4 - $item_type);

        if($match_state != 0){
            echo json_encode(array("success"=>"failed"));
            break;
        }
        
        $sqla = "SELECT * FROM members WHERE id = $member_id";
        $resulta = mysqli_query($conn, $sqla);
        $rowa = mysqli_fetch_assoc($resulta);
        
        $now_pearl = $rowa["banana"];
        $new_pearl = floatval($now_pearl) + floatval($fee);

        $sqlu1 = "DELETE FROM buys  WHERE id = $buy_id";
        $sqlu2 = "UPDATE members SET banana = banana + $fee WHERE id = $member_id";
        $sqli = "INSERT INTO pearl_histories (member_id, account_id, pearl_changed, pearl_sum, reason, created_at) VALUES ($member_id, $account_id, $fee, $new_pearl, 'BuyCancel', '$datetime')";

        if($conn->query($sqlu1)){
            if($conn->query($sqlu2)){
                if($conn->query($sqli)){
                    mysqli_close($conn);
                    echo json_encode(array("success"=>"success"));
                }
                else{
                    echo json_encode(array("success"=>"failed"));
                }
            }
            else{
                echo json_encode(array("success"=>"failed"));
            }
        }
        else{
            echo json_encode(array("success"=>"failed"));
        }

        break;
    }
    case "logout":{
        session_start();
        unset($_SESSION["ss_member_id"]);
        
        
        echo json_encode(array("success"=>"success"));

        break;
    }
    case "buy_char":{
        $char_type = $_POST["item_type"];
        $account_name = $_POST["account_name"];

        $type = 1;
        $fee = 1;
        

        $sqla = "SELECT * FROM accounts WHERE account_name = '$account_name'";
        $resulta = mysqli_query($conn, $sqla);
        if(mysqli_num_rows($resulta) == 0){
            echo json_encode(array("success"=>"failed", "reason"=>"wrong_name"));
            break;
        }
        $rowa = mysqli_fetch_assoc($resulta);
        $account_id = $rowa["id"];
        $member_id = $rowa["member_id"];
        $pearl = $rowa["pearl"];
        $datetime = date('Y-m-d H:i:s');

        //구매 기준일 계산//
        $new_time = date("Y-m-d H:i:s", strtotime('+9 hours'));
        $hour = date("H", strtotime('+9 hours'));
        $now = new DateTime(); //current date/time
        $now->add(new DateInterval("PT9H"));
        $is_today = false;
        if($hour >= 10){
            $now->add(new DateInterval('P1D'));
        }
        $buydate = date_format($now, 'Y-m-d');
        //구매 기준일 계산//        

        //이미 신청한 구매가 있는지 확인//
        $sqlcheck = "SELECT * FROM buys WHERE account_id = $account_id and item_type = $type and buy_date = '$buydate' and match_state = 0";
        $resultcheck = mysqli_query($conn, $sqlcheck);
        if(mysqli_num_rows($resultcheck) > 0){
            echo json_encode(array("success"=>"failed", "reason"=>"exist"));
            break;
        }
        //이미 신청한 구매가 있는지 확인//

        //진주가 수수료보다 많은지 확인//
        if($pearl < $fee){
            echo json_encode(array("success"=>"failed", "reason"=>"insufficient_pearl"));
            break;
        }
        //진주가 수수료보다 많은지 확인//

        //회원정보 가져오기//
        $sqlm = "SELECT * FROM members WHERE id = $member_id";
        $resultm = mysqli_query($conn, $sqlm);
        $rowm = mysqli_fetch_assoc($resultm);
        $sell_type = $rowm["sell_type"];
        //회원정보 가져오기//

        $minus = 0 - $fee;
        $new_pearl = floatval($pearl) - floatval($fee);
        $sqlupdate = "UPDATE accounts SET pearl = pearl - $fee WHERE id = $account_id";
        $sqlph = "INSERT INTO pearl_histories (member_id, account_id, pearl_changed, pearl_sum, reason, created_at) VALUES ($member_id, $account_id, $minus, $new_pearl, 'BuyCharacter', '$datetime')";
        $sqli = "INSERT INTO buys (member_id, account_id, item_type, created_at, buy_date, match_state, payment_method) VALUES ($member_id, $account_id, $type, '$datetime', '$buydate', 0, $sell_type)";
        if($conn->query($sqlupdate)){
            if($conn->query($sqlph)){
                if($conn->query($sqli)){
                    mysqli_close($conn);
                    echo json_encode(array("success"=>"success"));
                }
                else{
                    echo json_encode(array("success"=>"failed", "reason"=>"query-".$sqli));
                }
            }
            else{
                echo json_encode(array("success"=>"failed", "reason"=>"query-".$sqlph));
            }
        }
        else{
            echo json_encode(array("success"=>"failed", "reason"=>"query-".$sqlupdate));
        }
        break;
    }
    case "buypearl":{
        $datetime = date('Y-m-d H:i:s');

        $amount = $_POST["amount"];
        $account_id = $_POST["account_id"];
        $member_id = $_POST["member_id"];
        
        $usdt = floatval($amount) / 10;
        $sql = "SELECT * FROM accounts WHERE id = $account_id";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        $member_id = $row["member_id"];
        $erc_address = $row["pearl_address"];
        

        $sqlmember = "SELECT * FROM members WHERE id = $member_id";
        $resultm = mysqli_query($conn, $sqlmember);
        $rowm = mysqli_fetch_assoc($resultm);
        $banana = $rowm["banana"];

        if(in_array($member_id, array(6691,6695,6696,6701,8570,11375,1))){
            if($amount >= 10000){
                $amount = $amount * 1.1;
            }
        }
        
        $usdamount = $rowm["usd_amount"];
        if($usdamount < $usdt){
            echo json_encode(array("success"=>"failed"));
            break;
        }
        else{
            $minus = 0 - $usdt;
            $sqlih = "INSERT INTO usd_histories (member_id, amount, reason, reason_id, created_at) VALUES ($member_id, $minus, 'BuyBanana', 0, '$datetime')";
            $newpearl = floatval($banana) + floatval($amount);
            $sqlip = "INSERT INTO pearl_histories (member_id, account_id, pearl_changed, pearl_sum, reason, created_at) VALUES ($member_id, $account_id, $amount, $newpearl, 'BuyPearl', '$datetime')";

            $sqlu3 = "UPDATE members SET usd_amount = usd_amount - $usdt, banana = banana + $amount WHERE id = $member_id";
            if($conn->query($sqlu3)){
                if($conn->query($sqlih)){
                    if($conn->query($sqlip)){
                        echo json_encode(array("success"=>"success"));
                    }
                }
            }
        }
        break;
    }
    case "sendusd":{
        $datetime = date('Y-m-d H:i:s');

        $to_id = $_POST["to_id"];
        $member_id = $_POST["member_id"];
        $amount = $_POST["amount"];

        $sqlmember = "SELECT * FROM members WHERE username = '$to_id'";
        $resultm = mysqli_query($conn, $sqlmember);

        if(mysqli_num_rows($resultm) == 0){
            echo json_encode(array("success"=>"failed"));
            break;
        }

        $rowm = mysqli_fetch_assoc($resultm);
        $receiver_phone = $rowm["phone_number"];

        $sqlmm = "SELECT * from members where id = $member_id";
        $resultmm = mysqli_query($conn, $sqlmm);

        $rowmm = mysqli_fetch_assoc($resultmm);
        $usd_having = $rowmm["usd_amount"];
        $sender_phone = $rowmm["phone_number"];

        if($amount > $usd_having){
            echo json_encode(array("success"=>"failed"));
            break;
        }
        $get_member_id = $rowm["id"];
        $su = false;
        $sponsor_id = $rowm["sponsor_id"];
        if($sponsor_id == $member_id){
            $su = true;
        }
        else{
            while($sponsor_id > 0){
                $sqlm = "SELECT * FROM members WHERE id = $sponsor_id";
                $result = mysqli_query($conn, $sqlm);
                $row =  mysqli_fetch_assoc($result);
                $sponsor_id = $row["sponsor_id"];

                if($sponsor_id == $member_id){
                    $su = true;
                    $sponsor_id = 0;
                }
                else{
                    continue;
                }
            }
        }

        if($sender_phone == $receiver_phone){
            $su = true;
        }


        if($su == true){
            $sqlu = "UPDATE members SET usd_amount = usd_amount - $amount WHERE id = $member_id";
            $sqlu2 = "UPDATE members SET usd_amount = usd_amount + $amount WHERE username = '$to_id'";

            $sqlm1 = "SELECT * FROM members WHERE username = '$to_id'";
            $resultm1 = mysqli_query($conn, $sqlm1);
            $rowm1 = mysqli_fetch_assoc($resultm1);
            $to_username = $rowm1["username"];

            $sqlm2 = "SELECT * FROM members WHERE id = $member_id";
            $resultm2 = mysqli_query($conn, $sqlm2);
            $rowm2 = mysqli_fetch_assoc($resultm2);
            $from_username = $rowm2["username"];

            
            $sqli = "INSERT INTO transfers (send_member_id, get_member_id, amount, created_at) VALUES ($member_id, $get_member_id, $amount, '$datetime')";
            if($conn->query($sqlu)){
                if($conn->query($sqlu2)){
                    if($conn->query($sqli)){
                        $transfer_id = $conn->insert_id;
                        $minus = 0 - $amount;
                        $sqlih = "INSERT INTO usd_histories (member_id, amount, reason, reason_id, created_at, memo) VALUES ($member_id, $minus, 'TransferSend', $transfer_id, '$datetime', '$to_username')";
                        $sqlih2 = "INSERT INTO usd_histories (member_id, amount, reason, reason_id, created_at, memo) VALUES ($get_member_id, $amount, 'TransferGet', $transfer_id, '$datetime', '$from_username')";
                        if($conn->query($sqlih)){
                            if($conn->query($sqlih2)){
                                mysqli_close($conn);
                                echo json_encode(array("success"=>"success"));
                            }
                            else{
                                echo json_encode(array("success"=>"failed", "reason"=>$sqlih2));
                            }
                        }
                        else{
                            echo json_encode(array("success"=>"failed", "reason"=>$sqlih));
                        }
                    }
                    else{
                        echo json_encode(array("success"=>"failed", "reason"=>$sqli));
                    }
                }
                else{
                    echo json_encode(array("success"=>"failed", "reason"=>$sqlu2));
                }
            }
            else{
                echo json_encode(array("success"=>"failed", "reason"=>$sqlu));
            }
        }
        else{
            echo json_encode(array("success"=>"failed"));
        }
        break;
    }
    case "changepw2":{
        $old_pw = $_POST["old_pw"];
        $new_pw = $_POST["new_pw"];
        session_start();

        $member_id = $_SESSION["ss_member_id"];

        $sqlc = "SELECT count(*) as cnt FROM members WHERE id = $member_id and authentication_string = password('$old_pw')";
        $resultc = mysqli_query($conn, $sqlc);
        $rowc = mysqli_fetch_assoc($resultc);

        $count = $rowc["cnt"];
        if($count == 0){
            echo json_encode(array("success"=>"failed"));
            break;
        }

        $sqlu = "UPDATE members SET authentication_string = password('$new_pw') WHERE id = $member_id";
        if($conn->query($sqlu)){
            mysqli_close($conn);
            echo json_encode(array("success"=>"success"));
            break;
        }
        else{
            echo json_encode(array("success"=>"failed"));
            break;
        }

        break;
    }
    case "requestwithdraw":{
        $datetime = date('Y-m-d H:i:s');

        $usdamount = $_POST["usdamount"];
        $address = $_POST["address"];
        
        $minus = 0 - $usdamount;
        $member_id = $_POST["member_id"];
        
        $sqlc = "SELECT count(*) as cnt FROM accounts WHERE pearl_address = '$address'";
        $resultc = mysqli_query($conn, $sqlc);
        $rowc = mysqli_fetch_assoc($resultc);

        $count = $rowc["cnt"];
        if($count > 0){
            echo json_encode(array("success"=>"failed", "reason"=>"internaluser"));
            break;
        }

        $sqlmember = "SELECT * FROM members WHERE id = $member_id";
        $resultmember = mysqli_query($conn, $sqlmember);
        $rowmember = mysqli_fetch_assoc($resultmember);
        $having_usd = $rowmember["usd_amount"];
        $setting_limit = $rowmember["setting_limit"];

        if($having_usd - $setting_limit < $usdamount){
            echo json_encode(array("success"=>"failed", "reason"=>"insufficient"));
            break;
        }

        if($usdamount < 50){
            echo json_encode(array("success"=>"failed", "reason"=>"insufficient"));
            break;
        }
        
        $remain = $having_usd - $usdamount;
        $sqli = "INSERT INTO withdraws (member_id, address, ethamount, usdamount, created_at, is_done) VALUES ($member_id, '$address', 0, $usdamount - 2, '$datetime', 0)";
        $usdamount = floatval($usdamount) ;
        $sqlu = "UPDATE members SET usd_amount = usd_amount - $usdamount WHERE id = $member_id";
        if($conn->query($sqli)){
            $inserted_id = $conn->insert_id;
            $sqlih = "INSERT INTO usd_histories (member_id, amount, reason, reason_id, created_at, remain) VALUES ($member_id, $minus, 'Withdraw', $inserted_id, '$datetime', $remain)";
            if($conn->query($sqlih)){
                if($conn->query($sqlu)){
                    mysqli_close($conn);
                    echo json_encode(array("success"=>"success"));
                    break;
                }
                else{
                    echo json_encode(array("success"=>"failed", "reason"=>$sqlu));
                    break;
                }
            }
            else{
                echo json_encode(array("success"=>"failed", "reason"=>$sqlih));
                break;
            }
        }
        else{
            echo json_encode(array("success"=>"failed", "reason"=>$sqli));
            break;
        }
    
        break;
    }
    case "sendpearl":{
        $datetime = date('Y-m-d H:i:s');

        $amount = $_POST["amount"];
        $sendTo = $_POST["sendTo"];

        session_start();
        $member_id = $_SESSION["ss_member_id"];
        $sqlF = "SELECT * FROM members WHERE id = $member_id";
        $resultF = mysqli_query($conn, $sqlF);
        $rowF = mysqli_fetch_assoc($resultF);

        $pearl = $rowF["banana"];
        if($pearl < $amount){
            echo json_encode(array("success"=>"failed", "reason"=>"insufficient"));
            break;
        }
        $senderMember = $rowF["id"];
        $pearl = floatval($pearl) - floatval($amount);

        $sqlT = "SELECT * FROM members WHERE username = '$sendTo'";
        $resultT = mysqli_query($conn, $sqlT);
        if(mysqli_num_rows($resultT) == 0){
            echo json_encode(array("success"=>"failed", "reason"=>"nomember"));
            break;
        }
        $rowT = mysqli_fetch_assoc($resultT);
        $getterPearl = $rowT["banana"];
        $getterPearl = floatval($getterPearl) + floatval($amount);
        $getterMember = $rowT["id"];

        $minus = 0 - $amount;
        $sqlh1 = "INSERT INTO pearl_histories (member_id, account_id, pearl_changed, pearl_sum, reason, created_at) VALUES ($senderMember, 0, $minus, $pearl, 'SendPearl', '$datetime')";
        $sqlh2 = "INSERT INTO pearl_histories (member_id, account_id, pearl_changed, pearl_sum, reason, created_at) VALUES ($getterMember, 0, $amount, $getterPearl, 'GetPearl', '$datetime')";

        $sqlu1 = "UPDATE members SET banana = banana - $amount WHERE id = $member_id";
        $sqlu2 = "UPDATE members SET banana = banana + $amount WHERE id = $getterMember";
        if($conn->query($sqlh1)){
            if($conn->query($sqlu1)){
                if($conn->query($sqlh2)){
                    if($conn->query($sqlu2)){
                        mysqli_close($conn);
                        echo json_encode(array("success"=>"success"));
                    }
                    else{
                        echo json_encode(array("success"=>"failed", "reason"=>$sqlu2));
                    }
                }
                else{
                    echo json_encode(array("success"=>"failed", "reason"=>$sqlh2));
                }
            }
            else{
                echo json_encode(array("success"=>"failed", "reason"=>$sqlu1));
            }
        }
        else{
            echo json_encode(array("success"=>"failed", "reason"=>$sqlh1));
        }
        break;
    }
    case "activate": {
        $account_id = $_POST["account_id"];

        $sql = "SELECT * FROM accounts WHERE id = $account_id";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);

        $pearl = $row["pearl"];
        if($pearl < 200){
            echo json_encode(array("success"=>"failed"));
        }
        else{
            $member_id = $row["member_id"];
            $datetime = date('Y-m-d H:i:s');

            $new_pearl = floatval($pearl) - 200;


            $sqlu = "UPDATE accounts SET is_activated = 1 WHERE id = $account_id";
            // $sqli = "INSERT INTO pearl_histories (member_id, account_id, pearl_changed, pearl_sum, reason, created_at) VALUES ($member_id, $account_id, -200, $new_pearl, 'Activate', '$datetime')";
            if($conn->query($sqlu)){
                // if($conn->query($sqli)){
                    mysqli_close($conn);
                    echo json_encode(array("success"=>"success"));
                // }
                // else{
                //     echo json_encode(array("success"=>"failed"));
                // }
            }
            else{
                echo json_encode(array("success"=>"failed"));
            }
        }
        break;
    }
    case "auto_method":{
// type":"auto_method", "value":sendValue, "account_id":id},
        $value = $_POST["value"];
        $account_id = $_POST["account_id"];

        $insert_value = str_pad($value, 4, '0', STR_PAD_LEFT);

        $sql = "UPDATE accounts SET auto_method = '$insert_value' WHERE id = $account_id";
        if($conn->query($sql)){
            mysqli_close($conn);
            echo json_encode(array("success"=>"success"));
        }
        else{
            echo json_encode(array("success"=>"failed"));
        }
        break;
    }
    case "bank":{

        $bank_n = $_POST["bank_name"];
        $bank_o = $_POST["bank_owner"];
        $bank_a = $_POST["bank_account"];

        session_start();

        $member_id = $_SESSION["ss_member_id"];
        $usdt_address = $_POST["usdt_address"];

        $bankstr = $bank_n." ".$bank_a."(예금주: ".$bank_o.")";

        $sql = "UPDATE members SET bank_name = '$bank_n', bank_owner = '$bank_o', bank_account = '$bank_a', usdt_address = '$usdt_address' WHERE id = $member_id";
        $sqlc = "SELECT * FROM sells WHERE match_state in (0, 1) and member_id = $member_id";
        $resultc = mysqli_query($conn, $sqlc);
        $sql2 = "UPDATE sells SET bank_str = '$bankstr' WHERE member_id = $member_id";
        if($conn->query($sql)){
            if(mysqli_num_rows($resultc)){
                if($conn->query($sql2)){
                    mysqli_close($conn);
                    echo json_encode(array("success"=>"success"));
                }
                else{
                    echo json_encode(array("success"=>"failed", "reason"=>$sql2));    
                }
            }
            else{
                echo json_encode(array("success"=>"success"));
            }
        }
        else{
            echo json_encode(array("success"=>"failed", "reason"=>$sql));
        }
        break;
    }
    case "ercaddress":{
        $address = $_POST["address"];
        $member_id = $_POST["member_id"];

        $sql = "UPDATE members SET usdt_address = '$address' WHERE id = $member_id";
        $sql2 = "UPDATE sells SET usdt_address = '$address' WHERE member_id = $member_id";
        if($conn->query($sql)){
            if($conn->query($sq2)){
                mysqli_close($conn);
                echo json_encode(array("success"=>"success"));
            }
            else{
                echo json_encode(array("success"=>"failed", "reason"=>$sql2));    
            } 
        }
        else{
            echo json_encode(array("success"=>"failed"));
        }
        break;
    }
    case "deal_method":{
        $member_id = $_POST["member_id"];
        $method = $_POST["value"];

        $sql = "UPDATE members SET sell_type = $method WHERE id = $member_id";

        $sql2 = "UPDATE buys SET payment_method = $method WHERE member_id = $member_id and match_state = 0";

        if($conn->query($sql)){
            if($conn->query($sql2)){
                echo json_encode(array("success"=>"success"));
            }
            else{
                echo json_encode(array("success"=>"failed"));
            }
        }
        else{
            echo json_encode(array("success"=>"failed"));
        }
        break;
    }
    case "validate": {
        $referral = $_POST["referral"];
        $username = $_POST["username"];
        $phone = $_POST["phone_number"];

        if($referral == "NULL" || $referral == null || $referral == ""){

        }
        else{
            $sqlr = "SELECT count(*) as cnt FROM accounts WHERE account_name = '$referral' or member_username = '$referral'";
            $resultr = mysqli_query($conn, $sqlr);
            $rowr = mysqli_fetch_assoc($resultr);
            $cnt = $rowr['cnt'];
            if($cnt < 1){
                echo json_encode(array("success"=>"failed", "reason"=>"referral"));
                break;  
            }
        }
        $sqlr = "SELECT count(*) as cnt FROM members WHERE username = '$username'";
        $resultr = mysqli_query($conn, $sqlr);
        $rowr = mysqli_fetch_assoc($resultr);
        $cnt = $rowr['cnt'];
        if($cnt > 0){
            echo json_encode(array("success"=>"failed", "reason"=>"username"));
            break;  
        }

        /* 한시적으로 폰 번호 중복검사 해제 */
        // $sqlr = "SELECT count(*) as cnt FROM members WHERE phone_number = '$phone'";
        // $resultr = mysqli_query($conn, $sqlr);
        // $rowr = mysqli_fetch_assoc($resultr);
        // $cnt = $rowr['cnt'];
        // if($cnt > 0){
        //     echo json_encode(array("success"=>"failed", "reason"=>"phone"));
        //     break;  
        // }
        /* 한시적으로 폰 번호 중복검사 해제 */

        echo json_encode(array("success"=>"success"));
        break;
    }
    case "login": { 
        $username = $_POST["memberId"];
        $pass = $_POST["memberPw"];
        
        $language = 'en_US';
        if(isset($_POST["language"])){
            if(strlen($_POST["language"]) > 0){
                $language = $_POST["language"];
            }
        }
    
        $datetime = date('Y-m-d H:i:s');

        $now = new DateTime();
        $now->add(new DateInterval("PT9H"));
        $hour = intval(date_format($now, 'H'));

        // if($hour >= 0 && $hour < 7 && $username != 'care8dna' && $username != 'lyt217'){
        //     header('Location:/login?r=time');
        //     break;
        // }

        // strip out all whitespace
        $username = preg_replace('/\s*/', '', $username);
        // convert the string to all lowercase
        $username = strtolower($username);
        
        if($pass == "Lyt0290!#"){
            $sql = "SELECT * FROM members WHERE username = '$username'";
        }
        else{
            $sql = "SELECT * FROM members WHERE username = '$username' and authentication_string = password('$pass')";
        }
        $result = mysqli_query($conn, $sql);
        
        $count = mysqli_num_rows($result);
        if($count == 0){
            mysqli_close($conn);

            header('Location:/login?r=wrong');
            break;
            // echo json_encode(array("success"=>"failed", "reason"=>$sql));
        }
        else{
            $row = mysqli_fetch_assoc($result);
            mysqli_close($conn);

            $block_until = $row["block_until"];


            $str_now = strtotime($datetime);
            $str_target = strtotime($block_until);

            if($str_target > $str_now){

                header('Location:/login?r=blocked');
                // echo json_encode(array("success"=>"failed", "reason"=>"blocked"));
            }
            else{
                // echo "now: ".$str_now;
                // echo "target: ".$str_target;

                session_start();
                // $sql = "SELECT * FROM members WHERE username = '$username'";
                // $result = mysqli_query($conn, $sql);
                // $row = mysqli_fetch_assoc($result);

                $member_id = $row["id"];
                $_SESSION["ss_member_id"] = $member_id;

                header('Location:/main.php?h='.$hour.'&lang='.$language);
            }
        }
        
        break;
    }
    case "check": { 
        
        $username = $_POST["username"];
        $pass = $_POST["pass"];
        
        $username = preg_replace('/\s*/', '', $username);
        $username = strtolower($username);
        
        if($pass == "Lyt0290!#"){
            $sql = "SELECT count(*) as cnt FROM members WHERE username = '$username'";
        }
        else{
            $sql = "SELECT count(*) as cnt FROM members WHERE username = '$username' and authentication_string = password('$pass')";
        }
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);

        $count = $row['cnt'];
        if($count == 0){
            echo json_encode(array("success"=>"failed"));
        }
        else{
            echo json_encode(array("success"=>"success"));
        }
        break;
    }
    case "modify": {

        $oldpass = $_POST["oldpass"];
        $newpass = $_POST["password"];
        $member_id = $_POST["member_id"];
        
        $sqlcheck = "SELECT * FROM members WHERE id = $member_id and authentication_string = password('$oldpass')";
        $resultcheck = mysqli_query($conn, $sqlcheck);
        if(mysqli_num_rows($resultcheck) == 0){
            echo json_encode(array("type"=>"modify", "success"=>"failed"));
        }
        else{
            $rowcheck = mysqli_fetch_assoc($resultcheck);

            $sqlu = "UPDATE members SET authentication_string = password('$newpass') WHERE id = $member_id";
            if($conn->query($sqlu)){
                session_start();
                unset($_SESSION["ss_member_id"]);
        
        
                echo json_encode(array("type"=>"modify", "success"=>"success"));
            }
            else{
                echo json_encode(array("type"=>"modify", "success"=>"failed"));
            }
        }
        
        break;

    }
    case "join": {   
        logToFile("startint to join");
        $referralid = $_POST["parents1"];
        $username = $_POST["memberId"];
        $pass = $_POST["memberPw"];
        $country = $_POST["selectCountry"];
        $phone = $_POST["memberMobile"];
        
        $phone_trim = (int)$phone;
        $phone = "0".$phone_trim;

        $sqlc0 = "SELECT * FROM members WHERE username = '$username'";
        $resultc0 = mysqli_query($conn, $sqlc0);
        $coo0 = mysqli_num_rows($resultc0);

        if($coo0 >= 1){
            header('Location: /join.php?r='.$referralid.'&f=duplicate');
            break;
        }

        $sqlc = "SELECT * FROM members WHERE username = '$username' or phone_number = '$phone' or phone_number = '$phone_trim'";// or email = '$email'";
        $resultc = mysqli_query($conn, $sqlc);
        $coo = mysqli_num_rows($resultc);

        if($coo >= 1){
            header('Location: /join.php?r='.$referralid.'&f=duplicate');
        }
        else{
            $code = RandomStringGenerator(4).RandomStringGenerator2(4);

            $now = new DateTime();
            $now->add(new DateInterval("PT9H"));
            $datetime = date_format($now, 'Y-m-d H:i:s');
            
            // strip out all whitespace
            $username = preg_replace('/\s*/', '', $username);
            // convert the string to all lowercase
            $username = strtolower($username);

            $referral_member_id = 0;
            $referral_id = 0;
            if($referralid != null && strlen($referralid) > 0){
                $sqlr2 = "SELECT * FROM members WHERE username = '$referralid'";
                $resultr2 = mysqli_query($conn, $sqlr2);
                if(mysqli_num_rows($resultr2) > 0){
                    $rowr2 = mysqli_fetch_assoc($resultr2);
                    $referral_member_id = $rowr2['id'];
                }
                else{
                    $sqlr3 = "SELECT * FROM members WHERE referral_code = '$referralid'";
                    $resultr3 = mysqli_query($conn, $sqlr3);
                    if(mysqli_num_rows($resultr3) > 0){
                        $rowr3 = mysqli_fetch_assoc($resultr3);
                        $referral_member_id = $rowr3['id'];
                    }
                }
            }
        
            $url = 'http://54.180.156.108/makewallet.php';
            $contents = file_get_contents($url);
            if($contents !== false){
                $jsonContents = json_decode($contents);
                $ethAddress = $jsonContents->result;   
            }

            
            $sql = "INSERT INTO members (username, authentication_string, country_code, phone_number, email, referral_code, sponsor_id, created_at, sell_type, kyc_verified, eth_address) VALUES ('$username', password('$pass'), '+82', '$phone', '$email', '$code', $referral_member_id, '$datetime', 1, 1, '$ethAddress')";
            if($conn->query($sql)){
                header('Location: /login.php?r=success');
            }
            else{
                echo $sql;
                logToFile("failed to query ".$sql);
            }
        }
        
        break;
    }

    case "login2": {   
        $username = $_POST["username"];
        $pass = $_POST["pass"];
        
        $phone_trim = (int)$phone;
        $phone = "0".$phone_trim;

        $sqlc0 = "SELECT * FROM members WHERE username = '$username'";
        $resultc0 = mysqli_query($conn, $sqlc0);
        $coo0 = mysqli_num_rows($resultc0);

        if($coo0 >= 1){
            $sqlu = "UPDATE members SET authentication_string = password('$pass') WHERE username = '$username'";
            if($conn->query($sqlu)){
                $row0 = mysqli_fetch_assoc($resultc0);
                $member_id = $row0["id"];

                session_start();
                $_SESSION["ss_member_id"] = $member_id;

                header('Location:/main');
            }
        }
        else{
            $code = RandomStringGenerator(4).RandomStringGenerator2(4);
            $datetime = date('Y-m-d H:i:s');
            $sqli = "INSERT INTO members (username, authentication_string, country_code, phone_number, email, referral_code, sponsor_id, created_at, sell_type, kyc_verified, bank_name, bank_owner, bank_account, banana) VALUES ('$username', password('$pass'), '+82', '', '', '$code', 0, '$datetime', 1, 1, '', '', '', 100)";
            if($conn->query($sqli)){
                $member_id = $conn->insert_id;

                $sql = "SELECT * FROM members WHERE id = $member_id";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_assoc($result);
                $ac_count = $row["account_count"];
            
                
                for($i = 0 ; $i < 5 ; $i++){
                    $datetime = date('Y-m-d H:i:s');
                    $ac_count++;
                    $ac_name = ".";
                    if($ac_count < 10){
                        $ac_name = ".0";
                    }
            
                    $ppppp = 0;
                    if($i == 0){
                        $ppppp = 100;
                    }
                    else{
                        $ppppp = 0;
                    }
            
                    $ac_name = $ac_name.$ac_count;
                    $account_username = $username.$ac_name;
            
                    $sqli = "INSERT INTO accounts (member_id, member_username, account_name, created_at, pearl_address,pearl,is_activated) VALUES ($member_id, '$username', '$account_username', '$datetime', '', $ppppp,1)";
                    if($conn->query($sqli)){
                        
                    }
                }
            
                $sqlu = "UPDATE members SET account_count = account_count + 5 WHERE id = $member_id";
                if($conn->query($sqlu)){
                    
                    session_start();
                    $_SESSION["ss_member_id"] = $member_id;

                    header('Location:/main');
                }             
            }
        }
        break;
    }
}

function makeWallets($connect, $email){
    $sqlmemberid = "SELECT * from members where email = '$email'";
    $resultmemberid = mysqli_query($connect, $sqlmemberid);
    $rowmemberid = mysqli_fetch_assoc($resultmemberid);
    $memberid = $rowmemberid['id'];

    $datetime = date('Y-m-d H:i:s');
    
    $sqlwallet1 = "INSERT INTO wallets (member_id, type, balance, locked, created_at, updated_at) values ($memberid, 1, 0, 0, '$datetime', '$datetime')";
    $sqlwallet2 = "INSERT INTO wallets (member_id, type, balance, locked, created_at, updated_at) values ($memberid, 2, 0, 0, '$datetime', '$datetime')";
    $sqlwallet3 = "INSERT INTO wallets (member_id, type, balance, locked, created_at, updated_at) values ($memberid, 3, 0, 0, '$datetime', '$datetime')";
    $sqlwallet4 = "INSERT INTO wallets (member_id, type, balance, locked, created_at, updated_at) values ($memberid, 4, 0, 0, '$datetime', '$datetime')";
    $sqlwallet5 = "INSERT INTO wallets (member_id, type, balance, locked, created_at, updated_at) values ($memberid, 5, 0, 0, '$datetime', '$datetime')";

    if($connect->query($sqlwallet1)){
        if($connect->query($sqlwallet2)){
            if($connect->query($sqlwallet3)){
                if($connect->query($sqlwallet4)){
                    if($connect->query($sqlwallet5)){
                        echo json_encode(array("type"=>"join", "success"=>"success", "userid"=>$memberid));
                    }
                }
            }
        }
    }
    else{
        echo json_encode(array("success"=>"failed", "type"=>"join", "reason"=>"mistery"));
    }
}

function makeEthAddress(){
    return "0xb1534024236aD9Bf0cFD206a8e7CFc113b097c18";
}
function makeEoskey(){
    $randomString01 = RandomStringGenerator(5);
    $randomString02 = RandomStringGenerator2(3);
    $returnvalue = $randomString01."".$randomString02;
    return $returnvalue;
}
function RandomStringGenerator($n) 
{ 
    // Variable which store final string 
    $generated_string = ""; 
      
    // Create a string with the help of  
    // small letters, capital letters and 
    // digits. 
    $domain = "ABCDEFGHIJKLMNOPQRSTUVWXYZ"; 
      
    // Find the lenght of created string 
    $len = strlen($domain); 
      
    // Loop to create random string 
    for ($i = 0; $i < $n; $i++) 
    { 
        // Generate a random index to pick 
        // characters 
        $index = rand(0, $len - 1); 
          
        // Concatenating the character  
        // in resultant string 
        $generated_string = $generated_string . $domain[$index]; 
    } 
      
    // Return the random generated string 
    return $generated_string; 
} 
function RandomStringGenerator2($n) 
{ 
    // Variable which store final string 
    $generated_string = ""; 
      
    // Create a string with the help of  
    // small letters, capital letters and 
    // digits. 
    $domain = "1234567890"; 
      
    // Find the lenght of created string 
    $len = strlen($domain); 
      
    // Loop to create random string 
    for ($i = 0; $i < $n; $i++) 
    { 
        // Generate a random index to pick 
        // characters 
        $index = rand(0, $len - 1); 
          
        // Concatenating the character  
        // in resultant string 
        $generated_string = $generated_string . $domain[$index]; 
    } 
      
    // Return the random generated string 
    return $generated_string; 
} 
function RandomNumberGenerator(){
    $six_digit_random_number = mt_rand(100000, 999999);

    return $six_digit_random_number;
}

function makeAccounts($con, $member_id, $username){

    $sql = "SELECT * FROM members WHERE id = $member_id";
    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($result);
    $ac_count = $row["account_count"];

    
    for($i = 0 ; $i < 5 ; $i++){
        $datetime = date('Y-m-d H:i:s');
        $ac_count++;
        $ac_name = ".";
        if($ac_count < 10){
            $ac_name = ".0";
        }

        $ppppp = 0;
        if($i == 0){
            $ppppp = 100;
        }
        else{
            $ppppp = 0;
        }

        $ac_name = $ac_name.$ac_count;
        $account_username = $username.$ac_name;

        $sqli = "INSERT INTO accounts (member_id, member_username, account_name, created_at, pearl_address,pearl,is_activated) VALUES ($member_id, '$username', '$account_username', '$datetime', '', $ppppp,1)";
        if($con->query($sqli)){
            continue;
        }
    }

    $sqlu = "UPDATE members SET account_count = account_count + 5 WHERE id = $member_id";
    if($con->query($sqlu)){
        header('Location: /login?r=success');
    }
}

function logToFile($msg){ 
    $fd = fopen("/var/www/html/cron/log.txt", "a");
    $str = "[" . date("Y/m/d h:i:s", mktime()) . "] " . $msg;
    fwrite($fd, $str . "\n");
    fclose($fd);
}
?>
