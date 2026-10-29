<?php
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendPlainText(405, 'METHOD_NOT_ALLOWED');
    exit;
}

$user     = trim($_POST['user'] ?? '');
$password = $_POST['password'] ?? '';

if ($user !== '' && $password !== '' && authenticateUser($user, $password)) {
    sendPlainText(200, 'OK');
} else {
    sendPlainText(401, 'INVALID_CREDENTIALS');
}
