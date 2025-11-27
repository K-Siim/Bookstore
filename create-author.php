<?php

require_once('./connection.php');

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create-author') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    
    if (!$firstName || !$lastName) {
        $message = 'Viga: Palun täida kõik väljad!';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO authors (first_name, last_name) VALUES (:first_name, :last_name)');
            $stmt->execute([
                'first_name' => $firstName,
                'last_name' => $lastName,
            ]);
            $message = 'Autor edukalt lisatud!';
            $_POST = [];
        } catch (Exception $e) {
            $message = 'Viga: ' . $e->getMessage();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lisa autor</title>
</head>
<body>
    <h1>Lisa uus autor</h1>
    
    <?php if ($message): ?>
        <p style="color: <?= strpos($message, 'Viga') !== false ? 'red' : 'green'; ?>;"><?= htmlspecialchars($message); ?></p>
    <?php endif; ?>
    
    <form method="post" action="create-author.php">
        <div>
            <label for="first_name">Eesnimi:</label>
            <input type="text" name="first_name" id="first_name" 
                   value="<?= htmlspecialchars($_POST['first_name'] ?? ''); ?>" required>
        </div>
        <br>
        <div>
            <label for="last_name">Perekonnanimi:</label>
            <input type="text" name="last_name" id="last_name" 
                   value="<?= htmlspecialchars($_POST['last_name'] ?? ''); ?>" required>
        </div>
        <br>
        <button type="submit" name="action" value="create-author">Lisa autor</button>
    </form>
    
    <br>
    <a href="index.php">Tagasi pealehele</a>
</body>
</html>

