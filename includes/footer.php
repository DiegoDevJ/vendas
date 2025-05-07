<?php
$session_id = session_id();
$stmt = $pdo->prepare("SELECT c.id, c.product_id, c.quantity, p.name, p.price, p.image 
                       FROM cart c 
                       JOIN products p ON c.product_id = p.id 
                       WHERE c.session_id = ?");
$stmt->execute([$session_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<div class="cart-modal">
    <button class="cart-close" onclick="toggleCart()">✕</button>
    <h2>Seu Carrinho</h2>
    <?php if (empty($cart_items)): ?>
        <p>Seu carrinho está vazio.</p>
    <?php else: ?>
        <?php foreach ($cart_items as $item): ?>
            <div class="cart-item">
                <img src="/images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                <div class="cart-item-info">
                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                    <p>R$ <?php echo number_format($item['price'] * $item['quantity'], 2, ',', '.'); ?></p>
                    <div class="quantity-control">
                        <button onclick="updateQuantity(<?php echo $item['id']; ?>, <?php echo $item['quantity'] - 1; ?>)">-</button>
                        <input type="number" value="<?php echo $item['quantity']; ?>" onchange="updateQuantity(<?php echo $item['id']; ?>, this.value)">
                        <button onclick="updateQuantity(<?php echo $item['id']; ?>, <?php echo $item['quantity'] + 1; ?>)">+</button>
                    </div>
                </div>
                <button onclick="removeFromCart(<?php echo $item['id']; ?>)">Remover</button>
            </div>
        <?php endforeach; ?>
        <div class="cart-total">
            Total: R$ <?php echo number_format($total, 2, ',', '.'); ?>
        </div>
        <a href="/cart.php"><button class="checkout">Finalizar Compra</button></a>
    <?php endif; ?>
</div>

    </main>
    <script src="/js/scripts.js"></script>
</body>
</html>