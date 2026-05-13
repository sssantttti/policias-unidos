<?php
require_once 'config.php';

$body = json_decode(file_get_contents('php://input'), true);

file_put_contents(
    __DIR__ . '/webhook-log.txt',
    date('Y-m-d H:i:s') . ' — ' . json_encode($body) . PHP_EOL,
    FILE_APPEND
);

$tipo = $body['type'] ?? '';

// Pago único aprobado
if ($tipo === 'payment') {
    $paymentId = $body['data']['id'];
    $ch = curl_init("https://api.mercadopago.com/v1/payments/{$paymentId}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . MP_ACCESS_TOKEN]);
    $response = json_decode(curl_exec($ch), true);
    curl_close($ch);

    $estado = $response['status'] ?? 'unknown';
    $monto  = $response['transaction_amount'] ?? 0;
    $email  = $response['payer']['email'] ?? 'desconocido';

    file_put_contents(__DIR__ . '/pagos-log.txt',
        date('Y-m-d H:i:s') . " — ÚNICO | Estado: {$estado} | Monto: \${$monto} | Email: {$email}" . PHP_EOL,
        FILE_APPEND
    );

    if ($estado === 'approved') {
        $para    = 'policiasunidosong@gmail.com';
        $asunto  = "Nueva donación única — $" . number_format($monto, 0, ',', '.');
        $mensaje = "Donación única aprobada:\n\nMonto: $" . number_format($monto, 0, ',', '.') . "\nDonante: {$email}\nFecha: " . date('d/m/Y H:i') . "\nID MP: {$paymentId}";
        mail($para, $asunto, $mensaje);
    }
}

// Suscripción recurrente
if ($tipo === 'subscription_preapproval') {
    $preapprovalId = $body['data']['id'];
    $ch = curl_init("https://api.mercadopago.com/preapproval/{$preapprovalId}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . MP_ACCESS_TOKEN]);
    $response = json_decode(curl_exec($ch), true);
    curl_close($ch);

    $estado = $response['status'] ?? 'unknown';
    $monto  = $response['auto_recurring']['transaction_amount'] ?? 0;
    $email  = $response['payer_email'] ?? 'desconocido';

    file_put_contents(__DIR__ . '/pagos-log.txt',
        date('Y-m-d H:i:s') . " — RECURRENTE | Estado: {$estado} | Monto: \${$monto}/mes | Email: {$email}" . PHP_EOL,
        FILE_APPEND
    );

    if ($estado === 'authorized') {
        $para    = 'policiasunidosong@gmail.com';
        $asunto  = "Nueva suscripción mensual — $" . number_format($monto, 0, ',', '.') . "/mes";
        $mensaje = "Nueva suscripción mensual:\n\nMonto: $" . number_format($monto, 0, ',', '.') . "/mes\nDonante: {$email}\nFecha: " . date('d/m/Y H:i') . "\nID suscripción: {$preapprovalId}";
        mail($para, $asunto, $mensaje);
    }
}

http_response_code(200);
