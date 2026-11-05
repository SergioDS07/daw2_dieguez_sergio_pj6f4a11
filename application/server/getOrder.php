<?php
require_once 'functions.php';

$orders = readJsonFile(ORDERS_FILE);
if ($orders === null) {
    sendNotFoundPage('The file orders.json could not be found or opened.');
    exit;
}

$orderId = trim($_GET['orderId'] ?? '');
$order   = findOrderById($orders, $orderId);

if ($order === null) {
    sendNotFoundPage('Order "' . $orderId . '" was not found.');
    exit;
}

sendHtmlPage(200, 'Order ' . $orderId, renderOrdersTable([$order]));
