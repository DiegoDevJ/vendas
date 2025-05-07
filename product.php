<?php
include 'includes/config.php';
include 'includes/header.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$product_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: index.php');
    exit();
}
?>

<div class="product-details">
    <img src="/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
    <h2><?php echo htmlspecialchars($product['name']); ?></h2>
    <p><strong>Preço:</strong> R$ <?php echo number_format($product['price'], 2, ',', '.'); ?></p>
    <p><strong>Descrição:</strong> <?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
    <div class="product-actions">
        <button onclick="addToCart(<?php echo $product['id']; ?>)">Comprar</button>
        <span class="product-cart-icon" onclick="addToCart(<?php echo $product['id']; ?>)">🛒</span>
    </div>
</div>

<?php include 'includes/footer.php'; ?>