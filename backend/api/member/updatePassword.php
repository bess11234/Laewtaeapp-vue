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
        
        $result = $conn->query("SELECT passwd FROM users WHERE token='{$obj->token}'");
        if (password_verify($obj->old, $result->fetchObject()->passwd)){
            
            $hash_password = password_hash($obj->new, PASSWORD_BCRYPT);
            if ($conn->query("UPDATE users SET passwd='$hash_password' WHERE token='{$obj->token}'")){
                
                $payload = [
                    'result' => "",
                    'message' => "Update password successful",
                    'status' => 0,
                ];
            }else{
                
                $payload = [
                    'result' => "",
                    'message' => "Update password failed",
                    'status' => 1,
                ];
            }
        }else{
            $payload = [
                'result' => "",
                'message' => "Incorrect password",
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