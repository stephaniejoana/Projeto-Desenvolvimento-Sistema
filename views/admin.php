<?php
session_start();
require_once 'config.php';

$senhasecreta = "admin@123";

// Verifica a sessão para o login
if (!isset($_SESSION['logado']) && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['senha'])) {
    $senhadigt = $_POST['senha'];
    if ($senhadigt == $senhasecreta) {
        $_SESSION['logado'] = true;
    } else {
        echo "<h1>Senha Incorreta.</h1>";
    }
}

// Processa logout e redireciona para a página de login
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Executa a consulta de dados se o usuário estiver logado
if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
    $sql = "SELECT * FROM usuarios";
    $result = $conn->query($sql);

    if (!$result) {
        echo "<p>Erro na consulta ao banco de dados: " . $conn->error . "</p>";
    }
}

// Processamento da exclusão
if (isset($_POST['delete_id'])) {
    $deleteId = $_POST['delete_id'];
    $sqlDelete = "DELETE FROM usuarios WHERE id = ?";
    $stmtDelete = $conn->prepare($sqlDelete);
    $stmtDelete->bind_param("i", $deleteId);

    if ($stmtDelete->execute()) {
        echo "<p>Usuário excluído com sucesso.</p>";
        header("Refresh:0");
        exit;
    } else {
        echo "<p>Erro ao excluir usuário: " . $conn->error . "</p>";
    }
}

// Processamento da edição
if (isset($_POST['edit_id'])) {
    $editId = $_POST['edit_id'];
    $editNome = $_POST['nome'];
    $editEmail = $_POST['email'];
    $editTelefone = $_POST['telefone'];
    $editTipo = $_POST['tipo'];

    // Verificação de e-mail único
    $sqlCheckEmail = "SELECT id FROM usuarios WHERE email = ? AND id != ?";
    $stmtCheckEmail = $conn->prepare($sqlCheckEmail);
    $stmtCheckEmail->bind_param("si", $editEmail, $editId);
    $stmtCheckEmail->execute();
    $stmtCheckEmail->store_result();

    if ($stmtCheckEmail->num_rows > 0) {
        echo "<p>Erro: O e-mail já está em uso. Escolha outro e-mail.</p>";
    } else {
        $sqlEdit = "UPDATE usuarios SET nome = ?, email = ?, telefone = ?, tipo = ? WHERE id = ?";
        $stmtEdit = $conn->prepare($sqlEdit);
        $stmtEdit->bind_param("ssssi", $editNome, $editEmail, $editTelefone, $editTipo, $editId);

        if ($stmtEdit->execute()) {
            echo "<p>Usuário atualizado com sucesso.</p>";
            header("Refresh:0");
            exit;
        } else {
            echo "<p>Erro ao atualizar usuário: " . $conn->error . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
</head>
<body>
    <div class="container">
        <div class="form">
            <?php if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) : ?>
                <form method="post">
                    <div class="form-header">
                        <div class="title">
                            <h1>Administração - Inf. de dados por Tabela</h1>
                        </div>
                    </div>

                    <div class="input-box">
                        <label for="password">Senha</label>
                        <input id="password" type="password" name="senha" placeholder="Digite sua Senha" required>
                    </div>                  

                    <div class="continue-button">
                        <button type="submit">Buscar</button>
                    </div>
                </form>
            <?php else : ?>
                <div>
                    <form method="post" style="display:inline;">
                        <button type="submit" name="logout">Logout</button>
                    </form>
                    <button onclick="location.reload()">Atualizar Lista</button>
                    <?php if ($result && $result->num_rows > 0) : ?>
                        <h2>Dados</h2>
                        <ul>
                            <?php while ($row = $result->fetch_assoc()) : ?>
                                <li> 
                                    <form method="post">
                                        <input type="hidden" name="edit_id" value="<?php echo $row['id']; ?>">
                                        <label><strong>Nome: </strong></label>
                                        <input type="text" name="nome" value="<?php echo htmlspecialchars($row["nome"]); ?>" required><br>

                                        <label><strong>E-mail: </strong></label>
                                        <input type="email" name="email" value="<?php echo htmlspecialchars($row["email"]); ?>" required><br>

                                        <label><strong>Telefone: </strong></label>
                                        <input type="text" name="telefone" value="<?php echo htmlspecialchars($row["telefone"]); ?>" required><br>

                                        <label><strong>Tipo: </strong></label>
                                        <select name="tipo" required>
                                            <option value="comprador" <?php if ($row["tipo"] == "comprador") echo "selected"; ?>>Comprador</option>
                                            <option value="vendedor" <?php if ($row["tipo"] == "vendedor") echo "selected"; ?>>Vendedor</option>
                                            <option value="administrador" <?php if ($row["tipo"] == "administrador") echo "selected"; ?>>Administrador</option>
                                        </select><br>

                                        <button type="submit">Salvar Alterações</button>
                                    </form>

                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" onclick="return confirm('Tem certeza que deseja excluir este usuário?')">Excluir</button>
                                    </form>
                                </li>
                                <br>
                            <?php endwhile; ?>
                        </ul>
                    <?php else : ?>
                        <p>Nenhum dado encontrado!</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
