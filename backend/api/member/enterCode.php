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

    $userID = "";
    $code = "";

    if ($obj->token != "") {
        $result = $conn->query("SELECT userID FROM users WHERE token='{$obj->token}'");
        if ($result->rowCount() == 1) $userID = $result->fetch()['userID'];
    }

    $result = $conn->query("SELECT tableID FROM tables WHERE code='{$obj->code}' ");
    if ($result->rowCount() == 1) {

        $tableID = $result->fetch()['tableID'];
        
        $result = $conn->query("SELECT userID FROM tables WHERE tableID=$tableID");
        $isUserExist = $result->fetch()['userID'] != null;

        if ($userID == "" || $isUserExist) {
            if ($conn->query("UPDATE tables SET status=3 WHERE tableID=$tableID")) {

                $payload = [
                    'result' => $tableID,
                    'message' => 'Code valid',
                    'status' => 0,
                ];
            }
        } else {
            if ($conn->query("UPDATE tables SET status=3, userID=$userID WHERE tableID=$tableID")) {
                
                $payload = [
                    'result' => $tableID,
                    'message' => 'Code valid',
                    'status' => 0,
                ];
            }
        }
    } else {

        $payload = [
            'result' => '',
            'message' => 'Code invalid',
            'status' => 1,
        ];
    }
}


echo json_encode($payload);
unset($payload);
$conn = null;
