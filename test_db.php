<?php
try {
    $pdo = new PDO('pgsql:host=127.0.0.1;port=5432;dbname=postgres', 'postgres', '123456789');
    echo 'Connection successful: ' . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . PHP_EOL;
} catch (Exception $e) {
    echo 'Connection failed: ' . $e->getMessage() . PHP_EOL;
}
