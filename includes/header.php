<?php
include dirname(__DIR__) . '/includes/config.php';
session_start();

// Buscar logo do banco de dados
$stmt = $pdo->query("SELECT logo FROM settings LIMIT 1");
$settings = $stmt->fetch(PDO::FETCH_ASSOC);
$logo = $settings['logo'] ? "/images/{$settings['logo']}" : null;

// Contar itens no carrinho
$session_id = session_id();
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cart WHERE session_id = ?");
$stmt->execute([$session_id]);
$cart_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loja Virtual</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header>
        <?php if ($logo): ?>
            <img src="<?php echo htmlspecialchars($logo); ?>" alt="Logo da Loja">
        <?php else: ?>
            <h1>Loja Virtual</h1>
        <?php endif; ?>
        <div class="cart-icon" onclick="toggleCart()">
            🛒 <span><?php echo $cart_count; ?></span>
        </div>
    </header>
    <main>