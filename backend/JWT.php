<?php
require __DIR__ . '/vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// $payload = [
//     'iss' => 'http://example.org',
//     'aud' => 'http://example.com',
//     'iat' => 1356999524,
//     'nbf' => 1357000000
// ];

function getToken(array $payload, string $key){
    $jwt = JWT::encode($payload, $key, 'HS256');
    return $jwt;
}

function decodeToken(string $jwt, string $key){
    $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
    return $decoded;
}

/**
 * IMPORTANT:
 * You must specify supported algorithms for your application. See
 * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
 * for a list of spec-compliant algorithms.
 */
// $jwt = JWT::encode($payload, $key, 'HS256');
// $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
// print_r($decoded);

// echo "<br>";

// echo json_encode($decoded->iss);

// Pass a stdClass in as the third parameter to get the decoded header values
// $decoded = JWT::decode($jwt, new Key($key, 'HS256'), $headers = new stdClass());
// print_r($headers);

/*
 NOTE: This will now be an object instead of an associative array. To get
 an associative array, you will need to cast it as such:
*/

// $decoded_array = (array) $decoded;

/**
 * You can add a leeway to account for when there is a clock skew times between
 * the signing and verifying servers. It is recommended that this leeway should
 * not be bigger than a few minutes.
 *
 * Source: http://self-issued.info/docs/draft-ietf-oauth-json-web-token.html#nbfDef
 */
// JWT::$leeway = 60; // $leeway in seconds
// $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
