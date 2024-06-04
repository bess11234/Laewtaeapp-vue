<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: *");

include '../../configDatabase.php';
include '../../JWT.php';
include '../random.php';
include '../check.php';

$json = file_get_contents('php://input');
$obj = json_decode($json); //json_decode(, true) Make it to Array เพื่อใช้สำหรับการทำ JWT

$payload = [
    'result' => 'defalut',
    'message' => 'defalut',
    'status' => 0,
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isValidTokenAndRole($conn, $obj->token, 'MEMBER')) {
        $result = $conn->query("SELECT phoneNumber, memberName, email, points, role FROM users WHERE token='{$obj->token}' && status = 'ACTIVE'");
        $user = $result->fetchAll(\PDO::FETCH_ASSOC);

        $payload = [
            'result' => $user[0],
            'message' => '',
            'status' => 0,
        ];
    } else {

        $payload = [
            'result' => '',
            'message' => 'Token expired',
            'status' => 1,
        ];
    }
} else {
    $payload = [
        'result' => '',
        'message' =>
        //'',
        'Wrong Protocol',
        'status' => 1,
    ];
}

echo json_encode($payload);
unset($payload);
$conn = null;
// echo json_encode($obj).'<br>';

// $result = $conn->query("SELECT * FROM users");

// echo "<br>";
// foreach($result->fetchObject() as $key => $value){
//     echo "$key: $value; <br>";
// }

// $jwt = getCredential($obj);
// echo $jwt;

// $decode = decodeCredential($jwt);
// echo json_encode($decode);
