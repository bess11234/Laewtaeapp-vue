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
            $result = $conn->query("UPDATE gacha_banner SET name='{$obj->gacha->name}', description='{$obj->gacha->description}', cost={$obj->gacha->cost}, expire='{$obj->gacha->expire}' WHERE gachaID={$obj->gacha->id} ");
            if ($result){
                $payload = [
                    'result' => '',
                    'message' => 'Update gacha information successful',
                    'status' => 0,
                ];
            } else {
                $payload = [
                    'result' => '',
                    'message' => 'Update gacha information failed',
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
