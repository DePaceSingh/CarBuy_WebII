<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit;
}

$categoryId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
try {
    $stmt = $pdo->prepare("DELETE FROM category WHERE id = ?");
    $stmt->execute([$categoryId]);
    header("Location: adminCategories.php");
    exit;
} catch (PDOException $e) {
    die("Failed to delete category: " . $e->getMessage());
}
?>