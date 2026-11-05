<?php
require_once 'functions.php';

$orders = readJsonFile(ORDERS_FILE);
if ($orders === null) {
    sendNotFoundPage('The file orders.json could not be found or opened.');
    exit;
}

sendHtmlPage(200, 'All orders', $orders ? renderOrdersTable($orders) : '<p>There are no orders yet.</p>');
