<?php
// CORS - permitir requests desde GitHub Pages
header('Access-Control-Allow-Origin: https://sssantttti.github.io');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Manejar preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true);
$monto = isset($body['monto']) ? floatval($body['monto']) : 0;

if ($monto <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Monto inválido']);
    exit;
}

$preference = [
    "items" => [
        [
            "title" => "Donación a Policías Unidos",
            "quantity" => 1,
            "unit_price" => $monto,
            "currency_id" => "ARS"
        ]
    ],
    "statement_descriptor" => "Policias Unidos ONG"
];

$ch = curl_init('https://api.mercadopago.com/checkout/preferences');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($preference));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . MP_ACCESS_TOKEN
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

http_response_code($httpCode);
echo $response;
