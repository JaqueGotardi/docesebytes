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


// Consultar Itens e Comandas
$itens = $conn->query("SELECT * FROM itens");
$comandas = $conn->query("
    SELECT c.id AS comanda_id, c.observacoes, c.status, 
           GROUP_CONCAT(i.nome SEPARATOR ', ') AS itens, 
           SUM(i.preco) AS total 
    FROM comandas c
    LEFT JOIN itens_comandas ci ON c.id = ci.comanda_id
    LEFT JOIN itens i ON ci.item_id = i.id
    GROUP BY c.id
");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Comandas</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function toggleComandas() {
            var comandasTable = document.getElementById('comandas-table');
            if (comandasTable.style.display === 'none') {
                comandasTable.style.display = 'table';
            } else {
                comandasTable.style.display = 'none';
            }
        }
    </script>
	 <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        .form-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        textarea, select, input {
            padding: 10px;
            font-size: 14px;
            border-radius: 5px;
            border: 1px solid #ddd;
            width: 300px;
        }
        button {
            background-color: #9e1fe0;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #7c1ac6;
        }
        /* Estilo da Tabela Dentro de um Quadrado */
        .table-container {
            width: 80%;
            margin-top: 20px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }
        table {
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
        /* Organizando os Botões ao Lado */
        .actions-form {
            margin-top: 10px;
            display: flex;
            gap: 10px;
            flex-direction: row;
        }
        .actions-form textarea, .actions-form select {
            width: 150px;
        }
        .form-container button, .actions-form button {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <button class="menu-btn" onclick="toggleMenu()">Menu</button>
  
    <div class="menu">
        <button onclick="history.go(-1)" style="background-color: #9e1fe0; color: white; border: none; padding: 10px 20px; font-size: 14px; border-radius: 5px; cursor: pointer;">
    Voltar
        </button>
    </div>
	
    <h1>Gerenciamento de Comandas</h1>
<!-- Botão para Exibir/Ocultar Comandas -->
<button onclick="toggleComandas()" 
        style="background-color: #9e1fe0; color: white; border: none; padding: 10px 20px; font-size: 14px; border-radius: 5px; cursor: pointer;">
    Exibir/ocultar Comandas
</button>

<!-- Lista de Comandas -->
<h2>Comandas Cadastradas</h2>
<div class="table-container">
<table id="comandas-table" style="display: none;">
    <thead>
        <tr>
            <th>ID</th>
            <th>Observações</th>
            <th>Itens</th>
            <th>Total</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($comanda = $comandas->fetch_assoc()) : ?>
        <tr>
            <td><?= $comanda['comanda_id'] ?></td>
            <td><?= $comanda['observacoes'] ?></td>
            <td><?= $comanda['itens'] ?: 'Nenhum item' ?></td>
            <td>R$ <?= number_format($comanda['total'] ?: 0, 2, ',', '.') ?></td>
            <td><?= $comanda['status'] ?></td>
            <td>
                <!-- Formulário para Alterar Comanda -->
                <form method="POST" style="margin-bottom: 10px;">
                    <input type="hidden" name="comanda_id" value="<?= $comanda['comanda_id'] ?>">
                    <label>Observações:</label>
                    <textarea id="observacoes" name="observacoes" rows="3" placeholder="Digite algo se necessário..."></textarea>
                    <button type="submit" name="alterar_comanda" style="background-color: #9e1fe0; color: white; border: none; padding: 10px 20px; font-size: 14px; border-radius: 5px; cursor: pointer;">Alterar</button>
                </form>

        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
 </div>
     <script>
        function toggleMenu() {
            const menu = document.querySelector('.menu');
            menu.style.display = menu.style.display === 'none' || menu.style.display === '' ? 'block' : 'none';
        }
    </script>
</body>
</html>
