<?php
include("conexao.php");
$mensagem = "";
if($_SERVER["REQUEST_METHOD"] === "POST"){
    $nome = filter_input(INPUT_POST, "nome", FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $senha = filter_input(INPUT_POST, "senha", FILTER_SANITIZE_SPECIAL_CHARS);

    $sql = "SELECT COUNT(*) AS total FROM usuarios WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if($row['total'] > 0){
        $mensagem = "Este e-mail já está cadastrado.";
    } else {
        $hashedPassword = sha1($senha);
        try {
            
            $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':senha', $hashedPassword);
            $stmt->execute();

            $mensagem = "Usuário registrado com sucesso!";
        } catch (PDOException $e) {
            echo "Erro ao registrar usuário: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
</head>
<body>
    <div class="cad">
    <h1>Cadastre o usuário!</h1>
    <?php if(!empty($mensagem)): ?>
        <p><?php echo $mensagem; ?></p>
        <?php endif; ?>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="nome">Nome: </label>
            <input type="text" name="nome" id="nome" required><br>
            <label for="email">Email: </label>
            <input type="email" name="email" id="email" required><br>
            <label for="senha">Senha: </label>
            <input type="password" id="senha" name="senha" required><br>
            <button type="submit">Enviar</button>
        </form>
    </div>
    </div>
</body>
</html>