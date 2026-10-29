<?php
require_once 'functions.php';

// Validates the POST data. Returns an error message or null if everything is fine.
function validateOrderData(array $data): ?string
{
    foreach (['orderId', 'fullName', 'address', 'email', 'phone'] as $field) {
        if (trim($data[$field] ?? '') === '') {
            return "Missing field: $field";
        }
    }
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        return 'Invalid email address';
    }
    return null;
}

// Existing orders (empty array if the file does not exist yet).
// Returns null if the file exists but cannot be opened.
function loadOrdersForWriting(): ?array
{
    return is_file(ORDERS_FILE) ? readJsonFile(ORDERS_FILE) : [];
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendPlainText(405, 'METHOD_NOT_ALLOWED');
    exit;
}

$data = [];
foreach (['orderId', 'fullName', 'address', 'email', 'phone'] as $field) {
    $data[$field] = is_string($_POST[$field] ?? null) ? trim($_POST[$field]) : '';
}

$selected = is_array($_POST['products'] ?? null) ? $_POST['products'] : [];
$error = validateOrderData($data);
$lines = buildOrderLines($selected);
if ($error === null && $lines === null) {
    $error = 'Select at least one product with a valid quantity';
}
if ($error !== null) {
    sendPlainText(400, $error);
    exit;
}

$orders = loadOrdersForWriting();
if ($orders === null) {
    sendNotFoundPage('The file orders.json could not be found or opened.');
    exit;
}
if (findOrderById($orders, $data['orderId']) !== null) {
    sendPlainText(409, 'An order with this code already exists');
    exit;
}

$order    = buildOrder($data, $lines);
$orders[] = $order;

if (!writeJsonFile(ORDERS_FILE, $orders)) {
    sendNotFoundPage('The file orders.json could not be created or written.');
    exit;
}

// Only the result is sent back (no HTML/JS/CSS).
sendPlainText(200, number_format($order['totalWithVat'], 2, '.', ''));
