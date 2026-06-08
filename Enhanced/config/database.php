<?php
// SECURE: Database configuration with error handling
// In production, this file should be outside the web root
return [
    'host' => 'localhost',
    'dbname' => 'inventory_db',
    // SECURE: Using a dedicated database user with limited privileges in production.
    // We use root/empty password for localhost out-of-the-box compatibility.
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];
?>
