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

        if (!str_contains($obj->menu->file, '.webp')){

            $b64 = $obj->menu->file;
            $bin = base64_decode($b64);
            $im = imagecreatefromstring($bin);
    
            $obj->menu->file = time() . ".webp";
    
            $target_dir = "../../../src/assets/images/menus/".$obj->menu->file;
            imagewebp($im, $target_dir, 0);
        }

        if ($conn->query("UPDATE menus SET menuName='{$obj->menu->menuName}', price={$obj->menu->price}, description='{$obj->menu->description}', image='{$obj->menu->file}', categoryID={$obj->menu->categoryID} WHERE menuID={$obj->menu->menuID}")) {

            $payload = [
                'result' => '',
                'message' => 'Update menu successful',
                'status' => 0,
            ];
        } else {
            $payload = [
                'result' => '',
                'message' => 'Update menu failed',
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
