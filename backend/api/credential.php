<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: *");

include '../configDatabase.php';
include '../JWT.php';
include './random.php';

$json = file_get_contents('php://input');
$obj = json_decode($json); //json_decode(, true) Make it to Array เพื่อใช้สำหรับการทำ JWT

$payload = [
    'result' => 'defalut',
    'message' => 'defalut',
    'status' => 0,
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $result = $conn->query("SELECT passwd FROM users WHERE email='{$obj->email}' && status = 'ACTIVE'");
    if ($result->rowCount() == 1) {
        if (password_verify($obj->password, $result->fetchObject()->passwd)) {
            $result = $conn->query("SELECT phoneNumber, memberName, email, points, role FROM users WHERE email='{$obj->email}' && status = 'ACTIVE'");
            $user = json_decode(json_encode($result->fetchObject()), true);

            // Create secret_key
            $secret_key = randomPassword(10);

            // Create token with secret_key
            $jwt = getToken($user, $secret_key);

            // Update token in database
            if ($conn->query("UPDATE users SET token='$jwt' WHERE email='{$obj->email}'") === FALSE) {
                $payload = [
                    'result' => '',
                    'message' => 'Error update token',
                    'status' => 1,
                ];

            } else {
                $payload = [
                    'result' => ['jwt' => $jwt, 'key' => $secret_key] ,
                    'message' => 'Login Successful',
                    'status' => 0,
                ];
            }
        } else {
            $payload = [
                'result' => '',
                'message' => 'Wrong Password',
                'status' => 1,
            ];
        }
    } else {
        $payload = [
            'result' => '',
            'message' =>
            //'',
            'Database have familar email in users',
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

