<?php
include("conexao.php");
$mensagem = "";
$usuarioParaEditar = null;


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = filter_input(INPUT_POST, "nome", FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $senha = filter_input(INPUT_POST, "senha", FILTER_SANITIZE_SPECIAL_CHARS);
    $id = filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT);

    if (empty($id)) {

        $sql = "SELECT COUNT(*) AS total FROM usuarios WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row['total'] > 0) {
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
    } else {

        $hashedPassword = sha1($senha);
        $sql = "UPDATE usuarios SET nome = :nome, email = :email, senha = :senha WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $hashedPassword);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            $mensagem = "Usuário atualizado com sucesso!";
        } else {
            $mensagem = "Erro ao atualizar usuário.";
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["delete"])) {
    $id = filter_input(INPUT_GET, "delete", FILTER_SANITIZE_NUMBER_INT);
    $sql = "DELETE FROM usuarios WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id);
    if ($stmt->execute()) {
        $mensagem = "Usuário deletado com sucesso!";
    } else {
        $mensagem = "Erro ao deletar usuário.";
    }
}

$sql = "SELECT * FROM usuarios";
$stmt = $pdo->query($sql);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["edit"])) {
    $id = filter_input(INPUT_GET, "edit", FILTER_SANITIZE_NUMBER_INT);
    $sql = "SELECT * FROM usuarios WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $usuarioParaEditar = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Usuários</title>
    <link rel="stylesheet" href="styleCrud.css">

</head>
<body>
    <div class="cad">
        <h1>Tela de administrador</h1>
        <?php if (!empty($mensagem)): ?>
            <p><?php echo $mensagem; ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="hidden" name="id" id="id" value="<?php echo $usuarioParaEditar['id'] ?? ''; ?>">
            <label for="nome">Nome: </label>
            <input type="text" name="nome" id="nome" value="<?php echo $usuarioParaEditar['nome'] ?? ''; ?>" required><br>
            <label for="email">Email: </label>
            <input type="email" name="email" id="email" value="<?php echo $usuarioParaEditar['email'] ?? ''; ?>" required><br>
            <label for="senha">Senha: </label>
            <input type="password" id="senha" name="senha" value="" ><br>
            <button type="submit">Enviar</button>
            <a href="cadastro.php" class="button">Tela Inicial</a>
        </form>
    </div>
    <div class="lista">
        <h2>Usuários Registrados</h2>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Ações</th>
            </tr>
            <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td><?php echo $usuario['id']; ?></td>
                <td><?php echo $usuario['nome']; ?></td>
                <td><?php echo $usuario['email']; ?></td>
                <td>
                    <a href="?edit=<?php echo $usuario['id']; ?>">Editar</a>
                    <a href="?delete=<?php echo $usuario['id']; ?>" onclick="return confirm('Tem certeza que deseja deletar?')">Deletar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
