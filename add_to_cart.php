<?php
session_start();
include 'includes/config.php';

header('Content-Type: application/json');

if (!isset($_POST['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'Produto não especificado']);
    exit();
}

$product_id = $_POST['product_id'];
$session_id = session_id();

// Verificar se o produto já está no carrinho
$stmt = $pdo->prepare("SELECT * FROM cart WHERE session_id = ? AND product_id = ?");
$stmt->execute([$session_id, $product_id]);
$cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

if ($cart_item) {
    // Atualizar quantidade
    $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = ?");
    $stmt->execute([$cart_item['id']]);
} else {
    // Adicionar novo item
    $stmt = $pdo->prepare("INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, 1)");
    $stmt->execute([$session_id, $product_id]);
}

echo json_encode(['success' => true]);