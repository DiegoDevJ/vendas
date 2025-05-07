<?php
session_start();
include 'includes/config.php';

header('Content-Type: application/json');

if (!isset($_POST['cart_id']) || !isset($_POST['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos']);
    exit();
}

$cart_id = $_POST['cart_id'];
$quantity = (int)$_POST['quantity'];

if ($quantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Quantidade inválida']);
    exit();
}

$stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
$stmt->execute([$quantity, $cart_id]);

echo json_encode(['success' => true]);