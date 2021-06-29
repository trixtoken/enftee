<?php

include_once('_common.php');

$type = $_POST["type"];
if($type == "match"){
    $buy_id = $_POST["buy_id"];
    $item_id = $_POST["item_id"];

    $sqlb = "SELECT * FROM buys WHERE id = $buy_id and match_state = 0";
    $resultb = mysqli_query($conn, $sqlb);
    if(mysqli_num_rows($resultb) == 0){
        echo json_encode(array("success"=>"failed", "reason"=>"notbuyingnow"));
        return;
    }
    $rowb = mysqli_fetch_assoc($resultb);
    
    $member_id = $rowb["member_id"];
    $account_id = $rowb["account_id"];
    $item_type = $rowb["item_type"];
    $percentage = 12 + (4 * (int)($item_type));
    $moving_period = 3;
    $price = 0;

    $fee = 40 * (int)($item_type);

    if($item_type == 1){
        $price = 100;
    }
    else if($item_type == 2){
        $price = 200;
    }
    else if($item_type == 3){
        $price = 300;
    }
    $price_in_piat = (int)($price) * 1200;
    $datetime = date('Y-m-d H:i:s');
    

    $sqla = "SELECT * FROM accounts WHERE id = $account_id";

    $sqlmem = "UPDATE members SET previous_matches = previous_matches + 1 WHERE id = $member_id";
    if($conn->query($sqlmem)){
        if($item_id  == null || $item_id == 0){
            //아이템 만들어야함 (회사 아이템)


            $interval = 'P'.$moving_period.'D';
            $now = new DateTime(); //current date/time
            $now->add(new DateInterval("PT9H"));
            $now->add(new DateInterval($interval));
            
            $move_at = date_format($now, 'Y-m-d H:i:s');
            $dealdate = date_format($now, 'Y-m-d');
        
            
            $sqli  = "INSERT INTO items (type, owner_account_id, owner_member_id, price, created_at, last_moved_at, moving_period, percentage, next_move_at, next_deal_date) VALUES ($item_type, 0, 0, $price, '$datetime', '$datetime', $moving_period, $percentage, '$move_at', '$dealdate')";
            if($conn->query($sqli)){
                $item_id = $conn->insert_id;

                $url = 'http://3.34.125.66/makewallet.php';
                $contents = file_get_contents($url);
                if($contents !== false){
                    $jsonContents = json_decode($contents);
                    $ethAddress = $jsonContents->result;   
                }

                $now2 = new DateTime(); //current date/time
                $now2->add(new DateInterval("PT9H"));
                $selldate = date_format($now2, 'Y-m-d');
        
                $sqlis = "INSERT INTO sells (member_id, account_id, item_id, item_type, created_at, sell_date, match_state, buy_account_id, bank_str, usdt_address, payment_method) VALUES (0, 0, $item_id, $item_type, '$datetime', '$selldate', 1, $account_id, '', '$ethAddress', 3)";
                if($conn->query($sqlis)){
                    $sell_id = $conn->insert_id;

                    $sqlim = "INSERT INTO matchings (buy_account_id, buy_member_id, buy_id, buy_date, sell_account_id, sell_member_id, sell_id, price, item_type, matched_at, match_state, is_auto, price_in_piat, pay_type) VALUES ($account_id, $member_id, $buy_id, '$selldate', 0, 0, $sell_id, $price, $item_type, '$datetime', 1, 0, $price_in_piat, 3)";
                    if($conn->query($sqlim)){
                        $matching_id = $conn->insert_id;
                        $sqlu = "UPDATE buys SET match_state = 1 WHERE id = $buy_id";
                        if($conn->query($sqlu)){

                            if(rollup($conn, $member_id, $account_id, $matching_id, $fee)){
                                echo json_encode(array("success"=>"success"));
                            }
                            else{
                                echo json_encode(array("success"=>"success"));
                            }
                        }
                    }
                }
            }
        }
        else{
            //아이템과 매칭
            $datetime = date('Y-m-d H:i:s');
            $now = new DateTime(); //current date/time
            $buy_date = date_format($now, 'Y-m-d');

            //ITEMS 에서 정보추출//
            $sqli = "SELECT * FROM items WHERE id = $item_id";
            $resulti = mysqli_query($conn, $sqli);
            $rowi = mysqli_fetch_assoc($resulti);
            $price = $rowi["price"];
            $price_in_piat = floatval($price) * 1200;
            //ITEMS 에서 정보추출//

            //SELL이 있으면 정보추출 & 없으면 새로 생성//
            $sqls = "SELECT * FROM sells WHERE item_id = $item_id and match_state = 0";
            $results = mysqli_query($conn, $sqls);
            if(mysqli_num_rows($results) == 0){
                echo json_encode(array("success"=>"failed", "reason"=>"notsellingitem"));
                return;
            }
            $rows = mysqli_fetch_assoc($results);

            $iitem_type = $rows["item_type"];
            $sell_id = $rows["id"];
            $pay_type = $rows["payment_method"];
            $sell_account_id = $rows["account_id"];
            $sell_member_id = $rows["member_id"];
            //SELL이 있으면 정보추출 & 없으면 새로 생성//

            if($iitem_type != $item_type){
                echo json_encode(array("success"=>"failed", "reason"=>"typeunmatch"));
                return;
            }
            if($sell_member_id == $member_id){
                echo json_encode(array("success"=>"failed", "reason"=>"sameuser"));
                return;
            }
            $fee = floatval($item_type) * 40;

            $sqlm = "INSERT INTO matchings (buy_account_id, buy_member_id, buy_id, buy_date, sell_account_id, sell_member_id, sell_id, price, item_type, matched_at, match_state, is_auto, price_in_piat, pay_type) VALUES ($account_id, $member_id, $buy_id, '$buy_date', $sell_account_id, $sell_member_id, $sell_id, $price, $item_type, '$datetime', 1, 0, $price_in_piat, $pay_type)";
            $sqlu1 = "UPDATE buys SET match_state = 1 WHERE id = $buy_id";
            $sqlu2 = "UPDATE sells SET match_state = 1, buy_account_id = $account_id WHERE id = $sell_id";
            if($conn->query($sqlu1)){
                if($conn->query($sqlu2)){
                    if($conn->query($sqlm)){
                        $matching_id = $conn->insert_id;
                        // if(rollup($conn, $member_id, $account_id, $matching_id, $fee)){
                            echo json_encode(array("success"=>"success"));
                        // }
                        // else{
                        //     echo json_encode(array("success"=>"success"));
                        // }            
                    }
                    else{
                        echo json_encode(array("success"=>"failed", "reason"=>$sqlm));
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
    }
}
else if($type == "unmatch"){
    $datetime = date('Y-m-d H:i:s');

    $buy_id = $_POST["buy_id"];
    $sql = "SELECT * FROM buys WHERE id = $buy_id";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    $match_state = $row["match_state"];
    if($match_state != 0){
        echo json_encode(array("success"=>"failed", "reason"=>"notwaitingbuy"));
        return;
    }
    $account_id = $row["account_id"];
    $item_type = $row["item_type"];

    $fee = 40 * (int)($item_type);

    $sqla = "SELECT * FROM accounts WHERE id = $account_id";
    $resulta = mysqli_query($conn, $sqla);
    $rowa = mysqli_fetch_assoc($resulta);

    $member_id = $rowa["member_id"];
    $pearl = $rowa["pearl"];
    $newpearl = $pearl + $fee;

    $sqlu = "UPDATE accounts SET pearl = pearl + $fee, transferable_pearl = transferable_pearl + $fee WHERE id = $account_id";
    $sqlu2 = "UPDATE buys SET match_state = 100 WHERE id = $buy_id";
    $sqli = "INSERT INTO pearl_histories (member_id, account_id, pearl_changed, pearl_sum, reason, created_at) VALUES ($member_id, $account_id, $fee, $newpearl, 'BuyUnmatch', '$datetime')";

    if($conn->query($sqlu)){
        if($conn->query($sqlu2)){
            if($conn->query($sqli)){
                echo json_encode(array("success"=>"success"));
            }
            else{
                echo json_encode(array("success"=>"success", "error"=>$sqli));
            }
        }
        else{
            echo json_encode(array("success"=>"success", "error"=>$sqlu2));
        }
    }
    else{
        echo json_encode(array("success"=>"success", "error"=>$sqlu));
    }
    

}

function rollup($con, $member_id, $account_id, $matching_id, $fee){

    // echo "mem : ".$member_id." / acc : ".$account_id." / mat : ".$matching_id." / fee : ".$fee;
    $sql = "SELECT * FROM members WHERE id = $member_id";
    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($result);
    $upper_member_id = $row["sponsor_id"];

    $level = 1;
    while($upper_member_id > 0){
        $datetime = date('Y-m-d H:i:s');
        $sqlu = "SELECT * FROM accounts WHERE member_id = $upper_member_id order by id asc limit 1";
        $resultu = mysqli_query($con, $sqlu);
        $rowu = mysqli_fetch_assoc($resultu);

        $u_account_id = $rowu["id"];

        $amount = 0;
        $percentage = 1;
        $point = $rowu["point"];
        if($level == 1){
            $percentage = 6;
        }
        else if($level == 2){
            $percentage = 4;
        }
        else{
            $percentage = 1;
        }
        $amount = floatval($fee) * $percentage / 100;
        $new_point = floatval($point) + floatval($amount);

        $sqlupdate = "UPDATE accounts SET point = point + $amount WHERE id = $u_account_id";
        $sqlb = "INSERT INTO benefits (member_id, account_id, buy_member_id, buy_account_id, matching_id, amount, created_at) VALUES ($upper_member_id, $u_account_id, $member_id, $account_id, $matching_id, $amount, '$datetime')";

        if($con->query($sqlupdate)){
            if($con->query($sqlb)){
                $sqln = "SELECT * FROM members WHERE id = $upper_member_id";
                $resultn = mysqli_query($con, $sqln);
                $rown = mysqli_fetch_assoc($resultn);
                $upper_member_id = $rown["sponsor_id"];
                $level++;

                if($level == 24){
                    $upper_member_id = 0;
                }
            }
        }
    }


    return true;
}
?>