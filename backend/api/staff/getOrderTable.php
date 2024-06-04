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
        $result = $conn->query("SELECT menuID, userID, SUM(amount) as amount, menus.price as `price`, menus.menuName as `menuName` FROM orders JOIN menus USING (menuID) JOIN tables USING (tableID) WHERE tableID={$obj->id} AND billID is NULL GROUP BY menuID");
        $orders = $result->fetchAll(\PDO::FETCH_ASSOC);

        $result = $conn->query("SELECT userID, menuID, discount, code, menuName FROM user_discount JOIN menus USING (menuID) WHERE userID=1 AND CURDATE() < expire");
        $user_discount = $result->fetchAll(\PDO::FETCH_ASSOC);

        $payload = [
            'result' => ['orders'=>$orders, 'user_discount'=>$user_discount],
            'message' => '',
            'status' => 0,
        ];

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
