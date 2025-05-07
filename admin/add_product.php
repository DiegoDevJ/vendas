<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $image = $_FILES['image']['name'];

    $target = "../images/" . basename($image);
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $stmt = $pdo->prepare("INSERT INTO products (name, price, image, description) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $price, $image, $description]);
        header('Location: index.php?page=products');
        exit();
    } else {
        $error = "Erro ao fazer upload da imagem.";
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="admin-container">
    <div class="admin-section">
        <h2>Adicionar Produto</h2>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form class="admin-form" method="POST" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Nome do Produto" required>
            <input type="number" name="price" step="0.01" placeholder="Preço" required>
            <textarea name="description" placeholder="Descrição do Produto" rows="5" required></textarea>
            <input type="file" name="image" accept="image/*" required>
            <button type="submit">Adicionar</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
