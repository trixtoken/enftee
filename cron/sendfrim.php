<?php

include_once('/var/www/html/common.php');


$sqlw = "SELECT * FROM withdraws WHERE state = 1 and coin = 'MDL'";
$resultw = mysqli_query($conn, $sqlw);
while($roww = mysqli_fetch_assoc($resultw)){
    $wid = $roww["id"];

    $now = new DateTime();
    $now->add(new DateInterval("PT9H"));
    $datetime = date_format($now, 'Y-m-d H:i:s');

    $member_id = $roww["member_id"];
    $address = $roww["address"];
    $amount = floatval($roww["amount"]);
    $sqlm = "SELECT * FROM members WHERE id = $member_id";
    $resultm = mysqli_query($conn, $sqlm);
    $rowm = mysqli_fetch_assoc($resultm);
    $ethaddress = $rowm["eth_address"];

    $url = "http://3.34.125.66:3000/sendfri";

    $data = array(
            "sender" => $ethaddress,
            "getter" => $address,
            "amount" => $amount,
            "id" => "1"
    );

    $json_encoded_data = json_encode($data);
    echo $json_encoded_data;

    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_encoded_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json_encoded_data))
    );
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
    curl_setopt($ch, CURLOPT_TIMEOUT, 400);

    $resulti = json_decode(curl_exec($ch));
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
    }
    curl_close($ch);
    if (isset($error_msg)) {
        // TODO - Handle cURL error accordingly
        echo "ERROR ".$error_msg."\n";
    }
    else{
        $result = $resulti->transactionHash;
        if(strlen($result) > 0){

            $sqluw = "UPDATE withdraws SET state = 2, txid = '$result' WHERE id = $wid";
            $sqlum = "UPDATE members SET frim_sent = frim_sent - $amount WHERE id = $member_id";
            $sqlit = "INSERT INTO transactions (address, txid, created_at, coin, type, amount, member_id, state, sender) VALUE ('$address', '$result', '$datetime', 'MDL', 'Withdraw', $amount, $member_id, 2, '$eth_address')";
            if($conn->query($sqluw)){
                if($conn->query($sqlum)){
                    if($conn->query($sqlit)){
                        echo "S ";
                    }
                }
            }
        }
        else{
            echo "F ";
        }
    }
}
?>