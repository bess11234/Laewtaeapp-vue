<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: *");

include '../../configDatabase.php';
include '../check.php';
include '../../JWT.php';

$json = file_get_contents('php://input');
$obj = json_decode($json); //json_decode(, true) Make it to Array เพื่อใช้สำหรับการทำ JWT

$payload = [
    'result' => "",
    'message' => '',
    'status' => 0,
];

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isValidToken($conn, $obj->token)) {
        if ($conn->query("UPDATE users SET memberName='{$obj->name}' WHERE token='{$obj->token}'")) {

            $result = $conn->query("SELECT phoneNumber, memberName, email, points, role FROM users WHERE token='{$obj->token}' && status = 'ACTIVE'");
            $user = json_decode(json_encode($result->fetchObject()), true);

            $jwt = getToken($user, $obj->key);

            // Update token
            $conn->query("UPDATE users SET token='$jwt' WHERE token='{$obj->token}'");

            $payload = [
                'result' => $jwt,
                'message' => 'Update name successful',
                'status' => 0,
            ];
        } else {

            $payload = [
                'result' => "",
                'message' => 'Update name falied',
                'status' => 1,
            ];
        }
    } else {
        $payload = [
            'result' => "",
            'message' => "Token expired",
            'status' => 1,
        ];
    }
}

echo json_encode($payload);
unset($payload);
$conn = null;