<?php
// Inicia a sessão
session_start();

// Inclui a configuração da base de dados
include 'config.php';

// Verifica se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);

    // Verifica se todos os campos foram preenchidos
    if (empty($username) || empty($password) || empty($email)) {
        echo "<script>alert('Todos os campos são obrigatórios.'); window.location.href = 'registo.html';</script>";
        exit();
    }

    // Verifica se o email é válido
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('O email fornecido é inválido.'); window.location.href = 'registo.html';</script>";
        exit();
    }

    // Verifica se o username ou email já existe na base de dados
    $sql_verificar = "SELECT * FROM login WHERE user = ? OR email = ?";
    $stmt_verificar = $liga->prepare($sql_verificar);
    $stmt_verificar->bind_param("ss", $username, $email);
    $stmt_verificar->execute();
    $resultado = $stmt_verificar->get_result();

    if ($resultado->num_rows > 0) {
        echo "<script>alert('Erro: Username ou email já existe.'); window.location.href = 'registo.html';</script>";
        exit();
    }

    // Insere o novo utilizador na tabela `login`
    $dt_time = date("Y-m-d H:i:s"); // Define a data e hora atuais para `dt_time`
    $sql_inserir = "INSERT INTO login (user, password, email, dt_time) VALUES (?, ?, ?, ?)";
    $stmt_inserir = $liga->prepare($sql_inserir);
    $stmt_inserir->bind_param("ssss", $username, $password, $email, $dt_time);

    if ($stmt_inserir->execute()) {
        // Chama o script para enviar o email de boas-vindas
        include 'enviar_email_registo.php';
        enviarEmail($email, $username);

        echo "<script>alert('Utilizador criado com sucesso! Verifique o seu email para mais detalhes.'); window.location.href = 'login.html';</script>";
    } else {
        echo "<script>alert('Erro ao criar o utilizador. Tente novamente.'); window.location.href = 'registo.html';</script>";
    }
}
?>
