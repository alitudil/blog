<?php

$dbHost = 'localhost'; // Or your database host
$dbName = 'blogumum';
$dbUser = 'root'; // Replace with your database username
$dbPass = ''; // Replace with your database password

try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}