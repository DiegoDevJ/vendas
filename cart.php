<?php
include 'includes/config.php';
include 'includes/header.php';

$session_id = session_id();

// Buscar itens no carrinho
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    $delivery_option = $_POST['delivery_option'];
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $cep = $_POST['cep'] ?? '';
    $city = $_POST['city'] ?? '';
    $neighborhood = $_POST['neighborhood'] ?? '';
    $street = $_POST['street'] ?? '';
    $number = $_POST['number'] ?? '';
    $complement = $_POST['complement'] ?? '';

    // Taxa de entrega fixa (já que bairro é texto livre)
    $delivery_fee = $delivery_option === 'delivery' ? 10.00 : 0; // Ajuste conforme necessário

    // Montar mensagem para o WhatsApp
    $message = "Olá! Gostaria de finalizar a compra:\n\n";
    $message .= "Itens:\n";
    foreach ($cart_items as $item) {
        $message .= "- {$item['name']} (x{$item['quantity']}): R$ " . number_format($item['price'] * $item['quantity'], 2, ',', '.') . "\n";
    }
    $message .= "\nTotal dos Itens: R$ " . number_format($total, 2, ',', '.') . "\n";
    if ($delivery_option === 'delivery') {
        $message .= "Taxa de Entrega: R$ " . number_format($delivery_fee, 2, ',', '.') . "\n";
        $message .= "Total com Entrega: R$ " . number_format($total + $delivery_fee, 2, ',', '.') . "\n";
        $message .= "\nEndereço de Entrega:\n";
        $message .= "Nome: $name\n";
        $message .= "Telefone: $phone\n";
        $message .= "CEP: $cep\n";
        $message .= "Cidade: $city\n";
        $message .= "Bairro: $neighborhood\n";
        $message .= "Rua/Avenida: $street\n";
        $message .= "Número: $number\n";
        $message .= "Complemento/Referência: $complement\n";
    } else {
        $message .= "Opção: Retirar no Local\n";
        $message .= "Total: R$ " . number_format($total, 2, ',', '.') . "\n";
    }
    $message .= "\nChave Pix para pagamento: sua-chave-pix-aqui";

    $message = urlencode($message);
    $whatsapp_url = "https://wa.me/5511999999999?text=$message"; // Substitua pelo número do WhatsApp
    header("Location: $whatsapp_url");
    exit();
}
?>

<div class="cart-modal open">
    <button class="cart-close" onclick="window.location.href='/index.php'">✕</button>
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

        <!-- Formulário de Finalização -->
        <form class="checkout-form" method="POST">
            <label>Opção de Entrega:</label>
            <select name="delivery_option" id="deliveryOption" onchange="toggleDeliveryForm()">
                <option value="pickup">Retirar no Local</option>
                <option value="delivery">Entrega por Mototáxi</option>
            </select>

            <div id="deliveryForm" class="hidden">
                <label>Nome Completo:</label>
                <input type="text" name="name" required>
                <label>Número de Telefone:</label>
                <input type="text" name="phone" required>
                <label>CEP:</label>
                `<input type="text" name="cep" required>
                <label>Estado - Cidade:</label>
                <input type="text" name="city" value="Santo Antônio de Jesus" readonly>
                <label>Bairro:</label>
                <input type="text" name="neighborhood" required>
                <label>Rua / Avenida:</label>
                <input type="text" name="street" required>
                <label>Número:</label>
                <input type="text" name="number" required>
                <label>Complemento / Referências:</label>
                <textarea name="complement"></textarea>
            </div>

            <button type="submit" name="checkout">Finalizar Compra</button>
        </form>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>