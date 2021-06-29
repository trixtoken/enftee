<?php
include_once('_common.php');

$request = $_POST['type'];

switch($request){
    case "gmtocoin":{
        $now = new DateTime();
        $now->add(new DateInterval("PT9H"));
        $datetime = date_format($now, 'Y-m-d H:i:s');


        session_start();
        $member_id = $_SESSION["ss_member_id"];
        
        $coin = $_POST["coin"];
        $gmamount = $_POST["gmamount"];
        $gmtoamount = $_POST["gmtoamount"];

        $sql = "SELECT * FROM members WHERE id = $member_id";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);

        if($coin == 'USDT'){
            $sqlu = "UPDATE members SET usdt = usdt + $gmtoamount, game_money = game_money - $gmamount WHERE id = $member_id";
        }
        else if($coin == 'MDL'){
            $sqlu = "UPDATE members SET frim = frim + $gmtoamount, game_money = game_money - $gmamount WHERE id = $member_id";
        }
        else if($coin == 'TDOS'){
            $sqlu = "UPDATE members SET tdos = tdos + $gmtoamount, game_money = game_money - $gmamount WHERE id = $member_id";
        }
        else if($coin == 'ETH'){
            $sqlu = "UPDATE members SET eth = eth + $gmtoamount, game_money = game_money - $gmamount WHERE id = $member_id";
        }
        $sqli = "INSERT INTO transactions (address, txid, created_at, coin, type, amount, member_id, state) VALUE ('', '', '$datetime', '$coin', 'GMTransfer', $gmtoamount, $member_id, 2)";

        if($conn->query($sqlu)){
            if($conn->query($sqli)){
                header('Location:/transfer.php?r=success');
            }
            else{
                header('Location:/transfer.php?r=failed');
            }
        }
       
        break;
    }
    case "cointogm":{
        $now = new DateTime();
        $now->add(new DateInterval("PT9H"));
        $datetime = date_format($now, 'Y-m-d H:i:s');

        
        session_start();
        $member_id = $_SESSION["ss_member_id"];
        $sql = "SELECT * FROM members WHERE id = $member_id";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);

        $sqle = "SELECT * FROM exchanges ORDER BY created_at DESC LIMIT 1";
        $resulte = mysqli_query($conn, $sqle);
        $rowe = mysqli_fetch_assoc($resulte);

        $rate = 0;
        if($coin == 'USDT' || $coin == 'MDL'){
            $price = 1;
        }
        else if($coin == 'TDOS'){
            $price = 2;
        }
        else if($coin == 'ETH'){
            $price = 990;
        }

        $coinamount = $_POST["coinamount"];
        $cointoamount = $_POST["cointoamount"];

        if($coin == 'USDT'){
            $sqlu = "UPDATE members SET usdt = usdt - $coinamount, game_money = game_money + $cointoamount WHERE id = $member_id";
        }
        else if($coin == 'MDL'){
            $sqlu = "UPDATE members SET frim = frim - $coinamount, game_money = game_money + $cointoamount WHERE id = $member_id";
        }
        else if($coin == 'TDOS'){
            $sqlu = "UPDATE members SET tdos = tdos - $coinamount, game_money = game_money + $cointoamount WHERE id = $member_id";
        }
        else if($coin == 'ETH'){
            $sqlu = "UPDATE members SET eth = eth - $coinamount, game_money = game_money + $cointoamount WHERE id = $member_id";
        }
        $sqli = "INSERT INTO transactions (address, txid, created_at, coin, type, amount, member_id, state) VALUE ('', '', '$datetime', '$coin', 'CoinTransfer', $coinamount, $member_id, 2)";

        if($conn->query($sqlu)){
            if($conn->query($sqli)){
                header('Location:/transfer.php?r=success');
            }
            else{
                header('Location:/transfer.php?r=failed');
            }
        }

        break;
    }
}

?>