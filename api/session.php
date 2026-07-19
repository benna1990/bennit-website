<?php
/**
 * BennIT Remote Support — Zoho Assist session endpoint
 * Maakt een nieuwe on-demand sessie aan en geeft de customer_url terug.
 *
 * Credentials staan in config.php (niet in git, handmatig uploaden naar server).
 */

// ── CORS ──────────────────────────────────────────────────────────────────────
$allowed = ['https://bennit.nl', 'https://www.bennit.nl'];
$origin  = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed, true)) {
    header("Access-Control-Allow-Origin: $origin");
}
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// ── CONFIG ────────────────────────────────────────────────────────────────────
$config = __DIR__ . '/config.php';
if (!file_exists($config)) {
    http_response_code(503);
    echo json_encode(['error' => 'config.php not found on server']);
    exit;
}
require $config;

// ── STAP 1: Nieuw access token ophalen via refresh token ──────────────────────
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => 'https://accounts.zoho.eu/oauth/v2/token',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => http_build_query([
        'refresh_token' => ZOHO_REFRESH_TOKEN,
        'client_id'     => ZOHO_CLIENT_ID,
        'client_secret' => ZOHO_CLIENT_SECRET,
        'grant_type'    => 'refresh_token',
    ]),
    CURLOPT_TIMEOUT        => 10,
]);
$token_raw = curl_exec($ch);
$token_err = curl_error($ch);
curl_close($ch);

if ($token_err) {
    http_response_code(502);
    echo json_encode(['error' => 'Token request failed: ' . $token_err]);
    exit;
}

$token = json_decode($token_raw, true);
if (empty($token['access_token'])) {
    http_response_code(502);
    echo json_encode(['error' => 'No access_token in response', 'detail' => $token]);
    exit;
}

$access_token = $token['access_token'];

// ── STAP 2: Zoho Assist sessie aanmaken ───────────────────────────────────────
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => 'https://assist.zoho.eu/api/v2/session?type=rs',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => '',
    CURLOPT_HTTPHEADER     => [
        'Authorization: Zoho-oauthtoken ' . $access_token,
        'Content-Type: application/x-www-form-urlencoded',
    ],
    CURLOPT_TIMEOUT        => 10,
]);
$session_raw = curl_exec($ch);
$session_err = curl_error($ch);
curl_close($ch);

if ($session_err) {
    http_response_code(502);
    echo json_encode(['error' => 'Session request failed: ' . $session_err]);
    exit;
}

$session = json_decode($session_raw, true);
$customer_url = $session['representation']['customer_url'] ?? null;

if (!$customer_url) {
    http_response_code(502);
    echo json_encode(['error' => 'No customer_url in response', 'detail' => $session]);
    exit;
}

// ── SUCCES ────────────────────────────────────────────────────────────────────
echo json_encode(['url' => $customer_url]);
