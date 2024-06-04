<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: *");

include '../../configDatabase.php';
include '../check.php';
include '../../JWT.php';

$json = file_get_contents('php://input');
$obj = json_decode($json); //json_decode(, true) Make it to Array เพื่อใช้สำหรับการทำ JWT

$payload = [
    'result' => "",
    'message' => '',
    'status' => 0,
];

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $code = "";

    $result = $conn->query("SELECT tableID FROM tables WHERE code='{$obj->code}' ");
    if ($result->rowCount() == 1) {

        $tableID = $result->fetch()['tableID'];
        
        $orders = json_decode(json_encode($obj->orders), true);
        $data = [];
        foreach($orders as $menuID=>$info){
            array_push($data, "($tableID, $menuID, {$info['amount']})");
        }
        $data = join(', ', $data);

        if ($conn->query("INSERT INTO orders (tableID, menuID, amount) VALUES $data")){

            $payload = [
                'result' => $tableID,
                'message' => 'Order successful',
                'status' => 0,
            ];
        }else{

            $payload = [
                'result' => $tableID,
                'message' => 'Order failed',
                'status' => 0,
            ];
        }
    } else {

        $payload = [
            'result' => '',
            'message' => 'Code expired',
            'status' => 1,
        ];
    }
}


echo json_encode($payload);
unset($payload);
$conn = null;
