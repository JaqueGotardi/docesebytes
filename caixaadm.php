<?php
$conn = new mysqli('localhost', 'root', '', 'docesebytes');

// Alterar Observações
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['alterar_comanda'])) {
    $comanda_id = $_POST['comanda_id'];
    $observacoes = $_POST['observacoes'];

    $stmt = $conn->prepare("UPDATE comandas SET observacoes = ? WHERE id = ?");
    $stmt->bind_param("si", $observacoes, $comanda_id);
    $stmt->execute();
    $stmt->close();
}

// Obter lista de comandas
$comandas = $conn->query("SELECT id FROM comandas");
$selected_comanda = null;
$comanda_dados = null;

// Consultar dados da comanda selecionada
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ver_comanda'])) {
    $comanda_id = $_POST['comanda_id'];
    $selected_comanda = $comanda_id;

    $query = $conn->prepare("
        SELECT c.id AS comanda_id, c.observacoes, c.status, 
               GROUP_CONCAT(i.nome SEPARATOR ', ') AS itens, 
               SUM(i.preco) AS total 
        FROM comandas c
        LEFT JOIN itens_comandas ci ON c.id = ci.comanda_id
        LEFT JOIN itens i ON ci.item_id = i.id
        WHERE c.id = ?
        GROUP BY c.id
    ");
    $query->bind_param("i", $comanda_id);
    $query->execute();
    $result = $query->get_result();
    $comanda_dados = $result->fetch_assoc();
    $query->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Comandas</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        .form-container1, .info-container1 {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            max-width: 600px;
        }
        select, textarea, input, button {
            padding: 10px;
            font-size: 14px;
            border-radius: 5px;
            border: 1px solid #ddd;
            margin-bottom: 10px;
            display: block;
        }
        button {
            background-color: #9e1fe0;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #7c1ac6;
        }
        .info-container1 table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #9e1fe0;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <button class="menu-btn" onclick="toggleMenu()">Menu</button>
    <div class="menu">
        <ul>
		    <li><a href="index.html">Página Inicial</a></li>
			<li><a href="comandas.php">Comandas</a></li>
			<li><a href="itens1.php">Cadastrar Itens</a></li>
			<li><a href="cadastrar.php">Cadastrar Funcionários</a></li>
			<li><a href="caixaadm.php">Caixa</a></li>
			<li><a href="login.php">Sair</a></li>
        </ul>
    </div>

    <h1>Gerenciamento de Comandas</h1>

    <!-- Seleção de comanda -->
    <div class="form-container1">
        <form method="POST">
            <label for="comanda_id">Selecione uma comanda:</label>
            <select name="comanda_id" id="comanda_id" required>
                <option value="">Selecione</option>
                <?php while ($comanda = $comandas->fetch_assoc()) : ?>
                    <option value="<?= $comanda['id'] ?>" <?= $selected_comanda == $comanda['id'] ? 'selected' : '' ?>>
                        Comanda #<?= $comanda['id'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit" name="ver_comanda">Ver Comanda</button>
        </form>
    </div>

    <!-- Informações da comanda -->
    <?php if ($comanda_dados): ?>
        <div class="info-container1">
            <h2>Detalhes da Comanda #<?= $comanda_dados['comanda_id'] ?></h2>
            <table>
                <tr>
                    <th>Itens</th>
                    <td><?= $comanda_dados['itens'] ?: 'Nenhum item' ?></td>
                </tr>
                <tr>
                    <th>Total</th>
                    <td>R$ <?= number_format($comanda_dados['total'] ?: 0, 2, ',', '.') ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td><?= $comanda_dados['status'] ?></td>
                </tr>
                <tr>
                    <th>Observações</th>
                    <td><?= $comanda_dados['observacoes'] ?></td>
                </tr>
            </table>

            <!-- Formulário para Alterar Observações -->
            <form method="POST">
                <input type="hidden" name="comanda_id" value="<?= $comanda_dados['comanda_id'] ?>">
                <label for="observacoes">Alterar Observações:</label>
                <textarea id="observacoes" name="observacoes" rows="3"><?= $comanda_dados['observacoes'] ?></textarea>
                <button type="submit" name="alterar_comanda">Alterar</button>
				<a href="metodopagadm.html">FINALIZAR VENDA</a>

            </form>
        </div>
    <?php endif; ?>

    <script>
        function toggleMenu() {
            const menu = document.querySelector('.menu');
            menu.style.display = menu.style.display === 'none' || menu.style.display === '' ? 'block' : 'none';
        }
    </script>
</body>
</html>
