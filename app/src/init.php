<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['request_log']) || !is_array($_SESSION['request_log'])) {
    $_SESSION['request_log'] = [];
}

$_SESSION['request_log'][] = [
    'timestamp' => date('c'),
    'method' => $_SERVER['REQUEST_METHOD'] ?? 'CLI',
    'uri' => $_SERVER['REQUEST_URI'] ?? '/',
    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
];

// Keep only the most recent 100 request entries per session.
if (count($_SESSION['request_log']) > 100) {
    $_SESSION['request_log'] = array_slice($_SESSION['request_log'], -100);
}
