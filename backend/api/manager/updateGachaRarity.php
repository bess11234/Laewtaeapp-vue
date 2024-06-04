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

        try {
            $result = $conn->query("UPDATE gacha_item SET rarity={$obj->menu->rarity} WHERE gachaID={$obj->menu->gachaID} AND menuID={$obj->menu->menuID} AND discount='{$obj->menu->discount}' ");
            
            if ($result){
                $payload = [
                    'result' => '',
                    'message' => 'Update gacha rarity successful',
                    'status' => 0,
                ];
            } else {
                $payload = [
                    'result' => '',
                    'message' => 'Update gacha rarity failed',
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
