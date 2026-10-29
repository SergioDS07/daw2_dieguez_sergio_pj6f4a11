<?php
// Shared helper functions for the server side (PHP).

const VAT_RATE    = 0.21;
const ORDERS_FILE = __DIR__ . '/../../orders/orders.json';
const USERS_FILE  = __DIR__ . '/../../users/users.json';

// Product catalogue (the server owns the prices, the client only displays them).
const PRODUCTS = [
    'P001' => ['name' => 'Museum Entry Ticket',        'price' => 12.00],
    'P002' => ['name' => 'Theatre Play Ticket',        'price' => 25.50],
    'P003' => ['name' => 'Art History Book',           'price' => 34.90],
    'P004' => ['name' => 'Limited Edition Art Poster', 'price' => 18.00],
];

/* ---------- Files / JSON ---------- */

// Returns the decoded JSON as an associative array, or null if it can't be read.
function readJsonFile(string $path): ?array
{
    if (!is_file($path) || !is_readable($path)) {
        return null;
    }
    $content = file_get_contents($path);
    if ($content === false) {
        return null;
    }
    $data = json_decode($content, true);
    return is_array($data) ? $data : null;
}

function writeJsonFile(string $path, array $data): bool
{
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return $json !== false && file_put_contents($path, $json, LOCK_EX) !== false;
}

/* ---------- HTTP responses ---------- */

function sendPlainText(int $code, string $text): void
{
    http_response_code($code);
    header('Content-Type: text/plain; charset=UTF-8');
    echo $text;
}

function renderNavButtons(): string
{
    return '<div class="nav-buttons">'
        . '<a class="button" href="../client/viewOrders.html">Back to view orders menu</a>'
        . '<a class="button" href="../client/menu.html">Back to operations menu</a>'
        . '<a class="button" href="../client/index.html">Back to start</a>'
        . '</div>';
}

// Sends a full HTML document with the given status code.
function sendHtmlPage(int $code, string $title, string $body): void
{
    http_response_code($code);
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">'
        . '<title>' . htmlspecialchars($title) . '</title>'
        . '<link rel="stylesheet" href="../client/css/style.css"></head><body>'
        . '<main class="container wide"><h1>' . htmlspecialchars($title) . '</h1>'
        . $body . renderNavButtons() . '</main></body></html>';
}

function sendNotFoundPage(string $message): void
{
    sendHtmlPage(404, 'Error 404 - Not Found', '<p class="error">' . htmlspecialchars($message) . '</p>');
}

/* ---------- Business logic ---------- */

function authenticateUser(string $user, string $password): bool
{
    $users = readJsonFile(USERS_FILE);
    if ($users === null) {
        return false;
    }
    foreach ($users as $registered) {
        if ($registered['user'] === $user && password_verify($password, $registered['password'])) {
            return true;
        }
    }
    return false;
}

// Builds the order lines (multidimensional array) from [productCode => quantity].
function buildOrderLines(array $selected): ?array
{
    $lines = [];
    foreach ($selected as $code => $quantity) {
        if (!isset(PRODUCTS[$code]) || !ctype_digit((string)$quantity) || (int)$quantity < 1) {
            return null;
        }
        $price = PRODUCTS[$code]['price'];
        $lines[] = [
            'productCode' => $code,
            'productName' => PRODUCTS[$code]['name'],
            'unitPrice'   => $price,
            'quantity'    => (int)$quantity,
            'subtotal'    => round($price * (int)$quantity, 2),
        ];
    }
    return $lines ?: null;
}

function calculateTotalWithVat(array $lines): float
{
    $net = 0;
    foreach ($lines as $line) {
        $net += $line['subtotal'];
    }
    return round($net * (1 + VAT_RATE), 2);
}

function buildOrder(array $data, array $lines): array
{
    return [
        'orderId'      => $data['orderId'],
        'customer'     => [
            'fullName' => $data['fullName'],
            'address'  => $data['address'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
        ],
        'products'     => $lines,
        'totalWithVat' => calculateTotalWithVat($lines),
    ];
}

function findOrderById(array $orders, string $orderId): ?array
{
    foreach ($orders as $order) {
        if ($order['orderId'] === $orderId) {
            return $order;
        }
    }
    return null;
}

/* ---------- HTML rendering (only for the "view orders" features) ---------- */

function renderProductsTable(array $lines): string
{
    $html = '<table class="inner"><tr><th>Code</th><th>Product</th><th>Unit price</th><th>Qty</th><th>Subtotal</th></tr>';
    foreach ($lines as $line) {
        $html .= '<tr><td>' . htmlspecialchars($line['productCode']) . '</td>'
            . '<td>' . htmlspecialchars($line['productName']) . '</td>'
            . '<td>' . number_format($line['unitPrice'], 2) . ' €</td>'
            . '<td>' . $line['quantity'] . '</td>'
            . '<td>' . number_format($line['subtotal'], 2) . ' €</td></tr>';
    }
    return $html . '</table>';
}

function renderOrdersTable(array $orders): string
{
    $html = '<table><tr><th>Order ID</th><th>Customer full name</th><th>Address</th>'
        . '<th>Email</th><th>Phone</th><th>Products ordered</th><th>Total price (incl. 21% VAT)</th></tr>';
    foreach ($orders as $order) {
        $c = $order['customer'];
        $html .= '<tr><td>' . htmlspecialchars($order['orderId']) . '</td>'
            . '<td>' . htmlspecialchars($c['fullName']) . '</td>'
            . '<td>' . htmlspecialchars($c['address']) . '</td>'
            . '<td>' . htmlspecialchars($c['email']) . '</td>'
            . '<td>' . htmlspecialchars($c['phone']) . '</td>'
            . '<td>' . renderProductsTable($order['products']) . '</td>'
            . '<td>' . number_format($order['totalWithVat'], 2) . ' €</td></tr>';
    }
    return $html . '</table>';
}
