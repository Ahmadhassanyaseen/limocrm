<?php

declare(strict_types=1);

/**
 * OTP email service (Vercel).
 * POST /api/send-otp { lead_id }, POST /api/verify-otp { lead_id, otp }
 */

if (!defined('OTPM_MAIL_SERVER_BASE')) {
    define('OTPM_MAIL_SERVER_BASE', 'https://mail-server-plum.vercel.app');
}

/**
 * @return array{http:int, curlErr:string, data:?array<string,mixed>}
 */
function otp_mail_server_json_post(string $path, array $payload): array
{
    $url = rtrim(OTPM_MAIL_SERVER_BASE, '/') . $path;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_SLASHES));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    $body = curl_exec($ch);
    $curlErr = curl_error($ch);
    $http = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $data = json_decode(is_string($body) ? $body : '', true);
    $dataArr = is_array($data) ? $data : null;

    return ['http' => $http, 'curlErr' => $curlErr, 'data' => $dataArr];
}

/**
 * @return array{http:int, curlErr:string, data:?array<string,mixed>, ok:bool}
 */
function otp_mail_server_send_otp(string $leadIdUuid): array
{
    $r = otp_mail_server_json_post('/api/send-otp', ['lead_id' => $leadIdUuid]);
    $d = $r['data'] ?? [];
    $ok = $r['curlErr'] === ''
        && ($r['http'] >= 200 && $r['http'] < 300)
        && is_array($d)
        && (($d['success'] ?? false) === true);

    return $r + ['ok' => $ok];
}

/**
 * @return array{http:int, curlErr:string, data:?array<string,mixed>, ok:bool}
 */
function otp_mail_server_verify_otp(string $leadIdUuid, string $otpDigits): array
{
    $otpDigits = preg_replace('/\D/', '', $otpDigits);
    $r = otp_mail_server_json_post('/api/verify-otp', [
        'lead_id' => $leadIdUuid,
        'otp'     => $otpDigits,
    ]);
    $d = $r['data'] ?? [];
    $already = isset($d['message']) && is_string($d['message'])
        && stripos($d['message'], 'already verified') !== false;
    $ok = $r['curlErr'] === ''
        && ($r['http'] >= 200 && $r['http'] < 300)
        && (
            (($d['success'] ?? false) === true)
            || (($d['verified'] ?? false) === true)
            || $already
        );

    return $r + ['ok' => $ok];
}
