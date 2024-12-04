<?php
$conn = new mysqli('localhost', 'root', '', 'docesebytes');

// Adicionar item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['adicionar_item'])) {
    $nome = $_POST['nome'];
    $preco = $_POST['preco'];
    $stmt = $conn->prepare("INSERT INTO itens (nome, preco) VALUES (?, ?)");
    $stmt->bind_param("sd", $nome, $preco);
    $stmt->execute();
    $stmt->close();
}

// Alterar item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['alterar_item'])) {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $preco = $_POST['preco'];
    $stmt = $conn->prepare("UPDATE itens SET nome = ?, preco = ? WHERE id = ?");
    $stmt->bind_param("sdi", $nome, $preco, $id);
    $stmt->execute();
    $stmt->close();
}

// Listar itens somente se o botão for clicado
$itens = null;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['exibir_itens'])) {
    $itens = $conn->query("SELECT * FROM itens");
}

// Controle de exibição
$modo = isset($_GET['modo']) ? $_GET['modo'] : 'listar';
$id = isset($_GET['id']) ? $_GET['id'] : null;
$itemAtual = null;

if ($modo === 'alterar') {
    $stmt = $conn->prepare("SELECT * FROM itens WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $itemAtual = $resultado->fetch_assoc();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Itens</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <button class="menu-btn" onclick="toggleMenu()">Menu</button>

    <div class="menu">
        <button onclick="history.go(-1)" style="background-color: #9e1fe0; color: white; border: none; padding: 10px 20px; font-size: 14px; border-radius: 5px; cursor: pointer;">
            Voltar
        </button>
    </div>

    <h1>Gerenciamento de Itens</h1>
    <?php if ($modo === 'listar'): ?>
        <!-- Formulário de Cadastro -->
        <h2>Cadastro de Item</h2>
        <form method="POST">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required>
            <label for="preco">Preço:</label>
            <input type="number" step="0.01" id="preco" name="preco" required>
            <button type="submit" name="adicionar_item" style="background-color: #9e1fe0; color: white; border: none; padding: 10px 20px; font-size: 14px; border-radius: 5px; cursor: pointer;">Cadastrar</button>
        </form>

        <!-- Botão para listar itens -->
        <h2>Gerenciar Itens</h2>
        <form method="POST">
            <button type="submit" name="exibir_itens" style="background-color: #9e1fe0; color: white; border: none; padding: 10px 20px; font-size: 14px; border-radius: 5px; cursor: pointer;">Exibir Itens</button>
            <button type="button" onclick="ocultarItens()" style="background-color: #9e1fe0; color: white; border: none; padding: 10px 20px; font-size: 14px; border-radius: 5px; cursor: pointer;">Ocultar</button>
        </form>

        <?php if ($itens): ?>
            <!-- Lista de Itens -->
            <h2>Itens Cadastrados</h2>
            <table id="tabelaItens">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Preço</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = $itens->fetch_assoc()): ?>
                        <tr>
                            <td><?= $item['id'] ?></td>
                            <td><?= $item['nome'] ?></td>
                            <td>R$ <?= number_format($item['preco'], 2, ',', '.') ?></td>
                            <td>
                                <a href="?modo=alterar&id=<?= $item['id'] ?>">Alterar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php elseif ($modo === 'alterar' && $itemAtual): ?>
        <!-- Formulário de Alteração -->
        <h2>Alterar Item</h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?= $itemAtual['id'] ?>">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?= $itemAtual['nome'] ?>" required>
            <label for="preco">Preço:</label>
            <input type="number" step="0.01" id="preco" name="preco" value="<?= $itemAtual['preco'] ?>" required>
            <button type="submit" name="alterar_item" style="background-color: #9e1fe0; color: white; border: none; padding: 10px 20px; font-size: 14px; border-radius: 5px; cursor: pointer;">Salvar Alterações</button>
        </form>
        <a href="?modo=listar">Voltar</a>
    <?php else: ?>
        <p>Alteração Realizada com Sucesso</p>
        <a href="?modo=listar">Voltar</a>
    <?php endif; ?>

    <script>
        function toggleMenu() {
            const menu = document.querySelector('.menu');
            menu.style.display = menu.style.display === 'none' || menu.style.display === '' ? 'block' : 'none';
        }

        function ocultarItens() {
            const tabela = document.getElementById('tabelaItens');
            tabela.style.display = 'none'; // Esconde a tabela
        }
    </script>
</body>
</html>
