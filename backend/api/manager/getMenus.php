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

    $result = $conn->query("SELECT menuName, price, description, image, menu_category.name as `categoryName`, menuID FROM menus LEFT JOIN menu_category USING (categoryID);");
    $menus = $result->fetchAll(\PDO::FETCH_ASSOC);

    $result = $conn->query("SELECT * FROM menu_category");
    $categories = $result->fetchAll(\PDO::FETCH_ASSOC);

    $payload = [
        'result' => ['menus' => $menus, 'categories' => $categories],
        'message' => '',
        'status' => 0,
    ];
}

echo json_encode($payload);
unset($payload);
$conn = null;
