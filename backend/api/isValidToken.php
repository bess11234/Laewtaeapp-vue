<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: *");

include '../configDatabase.php';
include '../JWT.php';
include './check.php';

$json = file_get_contents('php://input');
$obj = json_decode($json); //json_decode(, true) Make it to Array เพื่อใช้สำหรับการทำ JWT

$payload = [
    'result' => "",
    'message' => '',
    'status' => 0,
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isValidToken($conn, $obj->token)) {
        $payload = [
            'result' => decodeToken($obj->token, $obj->key),
            'message' => '',
            'status' => 0,
        ];
    }else{
        $payload = [
            'result' => '',
            'message' => 'Token expired',
            'status' => 1,
        ];
    }
} else {
    $payload = [
        'result' => '',
        'message' => '', //'Wrong Protocol'
        'status' => 1,
    ];
}

echo json_encode($payload);
unset($payload);
$conn = null;
