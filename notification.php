<?php
    const HOST = "mysql.xmxcloud.net:3306";
    const USER = "u1070_Qeh4pgt2Ic";
    const PASSWORD = "u+6COSxZiNjFPqMK^9.HTOK7";
    const DB = "s1070_novaera";
    const ACCESS_TOKEN = "APP_USR-2387374436096259-030716-b5f369338cb357f1230dae6455d8f2cb-451257238";

   if ($_SERVER["REQUEST_METHOD"] != "POST") {
        http_response_code(500);
        return;
   }
   if (!(isset($_GET['data_id'])) && !(isset($_GET['type']))) {
        http_response_code(500);
        return;
   }
   if ($_GET['type'] != "payment") {
        http_response_code(500);
        return;
   }

   $id = $_GET['data_id'];

   $curl = curl_init();

   curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.mercadopago.com/v1/payments/' . $id,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . ACCESS_TOKEN
        )
   ));

   $payment = json_decode(curl_exec($curl), true);

   if ($payment["status"] === "approved") {
    
    $conn = new mysqli(HOST, USER, PASSWORD, DB);

    if ($conn->connect_error) {
        http_response_code(500);
        $conn->close();
        return;
    }
    $player = $payment["external_reference"];

    $insertSql = "INSERT INTO autopix_pendings (id, player) " 
            . "VALUES ('" . $id . "', '" . $player . "');";

    if ($conn->query($insertSql)) {
        $conn->close();
        http_response_code(201);
    } else {
        http_response_code(500);
        $conn->close();
    }
   }
  
?>