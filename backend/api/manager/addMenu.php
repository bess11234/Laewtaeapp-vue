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
        $b64 = $obj->menu->file;
        $bin = base64_decode($b64);
        $im = imagecreatefromstring($bin);

        $image = time() . ".webp";

        $target_dir = "../../../src/assets/images/menus/".$image;
        imagewebp($im, $target_dir, 0);

        if ($conn->query("INSERT INTO menus (menuName, price, description, image, categoryID) VALUES ('{$obj->menu->name}', {$obj->menu->price}, '{$obj->menu->description}', '$image', {$obj->menu->category})")) {

            $payload = [
                'result' => '',
                'message' => 'Add menu successful',
                'status' => 0,
            ];
        } else {
            $payload = [
                'result' => '',
                'message' => 'Add menu failed',
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
