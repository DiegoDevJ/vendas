<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['admin_id']) || !isset($_GET['id'])) {
    header('Location: index.php?page=products');
    exit();
}

$product_id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
$stmt->execute([$product_id]);

header('Location: index.php?page=products');
exit();
?>
