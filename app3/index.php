<?php
define('DB_HOST', getenv('DB_HOST') ?: '10.10.10.98');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: 'senha');
define('DB_NAME', getenv('DB_NAME') ?: 'app3_db');

// Configuração do banco
$host = DB_HOST;
$dbname = DB_NAME;
$username = DB_USER;
$password = DB_PASSWORD;

try {
    // Conexão com o servidor MySQL sem selecionar um banco de dados específico
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SHOW DATABASES LIKE '$dbname'");
    if ($stmt->rowCount() === 0) {
        // Cria o banco de dados se não existir
        $pdo->exec("CREATE DATABASE `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }

    $pdo->exec("USE `$dbname`");

    $stmt = $pdo->query("SHOW TABLES LIKE 'usuarios'");
    if ($stmt->rowCount() === 0) {
        $createTableSQL = "
            CREATE TABLE `usuarios` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `nome` VARCHAR(100) NOT NULL,
                `email` VARCHAR(100) NOT NULL
            ) ENGINE=InnoDB;
        ";
        $pdo->exec($createTableSQL);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';

        if ($nome && $email) {
            $stmt = $pdo->prepare("INSERT INTO `usuarios` (`nome`, `email`) VALUES (:nome, :email)");
            $stmt->execute(['nome' => $nome, 'email' => $email]);
            echo "Usuário cadastrado com sucesso!";
        } else {
            echo "Por favor, preencha todos os campos.";
        }
    }

    // Busca os dados da tabela
    $stmt = $pdo->query("SELECT * FROM usuarios");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Usuários</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f7f4f2; /* Fundo bege claro */
            color: #4b2e2e; /* Marrom escuro */
        }
        h1, h2 {
            text-align: center;
            color: #5a3d31; /* Tom de marrom médio */
        }
        form {
            background-color: #ffffff; /* Fundo branco */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: 20px auto;
            border: 2px solid #d2b48c; /* Borda bege/tom areia */
        }
        label {
            font-weight: bold;
            color: #4b2e2e;
        }
        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            background-color: #8b5a2b; /* Marrom escuro */
            color: #ffffff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background-color: #5a3d31; /* Marrom médio */
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #ffffff; /* Fundo branco */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #d2b48c; /* Bege/tom areia */
            color: #4b2e2e; /* Marrom escuro */
        }
        tr:nth-child(even) {
            background-color: #f5e8dc; /* Bege claro */
        }
        tr:hover {
            background-color: #e2c6a9; /* Bege médio */
        }
        a {
            color: #4b2e2e;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Gerenciamento de Usuários</h1>

    <!-- Formulário para inserção -->
    <form method="POST">
        <label for="nome">Nome:</label><br>
        <input type="text" id="nome" name="nome" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <button type="submit">Adicionar Usuário</button>
    </form>

    <!-- Exibição dos dados -->
    <h2>Lista de Usuários</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($usuarios)): ?>
                <tr>
                    <td colspan="3">Nenhum usuário encontrado.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['id']) ?></td>
                        <td><?= htmlspecialchars($usuario['nome']) ?></td>
                        <td><?= htmlspecialchars($usuario['email']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
