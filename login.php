<?php
// Variável para exibir erro de login, se houver
$erroLogin = "";

// Conexão com o banco de dados
$conn = new mysqli('localhost', 'root', '', 'docesebytes');

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verifica se o formulário foi enviado
if (isset($_POST['login'])) {
    // Recebe os dados do formulário
    $username = $_POST['usuario'];
    $password = $_POST['senha'];

    // Verifica se o usuário existe
    $sql = "SELECT * FROM usuarios WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Verifica se a senha está correta
        if (password_verify($password, $row['password'])) {
            // Salva o ID do usuário na sessão
            session_start();
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];

            // Redireciona para a página de acordo com o nível de acesso
            switch ($row['nivel_acesso_id']) {
                case 1:
                    header("Location: index.html"); // Administrador
                    break;
                case 2:
                    header("Location: iniciocoz.html"); // Cozinha
                    break;
                case 3:
                    header("Location: iniciocx.html"); // Caixa
                    break;
                case 4:
                    header("Location: inicioaten.html"); // Atendente
                    break;
                default:
                    $erroLogin = "Acesso não autorizado!";
            }
            exit();
        } else {
            $erroLogin = "Senha incorreta!";
        }
    } else {
        $erroLogin = "Usuário não encontrado!";
    }

    // Fecha a conexão
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Acesso</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <div class="logo-container">
        <img src="logoteste.png" alt="Logo do site" class="logo">
    </div>

    <div class="container">
        <form action="login.php" method="POST">
            <input type="text" name="usuario" placeholder="Usuário" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <input type="submit" name="login" value="Entrar">
        </form>
        <?php if ($erroLogin): ?>
            <p style="color: red;"><?php echo $erroLogin; ?></p>
        <?php endif; ?>
    </div>

</body>
</html>
