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
    if (isValidTokenAndRole($conn, $obj->token, 'MANAGER') || isValidTokenAndRole($conn, $obj->token, 'STAFF')) {
        $result = $conn->query("SELECT tableID, code, capacity, tables.status as `status`, users.memberName as `memberName` FROM tables LEFT OUTER JOIN users USING (userID)");
        $payload = [
            'result' => $result->fetchAll(\PDO::FETCH_ASSOC),
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