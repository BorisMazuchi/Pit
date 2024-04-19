<?php
session_start();

$mensagem = "";
$servername = "localhost";
$username = "root";
$password = "soneto2005";
$dbname = "pit";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST["submit"])) {
    $problema_id = $_POST["problema"];
    $bairro_id = $_POST["bairro"];
    $cep = $_POST["cep"];
    $descricao = $_POST["descricao"];
    $referencia = $_POST["referencia"] ?? "";  

    $foto = null;
    if (isset($_FILES["foto"]["tmp_name"]) && !empty($_FILES["foto"]["tmp_name"])) {
        $foto = file_get_contents($_FILES["foto"]["tmp_name"]);
    }

    // Obtém o ID do usuário da sessão
    $usuario_id = $_SESSION['usuario']['Cpf'];

    // Insere a denúncia
    $stmt = $conn->prepare("INSERT INTO denuncias (problema_id, bairro_id, cep, descricao, referencia, foto, data) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiisss", $problema_id, $bairro_id, $cep, $descricao, $referencia, $foto);
    
    if ($stmt->execute()) {
        // Obtém o ID da denúncia inserida
        $denuncia_id = $conn->insert_id;
    
        // Insere os dados na tabela denuncias_usuarios para relacionar usuário e denúncia
        $stmtDenunciasUsuarios = $conn->prepare("INSERT INTO denuncias_usuarios (id_usuario, id_denuncia) VALUES (?, ?)");
        $stmtDenunciasUsuarios->bind_param("si", $usuario_id, $denuncia_id);
        $stmtDenunciasUsuarios->execute();
    
        $mensagem = "Denúncia registrada com sucesso!";
    } else {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "Erro!",
                text: "Ocorreu um erro ao registrar a denúncia: ' . $stmt->error . '",
                confirmButtonText: "Fechar"
            });
        </script>';
    }
}

$problemas_query = "SELECT id, nome FROM problemas";
$problemas_result = $conn->query($problemas_query);

$bairros_query = "SELECT id, nome FROM bairros";
$bairros_result = $conn->query($bairros_query);
?>
 

<!DOCTYPE html>
<html>
<head>
    <title>Cadastro Denúncia</title>
    <script src="cadastro_problema.js"></script>
    <link rel="stylesheet" href="assets\css\cadastro_problema_style.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="assets/select2-4.0.7/select2-4.0.7/dist/js/select2.js"></script>
    <link rel="stylesheet" href="assets/select2-4.0.7/select2-4.0.7/dist/css/select2.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
</head>
<body>
<header>
    <a href="pagina_inicial.php">
    <div class="logo">
        <img src="assets/imagens/engrenagem.png" alt="Logo">
    </div>
    </a>
    <div class="our">
    </div>
    <style>
        /* Adicione os estilos CSS para a barra de rolagem */
        /* Estilos para a barra de rolagem do navegador */
        /* Barras verticais */
        ::-webkit-scrollbar {
            width: 10px;
            opacity: 0; /* Oculta a barra de rolagem por padrão */
            transition: opacity 0.2s; /* Adiciona uma transição suave para a opacidade */
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1; /* Cor de fundo da trilha da barra de rolagem */
        }

        ::-webkit-scrollbar-thumb {
            background: #8A2BE2; /* Cor roxa mais escura e translúcida */
        }

        /* Exibe a barra de rolagem quando o mouse estiver próximo */
        ::-webkit-scrollbar-thumb:hover {
            opacity: 1; /* Torna a barra de rolagem visível quando o mouse estiver próximo */
        }
    </style>
</header>
<div class="container">
    <h1>Denúncia</h1>
    <form method="post" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="problema">Problema:</label>
            <select id="problema" name="problema" class="select2" required>
                <?php
                while ($problema = $problemas_result->fetch_assoc()) {
                    echo "<option value='" . $problema['id'] . "'>" . $problema['nome'] . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="bairro">Bairro:</label>
            <select id="bairro" name="bairro" class="select2" required>
                <?php
                while ($bairro = $bairros_result->fetch_assoc()) {
                    echo "<option value='" . $bairro['id'] . "'>" . $bairro['nome'] . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="cep">CEP:</label>
            <input type="text" id="cep" name="cep" required placeholder="00000-000" pattern="[0-9]{5}-[0-9]{3}">
        </div>
        <div class="form-group">
            <label for="descricao">Descrição:</label>
            <textarea id="descricao" name="descricao" rows="4" cols="50" required></textarea>
        </div>
        <div class="form-group">
            <label for="referencia">Ponto de Referência:</label>
            <input type="text" id="referencia" name="referencia">
        </div>
        <div class="form-group">
            <label for="foto">Foto:</label>
            <input type="file" id="foto" name="foto" accept="image/*">
        </div>
        <input type="submit" id="submit" name="submit" value="Denunciar">
    </form>

    <?php
    if (!empty($mensagem)) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: '$mensagem',
                confirmButtonText: 'Fechar'
            });
        </script>";
    }
    ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicialize os campos de seleção com a Select2
            $('.select2').select2();
        });
    </script>
</body>
</html>
