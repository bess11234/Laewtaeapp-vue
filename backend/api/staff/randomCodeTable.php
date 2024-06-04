<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: *");

include '../../configDatabase.php';
include '../check.php';
include '../../JWT.php';
include '../random.php';

$json = file_get_contents('php://input');
$obj = json_decode($json); //json_decode(, true) Make it to Array เพื่อใช้สำหรับการทำ JWT

// fetchAll(\PDO::FETCH_ASSOC) ดึงเฉพาะที่มี keys

$payload = [
    'result' => "",
    'message' => '',
    'status' => 0,
];

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isValidTokenAndRole($conn, $obj->token, 'STAFF') || isValidTokenAndRole($conn, $obj->token, 'MANAGER')) {

        $code = randomCode(4);
        try {
            $result = $conn->query("UPDATE tables SET code='$code', status=2 WHERE tableID={$obj->tableID} ");
            if ($result){
                $payload = [
                    'result' => '',
                    'message' => 'Random code successful',  
                    'status' => 0,
                ];
            } else {
                $payload = [
                    'result' => '',
                    'message' => 'Random code failed',
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
