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
        $result = $conn->query("SELECT tableID, menuID, menuName, amount, orderAt FROM orders JOIN menus USING (menuID) WHERE status=1 ORDER BY orderAt");
        $orderFoods = $result->fetchAll(\PDO::FETCH_ASSOC);

        $result = $conn->query("SELECT tableID, menuID, menuName, amount, orderAt FROM orders JOIN menus USING (menuID) WHERE status=2 ORDER BY orderAt;");
        $orderServe = $result->fetchAll(\PDO::FETCH_ASSOC);

        $payload = [
            'result' => ['orderFoods'=>$orderFoods, 'orderServe'=>$orderServe],
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
