<?php
require_once 'vendor/autoload.php';
use \Firebase\JWT\JWT;

$key = file_get_contents('./config/jwt/private.pem');  // Clé privée
$payload = array(
    "iss" => "example.com",
    "iat" => time(),
    "exp" => time() + 3600,  // Le token expire dans 1 heure
    "data" => array("user_id" => 1)
);

$jwt = JWT::encode($payload, $key, 'RS256');  // Générer le JWT
echo "Token généré : " . $jwt . "\n";
