<?php
include_once('_common.php');


$now = new DateTime();
$now->add(new DateInterval("PT9H"));
$datetime = date_format($now, 'Y-m-d H:i:s');

$type = $_POST["type"];
$txid = $_POST["txid"];
$sql = "SELECT * FROM transactions WHERE txid = '$txid'";
$result = mysqli_query($conn, $sql);
if(mysqli_num_rows($result) > 0){
    echo "EXIST";
}
else{
    $address = $_POST["address"];
    $sqlm = "SELECT * FROM members WHERE eth_address = '$address'";
    $resultm = mysqli_query($conn, $sqlm);
    if(mysqli_num_rows($resultm) == 0){
        echo "NOT A MEMBER";
    }
    else{
        $rowm = mysqli_fetch_assoc($resultm);
        $member_id = $rowm["id"];


        $amount = $_POST["amount"];
        $sender = $_POST["sender"];

        
        if($type == "frim"){
            $coin = 'MDL';
            $sqlum = "UPDATE members SET frim = frim + $amount WHERE id = $member_id";
        }
        else if($type == "usdt"){
            $coin = 'USDT';
            $sqlum = "UPDATE members SET usdt = usdt + $amount WHERE id = $member_id";
        }
        else if($type == "tdos"){
            $coin = 'TDOS';
            $sqlum = "UPDATE members SET tdos = tdos + $amount WHERE id = $member_id";
        }
        else if($type == "eth"){
            $coin = 'ETH';
            $sqlum = "UPDATE members SET eth = eth + $amount WHERE id = $member_id";
        }
        else{
            $sqlum = "";
        }
        $sqli = "INSERT INTO transactions (address, txid, created_at, coin, type, amount, member_id, state, sender) VALUE ('$address', '$txid', '$datetime', '$coin', 'Deposit', $amount, $member_id, 2, '$sender')";

        if(strlen($sqlum) == 0){
            echo "NOT CORRECT ACCESS";
        }
        else{
            if($conn->query($sqli)){
                if($conn->query($sqlum)){
                    echo "SUCCESS";
                }
            }
        }
    }
}
?>