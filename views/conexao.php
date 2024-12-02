<?php
require_once 'config.php';

if (isset($_POST['acao'])) {
    $acao = $_POST['acao'];

    if ($acao === 'login') {
        // Processamento de Login
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        // Consulta para pegar os dados do usuário
        $query = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Verifica se o usuário foi encontrado
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verifica se a senha está correta
            if (password_verify($senha, $user['senha'])) {
                // Login bem-sucedido
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nome'];
                header("Location: buscarimoveis.html");
                exit();  // Interrompe o script após o redirecionamento
            } else {
                // Falha na senha
                header("Location: entrarforms.html?error=1");
                exit();  // Interrompe o script após o redirecionamento
            }
        } else {
            // Usuário não encontrado
            header("Location: entrarforms.html?error=2");
            exit();  // Interrompe o script após o redirecionamento
        }

        $stmt->close();

    } elseif ($acao === 'cadastro') {
        // Processamento de Cadastro
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $senha = $_POST['senha'];
        $telefone = $_POST['telefone'];
        $opcaoSelecionada = $_POST['opcoes'];

        // Verifica se o email já existe
        $stmt = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            // Caso o email já exista
            echo "<p>Este email já está em uso. Por favor, escolha outro.</p>";
        } else {
            // Cadastro de usuário com senha criptografada
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, telefone, tipo) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $nome, $email, $senhaHash, $telefone, $opcaoSelecionada);

            if ($stmt->execute()) {
                // Cadastro realizado com sucesso
                echo "<p>Cadastro realizado com sucesso! Agora você pode <a href='entrarforms.html'>entrar</a>.</p>";
            } else {
                // Erro no cadastro
                echo "<p>Erro na captura de dados: " . $stmt->error . "</p>";
            }
            $stmt->close();
        }
    }
}

$conn->close();
?>
