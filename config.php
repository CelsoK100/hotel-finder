<?php
// Conexão à base de dados
$liga = mysqli_connect('localhost', 'root', '');
if (!$liga) {
    echo "<h2>ERROR!!! Falha na ligação ao Servidor!</h2>";
    exit;
}
mysqli_select_db($liga, 'hotel_finder');

$_SESSION['sessao_id'] = session_id(); // Guarda o identificador da sessão
if (isset($_POST['email'])) {  // Certifica-te de que o campo 'username' existe
    $username = $_POST['email'];

    // Verifica se o utilizador existe na base de dados
    $sql = "SELECT * FROM login WHERE email = ?";
    $stmt = $liga->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Retorna um JSON com a verificação
    echo json_encode(['exists' => $result->num_rows > 0]);
}
?>