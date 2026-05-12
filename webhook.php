<?php
require_once 'config.php';

$body = json_decode(file_get_contents('php://input'), true);

file_put_contents(
    __DIR__ . '/webhook-log.txt',
    date('Y-m-d H:i:s') . ' — ' . json_encode($body) . PHP_EOL,
    FILE_APPEND
);

if (!isset($body['type']) || $body['type'] !== 'payment') {
    http_response_code(200);
    exit;
}

$paymentId = $body['data']['id'];

$ch = curl_init("https://api.mercadopago.com/v1/payments/{$paymentId}");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . MP_ACCESS_TOKEN
]);

$response = json_decode(curl_exec($ch), true);
curl_close($ch);

$estado = $response['status'] ?? 'unknown';
$monto  = $response['transaction_amount'] ?? 0;
$email  = $response['payer']['email'] ?? 'desconocido';

file_put_contents(
    __DIR__ . '/pagos-log.txt',
    date('Y-m-d H:i:s') . " — Estado: {$estado} | Monto: \${$monto} | Email: {$email}" . PHP_EOL,
    FILE_APPEND
);

// Notificación por email a la ONG
if ($estado === 'approved') {
    $para    = 'policiasunidosong@gmail.com';
    $asunto  = "Nueva donacion recibida - $" . number_format($monto, 0, ',', '.');
    $mensaje = "Nueva donacion recibida:\n\nMonto: $" . number_format($monto, 0, ',', '.') . "\nDonante: {$email}\nFecha: " . date('d/m/Y H:i') . "\nID operacion MP: {$paymentId}";
    mail($para, $asunto, $mensaje);
}

http_response_code(200);
