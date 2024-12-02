<?php
session_start();
require_once 'config.php'; // Certifique-se de que a conexão com o banco de dados está estabelecida aqui

// Substitua pela sua chave da API do Google Maps
$googleMapsApiKey = 'SUA_CHAVE_API_GOOGLE_MAPS';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Início da query
    $query = "SELECT * FROM imoveis WHERE 1=1";

    // Condições de filtro
    if (!empty($_GET['preco_min'])) {
        $preco_min = intval($_GET['preco_min']);
        $query .= " AND preco >= $preco_min";
    }

    if (!empty($_GET['preco_max'])) {
        $preco_max = intval($_GET['preco_max']);
        $query .= " AND preco <= $preco_max";
    }

    if (!empty($_GET['bairro'])) {
        $bairro = $conn->real_escape_string($_GET['bairro']);
        $query .= " AND bairro = '$bairro'";
    }

    if (!empty($_GET['cidade'])) {
        $cidade = $conn->real_escape_string($_GET['cidade']);
        $query .= " AND cidade = '$cidade'";
    }

    if (!empty($_GET['estado'])) {
        $estado = $conn->real_escape_string($_GET['estado']);
        $query .= " AND estado = '$estado'";
    }

    if (!empty($_GET['tipo_negociacao'])) {
        $tipo_negociacao = $conn->real_escape_string($_GET['tipo_negociacao']);
        $query .= " AND tipo_negociacao = '$tipo_negociacao'";
    }

    if (!empty($_GET['tipo_imovel'])) {
        $tipo = $conn->real_escape_string($_GET['tipo_imovel']);
        $query .= " AND tipo_imovel = '$tipo'";
    }

    if (isset($_GET['numero_quartos']) && $_GET['numero_quartos'] !== "") {
        $numero_quartos = intval($_GET['numero_quartos']);
        if ($numero_quartos == 3) {
            $query .= " AND numero_quartos >= 3";
        } else {
            $query .= " AND numero_quartos = $numero_quartos";
        }
    }

    if (isset($_GET['vagas_garagem']) && $_GET['vagas_garagem'] !== "") {
        $garagem = intval($_GET['vagas_garagem']);
        if ($garagem == 3) {
            $query .= " AND vagas_garagem >= 3";
        } else {
            $query .= " AND vagas_garagem = $garagem";
        }
    }

    // Executa a query
    try {
        $result = $conn->query($query);

        // Exibe os resultados em HTML
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $bairro = htmlspecialchars($row["bairro"]);
                $cidade = htmlspecialchars($row["cidade"]);
                $estado = htmlspecialchars($row["estado"]);

                // Criar o link para o Google Maps com o endereço completo
                $endereco = urlencode("$bairro, $cidade, $estado");

                echo "<div class='imovel'>";
                echo "<p><strong>ID:</strong> " . htmlspecialchars($row["id"]) . "</p>";
                echo "<p><strong>Bairro:</strong> $bairro</p>";
                echo "<p><strong>Cidade:</strong> $cidade</p>";
                echo "<p><strong>Estado:</strong> $estado</p>";
                echo "<p><strong>Preço:</strong> R$ " . htmlspecialchars($row["preco"]) . "</p>";
                echo "<p><strong>Tipo de Negociação:</strong> " . htmlspecialchars($row["tipo_negociacao"]) . "</p>";
                echo "<p><strong>Tipo de Imóvel:</strong> " . htmlspecialchars($row["tipo_imovel"]) . "</p>";
                echo "<p><strong>Quartos:</strong> " . htmlspecialchars($row["numero_quartos"]) . "</p>";
                echo "<p><strong>Vagas de Garagem:</strong> " . htmlspecialchars($row["vagas_garagem"]) . "</p>";

                // Link para o Google Maps
                echo "<p><strong>Localização:</strong></p>";
                echo "<a href='https://www.google.com/maps?q=$endereco' target='_blank' class='link-mapa'>Ver no Google Maps</a>";
                echo "</div>";
            }
        } else {
            echo "<p>Nenhum imóvel encontrado.</p>";
        }
    } catch (mysqli_sql_exception $e) {
        echo "<p>Erro na consulta: " . $e->getMessage() . "</p>";
    }

    $conn->close();
    exit();
}
?>
