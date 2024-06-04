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
    if (isValidTokenAndRole($conn, $obj->token, 'MANAGER')) {
        if ($conn->query("INSERT INTO gacha_item (gachaID, menuID, rarity, discount) VALUES ({$obj->item->gachaID}, {$obj->item->menuID}, {$obj->item->rarity}, {$obj->item->discount})")) {

            $payload = [
                'result' => '',
                'message' => 'Add gacha item successful',
                'status' => 0,
            ];
        } else {
            $payload = [
                'result' => '',
                'message' => 'Add gacha item failed',
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
