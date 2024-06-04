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
    if (isValidTokenAndRole($conn, $obj->token, 'MEMBER')) {        
        $result = $conn->query("SELECT userID FROM users WHERE token='{$obj->token}' ");
        $userID = $result->fetch()['userID'];
        $code = randomCode(7);

        if ($conn->query("INSERT INTO user_discount (userID, menuID, discount, code, expire) VALUES ($userID, {$obj->coupon->menuID}, {$obj->coupon->discount}, '$code', ADDDATE(CURDATE(), INTERVAL 1 YEAR) )")) {
            $conn->query("UPDATE users SET points=points-{$obj->coupon->cost} WHERE userID=$userID ");

            $payload = [
                'result' => '',
                'message' => 'Buy coupon successful',
                'status' => 0,
            ];
        } else {
            $payload = [
                'result' => '',
                'message' => 'Buy coupon failed',
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
