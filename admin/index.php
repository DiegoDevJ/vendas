<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$page = isset($_GET['page']) ? $_GET['page'] : 'settings';
?>

<?php include '../includes/header.php'; ?>

<div class="admin-container">
    <div class="admin-nav">
        <a href="index.php?page=settings" class="<?php echo $page === 'settings' ? 'active' : ''; ?>">Configurações</a>
        <a href="index.php?page=products" class="<?php echo $page === 'products' ? 'active' : ''; ?>">Produtos</a>
        <a href="logout.php">Sair</a>
    </div>

    <?php if ($page === 'settings'): ?>
        <div class="admin-section">
            <h2>Configurações do Site</h2>
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['logo'])) {
                $logo = $_FILES['logo']['name'];
                $target = "../images/" . basename($logo);
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $target)) {
                    $stmt = $pdo->prepare("UPDATE settings SET logo = ? WHERE id = 1");
                    $stmt->execute([$logo]);
                    echo '<p style="color: green;">Logo atualizada com sucesso!</p>';
                } else {
                    echo '<p style="color: red;">Erro ao fazer upload da logo.</p>';
                }
            }

            $stmt = $pdo->query("SELECT logo FROM settings LIMIT 1");
            $settings = $stmt->fetch(PDO::FETCH_ASSOC);
            ?>
            <form class="admin-form" method="POST" enctype="multipart/form-data">
                <label>Logo da Loja:</label>
                <?php if ($settings['logo']): ?>
                    <img src="/images/<?php echo htmlspecialchars($settings['logo']); ?>" alt="Logo Atual" style="max-width: 200px; margin: 10px 0;">
                <?php endif; ?>
                <input type="file" name="logo" accept="image/*" required>
                <button type="submit">Atualizar Logo</button>
            </form>
        </div>
    <?php elseif ($page === 'products'): ?>
        <div class="admin-section">
            <h2>Produtos</h2>
            <form class="admin-form" method="POST" action="add_product.php" enctype="multipart/form-data">
                <input type="text" name="name" placeholder="Nome do Produto" required>
                <input type="number" name="price" step="0.01" placeholder="Preço" required>
                <textarea name="description" placeholder="Descrição do Produto" rows="5" required></textarea>
                <input type="file" name="image" accept="image/*" required>
                <button type="submit">Adicionar Produto</button>
            </form>

            <h3>Produtos Cadastrados</h3>
            <?php
            $stmt = $pdo->query("SELECT * FROM products");
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <table class="product-table">
                <thead>
                    <tr>
                        <th>Imagem</th>
                        <th>Nome</th>
                        <th>Preço</th>
                        <th>Descrição</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><img src="/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>"></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td>R$ <?php echo number_format($product['price'], 2, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars(substr($product['description'], 0, 50)) . '...'; ?></td>
                            <td>
                                <button class="edit" onclick="location.href='edit_product.php?id=<?php echo $product['id']; ?>'">Editar</button>
                                <button class="delete" onclick="if(confirm('Tem certeza que deseja excluir?')) location.href='delete_product.php?id=<?php echo $product['id']; ?>'">Excluir</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
