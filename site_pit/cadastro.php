<?php
header('Content-Type: text/html; charset=utf-8');
$erro = "";

if (isset($_POST['enviar'])) {
    $banco = new PDO("mysql:host=localhost;dbname=pit", "root", "soneto2005");
    $Email = $_POST['email'];
    $senha = $_POST['senha'];
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $cep = $_POST['cep'];

    // Verificar se o CPF já existe na base de dados
    $consultaCPF = $banco->prepare("SELECT * FROM usuarios WHERE Cpf = ?");
    $consultaCPF->execute([$cpf]);
    $existeCPF = $consultaCPF->fetch();

    // Verificar se o Email já existe na base de dados
    $consultaEmail = $banco->prepare("SELECT * FROM usuarios WHERE Email = ?");
    $consultaEmail->execute([$Email]);
    $existeEmail = $consultaEmail->fetch();

    if ($existeCPF) {
        echo "<script>alert('CPF já cadastrado. Por favor, insira um CPF diferente.');</script>";
    } else if ($existeEmail) {
        echo "<script>alert('Email já cadastrado. Por favor, insira um Email diferente.');</script>";
    } else {
        // Se tanto o CPF quanto o Email não existem na base de dados, realizar a inserção
        $insert = $banco->prepare("INSERT INTO usuarios(Email, Senha, Nome, Cpf, Cep) VALUES(?,?,?,?,?)");
        $insert->execute([$Email, $senha, $nome, $cpf, $cep]);
        header("Location: landing_page.html");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <style>
        body {
            background-color: #fff9ed;
            margin: 0;
            padding: 0;
            height: 100vh;
            /* Adicione esta linha */
            display: flex;
            /* Adicione esta linha */
            justify-content: center;
            /* Adicione esta linha */
            align-items: center;
            /* Adicione esta linha */
        }

        .login {
            width: 400px;
            /* Adicione esta linha */
            padding: 20px;
            /* Adicione esta linha */
            background-color: #fff;
            /* Adicione esta linha */
            border-radius: 5px;
            /* Adicione esta linha */
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            /* Adicione esta linha */
        }

        .ButtonLogar {
            background-color: #8A2BE2;
            color: #fff;
        }

        @media (max-width: 600px) {
            .login {
                width: 90%;
            }
        }
    </style>
</head>

<body>
    <div class="login">
        <h1 class="mb-5">Cadastre-se</h1>
        <form class="" action="" method="POST">
            <label for="">Email</label>
            <input class="form-control mb-3" type="email" name="email" required>
            <label for="">Senha</label>
            <input class="form-control mb-3" type="password" name="senha" required>
            <label for="">Nome</label>
            <input class="form-control mb-3" type="text" name="nome" required>
            <label for="">CPF</label>
            <input class="form-control mb-3" type="text" name="cpf" required>
            <label for="">CEP</label>
            <input class="form-control mb-3" type="text" name="cep" required>
            <input class="ButtonLogar form-control" type="submit" value="Cadastre-se" name="enviar">
        </form>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
</body>

</html>