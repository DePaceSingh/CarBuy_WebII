<?php
try {
    $pdo = new PDO('mysql:host=mysql;dbname=assignment1;charset=utf8', 'v.je', 'v.je');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>