<?php
use LDAP\Result;
$server_name = 'localhost';
$dbname = 'laewtaeapp';
$username = 'bess1123';
$password = '1123';

try {
    $conn = new PDO("mysql:host=$server_name;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    header('HTTP/1.1 500 internal server error');
    exit;
}
