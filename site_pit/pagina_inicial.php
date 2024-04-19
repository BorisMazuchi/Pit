<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$banco = new PDO("mysql:host=localhost;dbname=pit", "root", "soneto2005");
$usuario = $_SESSION['usuario'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        // Removing association from the denuncias_usuarios table
        $deleteAssocSql = "DELETE FROM denuncias_usuarios WHERE id_denuncia = :id";
        $deleteAssocStmt = $banco->prepare($deleteAssocSql);
        $deleteAssocStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $deleteAssocStmt->execute();

        // Now delete the record from the denuncias table
        $deleteDenunciaSql = "DELETE FROM denuncias WHERE id = :id";
        $deleteDenunciaStmt = $banco->prepare($deleteDenunciaSql);
        $deleteDenunciaStmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($deleteDenunciaStmt->execute()) {
            // Deletion success message
            echo "<script>
                    Swal.fire('Excluído!', 'O registro foi excluído com sucesso.', 'success').then(() => {
                        location.reload();
                    });
                  </script>";
        } else {
            // Deletion failure message
            echo "<script>
                    Swal.fire('Erro!', 'Ocorreu um erro ao excluir o registro ou você não tem permissão.', 'error');
                  </script>";
        }
    } else {
        echo "ID do registro não especificado.";
    }
}

// ...
$sql = "SELECT denuncias.id, denuncias.problema_id, denuncias.cep, denuncias.descricao, denuncias.referencia, denuncias.data, bairros.nome AS nome_bairro FROM denuncias
        INNER JOIN bairros ON denuncias.bairro_id = bairros.id
        WHERE denuncias.id IN (SELECT id_denuncia FROM denuncias_usuarios WHERE id_usuario = :usuario_id)";
$resultado = $banco->prepare($sql);
$resultado->bindParam(':usuario_id', $usuario['Cpf'], PDO::PARAM_STR);
$resultado->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reclamações</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@9">
    <link rel="stylesheet" href="assets\css\inicial_style.css">
</head>
<body>
<header>
    <div class="logo">
        <a href="">
            <img src="assets/imagens/engrenagem.png" alt="Logo">
        </a>
    </div>
    <div class="user">
    <?php if ($usuario && isset($usuario['Nome'])) : ?>
            <p>Olá, <?php echo $usuario['Nome']; ?></p>
        <?php endif; ?>
    </div>
    <div class="header-content">
        <div class="our">
            <p><a href="cadastro_problema.php" style="text-decoration: none;">Cadastrar denúncia</a></p>
        </div>
    </div>
</header>
<div class="reclamacao-section">
        <form action="pagina_inicial.php" method="POST" id="delete-form">
        <h2>Excluir Registro</h2>
        <label for="id">ID do Registro a Excluir:</label>
        <input type="text" name="id" id="id" required>
        <input type="submit" value="Excluir">
    </form>

    <div>
        <h2>Reclamações Feitas</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>id</th>
                        <th>bairro</th>
                        <th>cep</th>
                        <th>descricao</th>
                        <th>referencia</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . $row['nome_bairro'] . "</td>"; 
                        echo "<td>" . $row['cep'] . "</td>";
                        echo "<td>" . $row['descricao'] . "</td>";
                        echo "<td>" . $row['referencia'] . "</td>";
                        echo "<td>" . date('d/m/Y H:i:s', strtotime($row['data'])) . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.getElementById('delete-form').addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Tem certeza?',
            text: 'Você está prestes a excluir este registro.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const id = document.getElementById('id').value;

                $.ajax({
                    url: 'pagina_inicial.php',
                    method: 'POST',
                    data: { id: id },
                    success: function(response) {
                        Swal.fire('Excluído!', 'O registro foi excluído com sucesso.', 'success').then(() => {
                            location.reload();
                        });
                    },
                    error: function() {
                        Swal.fire('Erro!', 'Ocorreu um erro ao excluir o registro.', 'error');
                    }
                });
            }
        });
    });
</script>
</body>
</html>
