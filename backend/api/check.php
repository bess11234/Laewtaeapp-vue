<?php
function isValidToken($conn, string $jwt)
{
    $sql = "SELECT token FROM users WHERE token='{$jwt}'";
    $result = $conn->query($sql);
    return $result->rowCount() == 1;
}

function isValidTokenAndRole($conn, string $jwt, string $role){
    $sql = "SELECT token FROM users WHERE token='{$jwt}' AND role='$role'";
    $result = $conn->query($sql);
    return $result->rowCount() == 1;
}