<?php
// Conexão com o banco de dados (substitua as credenciais com as suas)
$servername = "localhost";
$username = "root";
$password = "soneto2005";
$dbname = "pit";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Gráfico 1: Total de Problemas no Ano Agrupado por Mês
$queryTotalProblemasAno = "SELECT YEAR(data) AS ano, MONTH(data) AS mes, COUNT(*) as total
                           FROM denuncias
                           WHERE YEAR(data) = YEAR(CURDATE())
                           GROUP BY ano, mes";
$resultTotalProblemasAno = $conn->query($queryTotalProblemasAno);

if (!$resultTotalProblemasAno) {
    die("Erro na consulta SQL: " . $conn->error);
}

$dataTotalProblemasAno = array();
while ($row = $resultTotalProblemasAno->fetch_assoc()) {
    $dataTotalProblemasAno[] = array(
        "ano" => $row["ano"],
        "mes" => $row["mes"],
        "total" => $row["total"]
    );
}

// Gráfico 2: Número de Problemas do Último Mês por Bairro
$queryProblemasPorBairro = "SELECT b.id AS bairro_id, b.nome AS nome_bairro, COUNT(d.id) AS total
                          FROM bairros b
                          LEFT JOIN denuncias d ON b.id = d.bairro_id
                          WHERE DATE_SUB(CURDATE(), INTERVAL 1 MONTH) <= d.data
                          GROUP BY b.id, b.nome";
$resultProblemasPorBairro = $conn->query($queryProblemasPorBairro);

if (!$resultProblemasPorBairro) {
    die("Erro na consulta SQL: " . $conn->error);
}

$dataProblemasPorBairro = array();
while ($row = $resultProblemasPorBairro->fetch_assoc()) {
    $dataProblemasPorBairro[] = array(
        "bairro_id" => $row["bairro_id"],
        "nome_bairro" => $row["nome_bairro"],
        "total" => $row["total"]
    );
}

// Gráfico 3: Quantidade de Diferentes Tipos de Problemas
$queryTiposProblemas = "SELECT problema_id, COUNT(*) as total FROM denuncias GROUP BY problema_id";
$resultTiposProblemas = $conn->query($queryTiposProblemas);

if (!$resultTiposProblemas) {
    die("Erro na consulta SQL: " . $conn->error);
}

$dataTiposProblemas = array();
while ($row = $resultTiposProblemas->fetch_assoc()) {
    $dataTiposProblemas[] = array(
        "problema_id" => $row["problema_id"],
        "total" => $row["total"]
    );
}

// Consulta para obter os nomes dos tipos de problemas
$queryNomesProblemas = "SELECT id, nome FROM problemas";
$resultNomesProblemas = $conn->query($queryNomesProblemas);

if (!$resultNomesProblemas) {
    die("Erro na consulta SQL: " . $conn->error);
}

$nomesProblemas = array();
while ($row = $resultNomesProblemas->fetch_assoc()) {
    $nomesProblemas[$row["id"]] = $row["nome"];
}
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" type="text/css" href="assets\css\acesso_interno_style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <header class="header">
        <h1>Acesso Interno</h1>
    </header>
    <div class="center-container">
        <div class="chart-container chart-margin chart-1">
            <!-- Gráfico 1: Total de Problemas no Ano Agrupado por Mês -->
            <canvas id="graficoTotalProblemas"></canvas>
        </div>
        <div class="chart-container chart-margin">
            <!-- Gráfico 2: Número de Problemas do Último Mês por Bairro -->
            <canvas id="graficoProblemasPorBairro"></canvas>
        </div>
        <div class="chart-container chart-margin">
            <!-- Gráfico 3: Quantidade de Diferentes Tipos de Problemas -->
            <canvas id="graficoTiposProblemas"></canvas>
        </div>
    </div>
</body>
<script>
    // Gráfico 1: Total de Problemas no Ano Agrupado por Mês
    var ctx1 = document.getElementById('graficoTotalProblemas').getContext('2d');
    var totalProblemasAnoData = <?php echo json_encode($dataTotalProblemasAno); ?>;
    var meses = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
    var dataLabels = totalProblemasAnoData.map(function (entry) {
        return meses[entry.mes - 1] + ' ' + entry.ano;
    });
    var dataValues = totalProblemasAnoData.map(function (entry) {
        return entry.total;
    });

    var chartTotalProblemasAno = new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: dataLabels,
            datasets: [{
                label: 'Total de Problemas no Ano Agrupado por Mês',
                data: dataValues,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    precision: 0, // Define a precisão para zero casas decimais
                }
            }
        }
    });

    // Gráfico 2: Número de Problemas do Último Mês por Bairro
    var ctx2 = document.getElementById('graficoProblemasPorBairro').getContext('2d');
    var problemasPorBairroData = <?php echo json_encode(array_column($dataProblemasPorBairro, 'total')); ?>;
    problemasPorBairroData = problemasPorBairroData.map(Math.round); // Arredonda todos os valores para números inteiros
    var nomesBairro = <?php echo json_encode(array_column($dataProblemasPorBairro, 'nome_bairro')); ?>;
    var chartProblemasPorBairro = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: nomesBairro,
            datasets: [{
                label: 'Problemas no Último Mês por Bairro',
                data: problemasPorBairroData,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    precision: 0, // Define a precisão para zero casas decimais
                }
            }
        }
    });

    // Gráfico 3: Quantidade de Diferentes Tipos de Problemas
    var ctx3 = document.getElementById('graficoTiposProblemas').getContext('2d');
    var tiposProblemasData = <?php echo json_encode(array_column($dataTiposProblemas, 'total')); ?>;
    tiposProblemasData = tiposProblemasData.map(Math.round); // Arredonda todos os valores para números inteiros

    // Mapeia o problema_id para o nome do problema
    var problemaIdToNome = <?php echo json_encode($nomesProblemas); ?>;
    var tipoProblemaIds = <?php echo json_encode(array_column($dataTiposProblemas, 'problema_id')); ?>;
    var tipoProblemaNomes = tipoProblemaIds.map(function (id) {
        return problemaIdToNome[id];
    });

    var chartTiposProblemas = new Chart(ctx3, {
        type: 'bar',
        data: {
            labels: tipoProblemaNomes, // Use nomes dos tipos de problemas como rótulos
            datasets: [{
                label: 'Quantidade de Diferentes Tipos de Problemas',
                data: tiposProblemasData,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            title: {
                title: {
                    display: true,
                    text: 'Quantidade de Diferentes Tipos de Problemas',
                    fontSize: 16
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    precision: 0, // Define a precisão para zero casas decimais
                }
            },
            plugins: {
                legend: {
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            var label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.parsed.y;
                            return label;
                        }
                    }
                }
            }
        }
    });
</script>

</html>