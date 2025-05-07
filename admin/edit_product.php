<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: index.php?page=products');
    exit();
}

$product_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: index.php?page=products');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $image = $product['image'];

    if (isset($_FILES['image']) && $_FILES['image']['name']) {
        $image = $_FILES['image']['name'];
        $target = "../images/" . basename($image);
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $error = "Erro ao fazer upload da imagem.";
        }
    }

    if (!isset($error)) {
        $stmt = $pdo->prepare("UPDATE products SET name = ?, price = ?, image = ?, description = ? WHERE id = ?");
        $stmt->execute([$name, $price, $image, $description, $product_id]);
        header('Location: index.php?page=products');
        exit();
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="admin-container">
    <div class="admin-section">
        <h2>Editar Produto</h2>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form class="admin-form" method="POST" enctype="multipart/form-data">
            <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            <input type="number" name="price" step="0.01" value="<?php echo $product['price']; ?>" required>
            <textarea name="description" rows="5" required><?php echo htmlspecialchars($product['description']); ?></textarea>
            <label>Imagem Atual:</label>
            <img src="../images/<?php echo htmlspecialchars($product['image']); ?>" alt="Imagem Atual" style="max-width: 200px; margin: 10px 0;">
            <input type="file" name="image" accept="image/*">
            <button type="submit">Salvar</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
