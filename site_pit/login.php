<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
$resultado = "";

if (isset($_POST['enviar'])) {
    $banco = new PDO("mysql:host=localhost;dbname=pit", "root", "soneto2005");
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $tipo_login = $_POST['tipo_login'];

    $tabela = ($tipo_login == 'usuario') ? 'usuarios' : 'funcionarios';

    $sql = "SELECT * FROM $tabela WHERE Email = :email AND Senha = :senha"; // Corrigido para Senha (maiúsculo)
    $stmt = $banco->prepare($sql);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':senha', $senha, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC); // Obter os detalhes do usuário
        $_SESSION['usuario'] = $usuario; // Armazenar os detalhes do usuário na sessão

        if ($tipo_login == 'funcionario') {
            header("Location: acesso_interno.php");
            exit();
        } else {
            header("Location: pagina_inicial.php");
            exit();
        }
    } else {
        $resultado = "FALHA NO LOGIN";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <style>
        body {
            background-color: #fff9ed;
            margin: 0;
            padding: 0;
            height: 100%;
        }

        .ButtonLogar {
            background-color: #8A2BE2;
            color: #fff;
        }

        @media (max-width: 600px) {
            .card {
                width: 90%;
            }
        }
    </style>
</head>

<body>
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-lg" style="width: 25rem;">
            <div class="card-body">
                <h1 class="mb-5 text-center">Login</h1>
                <form class="" action="" method="POST">
                    <label for="">Email</label>
                    <input class="form-control mb-3" type="email" name="email">
                    <label for="">Senha</label>
                    <input class="form-control mb-3" type="password" name="senha">
                    <label for="">Tipo de Login</label>
                    <select class="form-control mb-3" name="tipo_login">
                        <option value="usuario">Usuário</option>
                        <option value="funcionario">Funcionário</option>
                    </select>
                    <input class="ButtonLogar form-control" type="submit" value="Logar" name="enviar">
                </form>
            </div>
        </div>
    </div>

    <script>
        function exibirPopup(resultado) {
            alert(resultado);
        }
        
        // Verifica se a variável $resultado não está vazia para exibir o pop-up
        <?php if (!empty($resultado)) : ?>
            exibirPopup('<?php echo $resultado; ?>');
        <?php endif; ?>
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
</body>

</html>
