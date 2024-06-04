<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: *");

include '../../configDatabase.php';
include '../check.php';
include '../../JWT.php';

$json = file_get_contents('php://input');
$obj = json_decode($json); //json_decode(, true) Make it to Array เพื่อใช้สำหรับการทำ JWT

// fetchAll(\PDO::FETCH_ASSOC) ดึงเฉพาะที่มี keys

$payload = [
    'result' => "",
    'message' => '',
    'status' => 0,
];

function message($status, $tableID){
    switch ($status){
        case 2: {
            return "Complete orders in table $tableID successful";
        }
        case 3: {
            return "Cancelled orders in table $tableID successful";
        }
        case 4: {
            return "Served orders in table $tableID successful";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isValidTokenAndRole($conn, $obj->token, 'STAFF') || isValidTokenAndRole($conn, $obj->token, 'MANAGER')) {

        try {
            $result = $conn->query("UPDATE orders SET status={$obj->order->status} WHERE tableID={$obj->order->tableID} AND orderAt='{$obj->order->orderAt}' ");
            if ($result){
                $payload = [
                    'result' => '',
                    'message' => message($obj->order->status, $obj->order->tableID),
                    'status' => 0,
                ];
            } else {
                $payload = [
                    'result' => '',
                    'message' => 'Update status orders failed',
                    'status' => 1,
                ];
            }
        } catch (Exception $error) {
            $payload = [
                'result' => '',
                'message' => $error->getMessage(),
                'status' => 1,
            ];
        }
    } else {
        $payload = [
            'result' => "",
            'message' => "Token expired", //OR role not match
            'status' => 1,
        ];
    }
}

echo json_encode($payload);
unset($payload);
$conn = null;
