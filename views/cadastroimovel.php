<?php
require_once 'config.php';


// Captura os dados do formulário
$titulo = $_POST['titulo'];
$descricao = $_POST['descricao'];
$preco = $_POST['preco'];
$endereco = $_POST['endereco'];
$cidade = $_POST['cidade'];
$estado = $_POST['estado'];
$cep = $_POST['cep'];
$numero_quartos = $_POST['numero_quartos'];
$vagas_garagem = $_POST['vagas_garagem'];
$bairro = $_POST['bairro'];
$tipo_imovel = $_POST['tipo_imovel'];
$status = $_POST['status'];
$tipo_negociacao = $_POST['tipo_negociacao'];

// Insere os dados no banco de dados
$smtp = $conn->prepare("INSERT INTO imoveis (titulo, descricao, preco, endereco, cidade, estado, cep, usuario_id, numero_quartos, vagas_garagem, bairro, tipo_imovel, status, tipo_negociacao) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$smtp->bind_param("sssssssiiissss", $titulo, $descricao, $preco, $endereco, $cidade, $estado, $cep, $usuario_id, $numero_quartos, $vagas_garagem, $bairro, $tipo_imovel, $status, $tipo_negociacao);

if ($smtp->execute()) {
    echo "Imóvel cadastrado com sucesso!";
} else {
    echo "Erro na captura de dados: " . $smtp->error;
}

$smtp->close();
$conn->close();
?>
