<?php
header('Access-Control-Allow-Origin: https://sssantttti.github.io');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

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

$body  = json_decode(file_get_contents('php://input'), true);
$monto = isset($body['monto']) ? floatval($body['monto']) : 0;
$email = isset($body['email']) ? trim($body['email']) : '';

if ($monto <= 0 || empty($email)) {
    http_response_code(400);
    echo json_encode(['error' => 'Monto o email inválido']);
    exit;
}

$preapproval = [
    "reason"         => "Donación mensual a Policías Unidos",
    "auto_recurring" => [
        "frequency"          => 1,
        "frequency_type"     => "months",
        "transaction_amount" => $monto,
        "currency_id"        => "ARS"
    ],
    "back_url"    => MP_URL_RECURRENTE_SUCCESS,
    "payer_email" => $email
];

$ch = curl_init('https://api.mercadopago.com/preapproval');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($preapproval));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . MP_ACCESS_TOKEN
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

http_response_code($httpCode);
echo $response;
