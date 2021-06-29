<?php
include_once('_common.php');


$request = $_POST['type'];

switch($request){
    case "withdraw":{
        $now = new DateTime();
        $now->add(new DateInterval("PT9H"));
        $datetime = date_format($now, 'Y-m-d H:i:s');

        $coin = $_POST["coin"];

        session_start();
        $member_id = $_SESSION["ss_member_id"];
        $address = $_POST["walletAddress"];
        $amount = $_POST["amount"];

        if($coin == 'MDL'){
            $sqlu = "UPDATE members SET frim = frim - $amount, frim_sent = frim_sent + $amount WHERE id = $member_id";
        }
        else if($coin == 'USDT'){
            $sqlu = "UPDATE members SET usdt = usdt - $amount WHERE id = $member_id";
        }
        else if($coin == 'TDOS'){
            $sqlu = "UPDATE members SET tdos = tdos - $amount WHERE id = $member_id";
        }
        else if($coin == 'ETH'){
            $sqlu = "UPDATE members SET eth = eth - $amount WHERE id = $member_id";
        }
        $sqli = "INSERT INTO withdraws (member_id, coin, amount, created_at, address, state) VALUE ($member_id, '$coin', $amount, '$datetime', '$address', 1)";
        if($conn->query($sqli)){
            if($conn->query($sqlu)){
                header('Location:/history.php?coin='.$coin.'&r=success');
            }
            else{
                header('Location:/transaction.php?coin='.$coin.'&r=failed');
            }
        }
        else{
            header('Location:/transaction.php?coin='.$coin.'&r=failed');
        }

    }
    case "iotc_success":{
        $member_id = $_POST["member_id"];
        $amount = $_POST["amount"];
        $refund_amount = $amount / 2;
            
        $sqlupdate = "UPDATE gaia_withdrawings SET shopping_amount = 0 WHERE member_id = $member_id";

        $sqlwallet = "SELECT * FROM wallets WHERE member_id = $member_id and type = 2";
        $resultwallet = mysqli_query($conn, $sqlwallet);
        $rowwallet = mysqli_fetch_assoc($resultwallet);
        $wallet_id = $rowwallet['id'];

        $newsum = floatval($rowwallet['balance']) + floatval($rowwallet['locked']) + $refund_amount;

        if($conn->query($sqlupdate)){

            $datetime = date('Y-m-d H:i:s');
            $sqlinsert = "INSERT INTO deposits (member_id, currency, amount, txid, created_at, aasm_state) values ($member_id, 'GAIA', $refund_amount, 'REFUND', '$datetime', 'accepted')";
            if($conn->query($sqlinsert)){
                $deposit_id = $conn->insert_id;
                $sqlaccountversion = "INSERT INTO account_versions(member_id, wallet_id, modifiable_id, modifiable_type, balance, locked, sum, available, created_at) VALUES ($member_id, $wallet_id, 0, 'Refund', $refund_amount, 0, $newsum, 0, '$datetime')";
                if($conn->query($sqlaccountversion)){
                    $sqlupdate2 = "UPDATE wallets SET balance = balance + $refund_amount WHERE id = $wallet_id";
                    if($conn->query($sqlupdate2)){
                        $sqlwallet = "SELECT * FROM wallets WHERE member_id = $member_id and type = 2";
                        $resultwallet = mysqli_query($conn, $sqlwallet);
                        $rowwallet = mysqli_fetch_assoc($resultwallet);
                        

                        $sqlselect = "SELECT * FROM gaia_withdrawings WHERE member_id = $member_id";
                        $result = mysqli_query($conn, $sqlselect);
                        $row = mysqli_fetch_assoc($result);
                        echo json_encode(array("success"=>"success", "type"=>"iotc_success", "withdrawing"=>$row, "stackwallet"=>$rowwallet));

                    }
                }
            }
            
        }
        break;
    }
    case "requestwithdraw":{
        $datetime = date('Y-m-d H:i:s');
        $member_id = $_POST["member_id"];
        $usdamount = $_POST["usdamount"];
        $ethamount = $_POST["ethamount"];
        $address = $_POST["address"];
        $minus = 0 - $usdamount;

        $sqlc = "SELECT count(*) as cnt FROM accounts WHERE pearl_address = '$address'";
        $resultc = mysqli_query($conn, $sqlc);
        $rowc = mysqli_fetch_assoc($resultc);

        $count = $rowc["cnt"];
        if($count > 0){
            echo json_encode(array("success"=>"failed", "reason"=>"internaluser"));
            break;
        }
        $remain = $having_usd - $usdamount;
        
        $sqli = "INSERT INTO withdraws (member_id, address, ethamount, usdamount, created_at, is_done) VALUES ($member_id, $ethamount, $usdamount, '$datetime', 0)";
        $sqlu = "UPDATE members SET usd_amount = usd_amount - $usdamount WHERE id = $member_id";
        if($conn->query($sqli)){
            $inserted_id = $conn->insert_id;
            $sqlih = "INSERT INTO usd_histories (member_id, amount, reason, reason_id, created_at, remain) VALUES ($member_id, $minus, 'Withdraw', $inserted_id, '$datetime', $remain)";
            if($conn->query($sqlih)){
                if($conn->query($sqlu)){
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
    case "withdrawing":{
        $member_id = $_POST["member_id"];
        $amount = $_POST["amount"];
        $amount_p2p = $amount * 0.5;
        $amount_shop = $amount * 0.4;
        $amount_game = $amount * 0.1;
        
        $sqlcheck = "SELECT * FROM wallets WHERE member_id = $member_id and type = 1";
        $result = mysqli_query($conn, $sqlcheck);
        $row = mysqli_fetch_assoc($result);
        $balance = $row['balance'];
        $wallet_id = $row['id'];

        $newsum = $row['balance'] + $row['locked'] - $amount;
        $datetime = date('Y-m-d H:i:s');

        $minus = 0 - $amount;
        if($balance < $amount){
            echo json_encode(array("success"=>"failed", "reason"=>"exceed"));
            break;
        }
        else{
            $sqlminus = "UPDATE wallets SET balance = balance - $amount WHERE member_id = $member_id and type = 1";
            $sqlav = "INSERT INTO account_versions(member_id, wallet_id, modifiable_id, modifiable_type, balance, locked, sum, available, created_at) VALUES ($member_id, $wallet_id, 0, 'Withdrawing', $minus, 0, $newsum, 0, '$datetime')";
            if($conn->query($sqlav)){
                if($conn->query($sqlminus)){
                    $sqlwithdrawing = "SELECT count(*) as cnt FROM gaia_withdrawings WHERE member_id = $member_id";
                    $resultw = mysqli_query($conn, $sqlwithdrawing);
                    $roww = mysqli_fetch_assoc($resultw);

                    if($roww['cnt'] == 0){
                        $sqlinsert = "INSERT INTO gaia_withdrawings (member_id, cumulated_amount, p2p_amount, shopping_amount, game_amount) VALUES ($member_id, $amount, $amount_p2p, $amount_shop, $amount_game)";
                        if($conn->query($sqlinsert)){
                            $insert_id = $conn->insert_id;

                            $sqlselect = "SELECT * FROM gaia_withdrawings WHERE id = $insert_id";
                            $results = mysqli_query($conn, $sqlselect);
                            $rows = mysqli_fetch_assoc($results);

                            $sqlwa = "SELECT * FROM wallets WHERE id = $wallet_id";
                            $resultwa = mysqli_query($conn, $sqlwa);
                            $rowwa = mysqli_fetch_assoc($resultwa);

                            echo json_encode(array("success"=>"success", "type"=>"withdrawing", "result"=>$rows, "wallet1"=>$rowwa));

                            break;  
                        }
                    }
                    else{
                        $sqlupdate = "UPDATE gaia_withdrawings SET cumulated_amount = cumulated_amount + $amount, p2p_amount = p2p_amount + $amount_p2p, shopping_amount = shopping_amount + $amount_shop, game_amount = game_amount + $amount_game WHERE member_id = $member_id";
                        if($conn->query($sqlupdate)){

                            $sqlselect = "SELECT * FROM gaia_withdrawings WHERE member_id = $member_id";
                            $results = mysqli_query($conn, $sqlselect);
                            $rows = mysqli_fetch_assoc($results);

                            $sqlwa = "SELECT * FROM wallets WHERE id = $wallet_id";
                            $resultwa = mysqli_query($conn, $sqlwa);
                            $rowwa = mysqli_fetch_assoc($resultwa);

                            echo json_encode(array("success"=>"success", "type"=>"withdrawing", "result"=>$rows, "wallet1"=>$rowwa));

                            break;  
                        }
                    }
                }
            }
        }
        break;
    }
    case "inquiryexchange": {
        $member_id = $_POST["member_id"];
        $address = $_POST["address"];
        
        $sqlamount = "SELECT * FROM gaia_withdrawings WHERE member_id = $member_id";
        $resultamount = mysqli_query($conn, $sqlamount);
        $rowamount = mysqli_fetch_assoc($resultamount);
        $p2pamount = $rowamount["p2p_amount"];
        $datetime = date('Y-m-d H:i:s');

        $sqlwithdraw = "INSERT INTO withdraws (member_id, amount, created_at, aasm_state, fund_uid, currency, fee, total) VALUES ($member_id, $p2pamount, '$datetime', 'inquiry', '$address', 'GAIA', 0, $p2pamount)";
        $sqlupdate = "UPDATE gaia_withdrawings SET p2p_amount = 0 WHERE member_id = $member_id";

        if($conn->query($sqlwithdraw)){
            if($conn->query($sqlupdate)){

                $sqlwithdrawing = "SELECT * FROM gaia_withdrawings WHERE member_id = $member_id";
                $result = mysqli_query($conn, $sqlwithdrawing);
                $row = mysqli_fetch_assoc($result);
                
                echo json_encode(array("success"=>"success", "type"=>"inquiryexchange", "withdrawing"=>$row));
            }
        }
        else{
            echo json_encode(array("success"=>"failed", "query"=>$sqlwithdraw));
        }
        break;
    }
    case "inquiryp2p": {

        $member_id = $_POST["member_id"];
        $address = $_POST["address"];
        $amount = $_POST["amount"];
        $amount08 = $amount * 0.2;
        $amount02 = $amount * 0.8;
        /* 이메일 주소로 회원 찾기 */
        $sqlcount = "SELECT * FROM members WHERE email = '$address'";
        $resultmember = mysqli_query($conn, $sqlcount);
        if(mysqli_num_rows($resultmember) == 0){
            echo json_encode(array("success"=>"failed", "type"=>"inquiryp2p", "reason"=>"nomember"));
            break;
        }
        $rowmember = mysqli_fetch_assoc($resultmember);
        $rowaddress = $rowmember['eth_address'];
        $rowid = $rowmember['id'];
        


        $datetime = date('Y-m-d H:i:s');

        $sqlw = "SELECT * FROM wallets WHERE member_id = $member_id and type = 1";
        $resultw = mysqli_query($conn, $sqlw);
        $roww = mysqli_fetch_assoc($resultw);
        $wallet_id = $roww['id'];
        $sum = $roww['balance'] + $roww['locked'];

        /* 회원아이디로 지갑 찾기 (FREE & STACK)*/
        $sqlwalletfree = "SELECT * FROM wallets WHERE member_id = $rowid and type = 1";
        $sqlwalletstack = "SELECT * FROM wallets WHERE member_id = $rowid and type = 2";
        $resultfree = mysqli_query($conn, $sqlwalletfree);
        $resultstack = mysqli_query($conn, $sqlwalletstack);
        $rowfree = mysqli_fetch_assoc($resultfree);
        $rowstack = mysqli_fetch_assoc($resultstack);
        $newsumfree = $rowfree['balance'] + $rowfree['locked'] + $amount02;
        $newsumstack = $rowstack['balance'] + $rowstack['locked'] + $amount08;
        $idfree = $rowfree['id'];
        $idstack = $rowstack['id'];

        /* update 및 insert sqlquery */

        $sqlwithdraw = "INSERT INTO withdraws (member_id, amount, created_at, done_at, aasm_state, fund_uid, fund_extra, currency, fee, total) VALUES ($member_id, $amount, '$datetime', '$datetime', 'done', '$rowaddress', '$address', 'GAIA', 0, $amount)";
        $sqlupdate = "UPDATE gaia_withdrawings SET p2p_amount = p2p_amount - $amount WHERE member_id = $member_id";

        $sqldeposit = "INSERT INTO deposits (member_id, currency, amount, txid, created_at, aasm_state, address, done_at, is_auto) VALUES ($rowid, 'GAIA', $amount, 'TRANSFER', '$datetime', 'accepted', '$rowaddress', '$datetime', 1)";
        
        $sqlupdate01 = "UPDATE wallets SET balance = balance + $amount02 WHERE member_id = $rowid and type = 1";
        $sqlupdate02 = "UPDATE wallets SET balance = balance + $amount08 WHERE member_id = $rowid and type = 2";
        if($conn->query($sqlwithdraw)){
            $withdraw_id = $conn->insert_id;
            $sqlav1 = "INSERT INTO account_versions (member_id, wallet_id, modifiable_id, modifiable_type, balance, locked, sum, available, created_at) VALUES ($member_id, $wallet_id, $withdraw_id, 'P2PWithdraw', 0, 0, $sum, 0, '$datetime')";
            if($conn->query($sqlav1)){

    
                if($conn->query($sqlupdate)){
                    if($conn->query($sqldeposit)){
                        $deposit_id = $conn->insert_id;
                        $sqlavfree = "INSERT INTO account_versions (member_id, wallet_id, modifiable_id, modifiable_type, balance, locked, sum, available, created_at) VALUES ($rowid, $idfree, $deposit_id, 'P2PDeposit', $amount02, 0, $newsumfree, 0, '$datetime')";
                        $sqlavstack = "INSERT INTO account_versions (member_id, wallet_id, modifiable_id, modifiable_type, balance, locked, sum, available, created_at) VALUES ($rowid, $idstack, $deposit_id, 'P2PDeposit', $amount08, 0, $newsumstack, 0, '$datetime')";

                        if($conn->query($sqlupdate01)){
                            if($conn->query($sqlupdate02)){
                                if($conn->query($sqlavfree)){
                                    if($conn->query($sqlavstack)){

                                        $sqlwithdrawing = "SELECT * FROM gaia_withdrawings WHERE member_id = $member_id";
                                        $result = mysqli_query($conn, $sqlwithdrawing);
                                        $row = mysqli_fetch_assoc($result);
                                        
                                        echo json_encode(array("success"=>"success", "type"=>"inquiryp2p", "withdrawing"=>$row));
                                    }
                                    else{
                                        echo json_encode(array("success"=>"failed", "query"=>$sqlavstack));
                                    }
                                }
                                else{
                                    echo json_encode(array("success"=>"failed", "query"=>$sqlavfree));
                                }
                            }
                            else{
                                echo json_encode(array("success"=>"failed", "query"=>$sqlupdate02));
                            }
                        }
                        else{
                            echo json_encode(array("success"=>"failed", "query"=>$sqlupdate01));
                        }
                    }
                    else{
                        echo json_encode(array("success"=>"failed", "query"=>$sqldeposit));
                    }
                }
                else{
                    echo json_encode(array("success"=>"failed", "query"=>$sqlupdate));
                }
            }
            else{
                echo json_encode(array("success"=>"failed", "query"=>$sqlav1));
            }
        }
        else{
            echo json_encode(array("success"=>"failed", "query"=>$sqlwithdraw));
        }

        break;
    }
    case "inquirygame":{
        $member_id = $_POST["memberid"];
        $currency = "GAIA";
        $amount = $_POST["amount"];
        $fee = $amount * 0.05;
        $total = $amount;
        $amount = $amount - $fee;
        $address = $_POST["address"];
        
        $datetime = date('Y-m-d H:i:s');

        $sqlinquiry = "INSERT INTO withdraws (member_id, wallet_id, amount, created_at, aasm_state, fund_uid, currency, fee, total) VALUES ($member_id, 0, $amount, '$datetime', 'inquiry', '$address', 'GAIA', $fee, $total)";
        $sqlupdate = "UPDATE gaia_withdrawings SET game_amount = game_amount - $total, game_earned = game_earned - $total WHERE member_id = $member_id";

        if($conn->query($sqlinquiry)){
            if($conn->query($sqlupdate)){
                $sqlquery = "SELECT * FROM gaia_withdrawings WHERE member_id = $member_id";
                $result = mysqli_query($conn, $sqlquery);
                $row = mysqli_fetch_assoc($result);

                echo json_encode(array("success"=>"success", "type"=>"inquirygame", "withdrawing"=>$row));
            }
        }

        break;
    }
    case "inquirywithdraw": {
        //FREE WALLET 에서 출금 (GAIA 수량으로)
        $member_id = $_POST["memberid"];
        $currency = $_POST["currency"];
        $amount = $_POST["amount"];
        $fee = $_POST["fee"];
        $total = $fee + $amount;
        $address = $_POST["address"];
        $datetime = date('Y-m-d H:i:s');

        $sqlwallet = "SELECT * FROM wallets WHERE member_id = $member_id and type = 1";
            
        $resultwallet = mysqli_query($conn, $sqlwallet);
        $rowwallet = mysqli_fetch_assoc($resultwallet);
            
        $balancewallet = $rowwallet["balance"];
        $walletid = $rowwallet['id'];
        $sumwallet = $rowwallet["balance"] + $rowwallet["locked"];

        $sqlinquiry = "";
        $sqlupdate1 = "";
        $minus1 = 0;
    
        $sqlinquiry = "INSERT INTO withdraws (member_id, wallet_id, amount, created_at, aasm_state, fund_uid, currency, fee, total) VALUES ($member_id, $walletid, $amount, '$datetime', 'inquiry', '$address', 'GAIA', $fee, $total)";
        $sqlupdate1 = "UPDATE wallets set balance = balance - $total, locked = locked + $total, last_withdraw_at = '$datetime' where member_id = $member_id and type = 1";
        $minus1 = 0 - $total;
    
    
        $plus1 = 0 - $minus1;
        // $wallet11 = $availablewallet1 + $minus1;
        // $wallet22 = $availablewallet2 + $minus2;
	$datetime = date('Y-m-d H:i:s');
        if($conn->query($sqlinquiry)){
            $withdraw_id = $conn->insert_id;
            if($conn->query($sqlupdate1)){
                $sqlaccountversion1 = "INSERT INTO account_versions (member_id, wallet_id, modifiable_id, modifiable_type, balance, locked, sum, available, created_at) VALUES ($member_id, $walletid, $withdraw_id, 'WithdrawInquiry', $minus1, $plus1, $sumwallet, 0, '$datetime')";
                if($conn->query($sqlaccountversion1)){

                    $sqlwallet1 = "SELECT * FROM wallets WHERE member_id = $member_id and type = 1";
                    $sqlwallet2 = "SELECT * FROM wallets WHERE member_id = $member_id and type = 2";
                    $sqlwallet3 = "SELECT * FROM wallets WHERE member_id = $member_id and type = 3";
                    $sqlwallet4 = "SELECT * FROM wallets WHERE member_id = $member_id and type = 4";
                    $sqlwallet5 = "SELECT * FROM wallets WHERE member_id = $member_id and type = 5";

                    $re1 = mysqli_query($conn, $sqlwallet1);
                    $re2 = mysqli_query($conn, $sqlwallet2);
                    $re3 = mysqli_query($conn, $sqlwallet3);
                    $re4 = mysqli_query($conn, $sqlwallet4);
                    $re5 = mysqli_query($conn, $sqlwallet5);

                    $ro1 = mysqli_fetch_assoc($re1);
                    $ro2 = mysqli_fetch_assoc($re2);
                    $ro3 = mysqli_fetch_assoc($re3);
                    $ro4 = mysqli_fetch_assoc($re4);
                    $ro5 = mysqli_fetch_assoc($re5);


                    echo json_encode(array("success"=>"success", "type"=>$request, "wallet1"=>$ro1, "wallet2"=>$ro2, "wallet3"=>$ro3, "wallet4"=>$ro4, "wallet4"=>$ro5));
                }
                else{
                    echo json_encode(array("success"=>"failed", "type"=>$request, "query"=>$sqlaccountversion1));    
                }
            }
            else{
                echo json_encode(array("success"=>"failed", "type"=>$request, "reason"=>"mistery", "query"=>$sqlupdate1));    
            }
        }
        else{
            echo json_encode(array("success"=>"failed", "type"=>$request, "reason"=>"mistery", "query"=>$sqlinquiry));
        }
        break;
    }
}

?>
