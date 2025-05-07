<?php
include 'includes/config.php';
include 'includes/header.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT * FROM products";
$params = [];

if ($search) {
    $query .= " WHERE name LIKE ?";
    $params[] = "%$search%";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="search-container">
    <form method="GET">
        <input type="text" name="search" placeholder="Buscar produtos..." value="<?php echo htmlspecialchars($search); ?>">
    </form>
</div>

<div class="product-grid">
    <?php if (empty($products)): ?>
        <p>Nenhum produto encontrado.</p>
    <?php else: ?>
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <span class="product-cart-icon" onclick="addToCart(<?php echo $product['id']; ?>)">🛒</span>
                <a href="/product.php?id=<?php echo $product['id']; ?>">
                    <img src="/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p>R$ <?php echo number_format($product['price'], 2, ',', '.'); ?></p>
                    </div>
                </a>
                <div class="product-actions">
                    <button onclick="addToCart(<?php echo $product['id']; ?>)">Comprar</button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>