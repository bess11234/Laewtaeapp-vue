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

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isValidTokenAndRole($conn, $obj->token, 'STAFF') || isValidTokenAndRole($conn, $obj->token, 'MANAGER')) {

        $result = $conn->query("SELECT userID FROM tables WHERE tableID={$obj->bill->tableID}");
        $userID = $result->fetch()['userID'];

        if ($conn->query("INSERT INTO bills (userID, codeDiscount, paymentMethod, total) VALUES ($userID, '{$obj->bill->codeDiscount}', {$obj->bill->paymentMethod}, {$obj->bill->total} )")) {
            $result = $conn->query("SELECT billID FROM bills ORDER BY billID DESC LIMIT 1");
            $billID = $result->fetch()['billID'];

            if ($conn->query("UPDATE orders SET billID=$billID WHERE tableID={$obj->bill->tableID} AND billID is NULL ")) {
                if ($obj->bill->codeDiscount != ""){
                    $conn->query("DELETE FROM user_discount WHERE userID=$userID AND code='{$obj->bill->codeDiscount}' ");
                }

                $conn->query("UPDATE tables SET code=NULL, userID=NULL, status=1 WHERE tableID={$obj->bill->tableID} ");

                $payload = [
                    'result' => '',
                    'message' => "Check bill {$obj->bill->tableID} successful",
                    'status' => 0,
                ];

            } else {

                $payload = [
                    'result' => '',
                    'message' => "Check bill {$obj->bill->tableID} failed",
                    'status' => 1,
                ];
            }
        } else {
            $payload = [
                'result' => '',
                'message' => 'Check bill failed',
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
