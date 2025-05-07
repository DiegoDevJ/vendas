<?php
session_start();
include 'includes/config.php';

header('Content-Type: application/json');

if (!isset($_POST['cart_id'])) {
    echo json_encode(['success' => false, 'message' => 'Item não especificado']);
    exit();
}

$cart_id = $_POST['cart_id'];
$stmt = $pdo->prepare("DELETE FROM cart WHERE id = ?");
$stmt->execute([$cart_id]);

echo json_encode(['success' => true]);