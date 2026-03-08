<?php
try {
    $pdo = new PDO('pgsql:host=127.0.0.1;port=5432;dbname=postgres', 'postgres', '123456789');
    $pdo->exec("CREATE DATABASE premier_shop;");
    echo 'Database "premier_shop" created successfully.' . PHP_EOL;
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
