<?php
// Verifica se o formulário foi enviado
if (isset($_POST['cadastrar'])) {
    // Conectar ao banco de dados
    $conn = new mysqli('localhost', 'root', '', 'docesebytes');

    // Verifica a conexão
    if ($conn->connect_error) {
        die("Conexão falhou: " . $conn->connect_error);
    }

    // Recebe os dados do formulário
    $username = $_POST['username'];
    $password = $_POST['password']; // A senha será armazenada de forma segura
    $email = $_POST['email'];
    $nivel_acesso_id = $_POST['nivel_acesso_id'];

    // Hash da senha
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Inserir os dados na tabela 'usuarios'
    $stmt = $conn->prepare("INSERT INTO usuarios (username, password, email, nivel_acesso_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $username, $hashed_password, $email, $nivel_acesso_id);

    if ($stmt->execute()) {
        echo "<p>Usuário cadastrado com sucesso!</p>";
    } else {
        echo "<p>Erro ao cadastrar usuário: " . $stmt->error . "</p>";
    }

    // Fecha a conexão
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário</title>
    <link rel="stylesheet" href="styles.css"> <!-- Seu CSS aqui -->
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
     <div class="container">
     <h2>Cadastro de Usuário</h2>    
    <form action="cadastrar.php" method="POST">

        <input type="text" id="username" name="username" placeholder="Usuário" required><br><br>

        <input type="password" id="password" name="password" placeholder="Senha" required><br><br>

        <input type="email" id="email" name="email" placeholder="Email" required><br><br>

        <label for="nivel_acesso">Nível de Acesso:</label>
        <select name="nivel_acesso_id" id="nivel_acesso" required>
            <!-- As opções vão ser carregadas via PHP -->
            <?php
                // Conexão com o banco de dados para carregar os níveis de acesso
                $conn = new mysqli('localhost', 'root', '', 'docesebytes');
                if ($conn->connect_error) {
                    die("Conexão falhou: " . $conn->connect_error);
                }

                // Busca os níveis de acesso
                $result = $conn->query("SELECT id, nome FROM nivel_acesso");

                // Exibe as opções no select
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['id'] . "'>" . $row['nome'] . "</option>";
                }

                $conn->close();
            ?>
        </select><br><br>

        <button type="submit" name="cadastrar">Cadastrar</button>
    </form>
	
    <script>
        function toggleMenu() {
            const menu = document.querySelector('.menu');
            menu.style.display = menu.style.display === 'none' || menu.style.display === '' ? 'block' : 'none';
        }
    </script>
</body>
</html>
