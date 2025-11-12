<?php
require_once('./connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');

    if ($first_name && $last_name) {
        $stmt = $pdo->prepare("INSERT INTO authors (first_name, last_name) VALUES (:first_name, :last_name)");
        $stmt->execute([
            'first_name' => $first_name,
            'last_name' => $last_name
        ]);

        header("Location: index.php");
        exit();
    } else {
        $error = "Palun täida mõlemad väljad!";
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

    <?php if (!empty($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" action="add_author.php">
        <label for="first_name">Eesnimi:</label><br>
        <input type="text" id="first_name" name="first_name" required><br><br>

        <label for="last_name">Perekonnanimi:</label><br>
        <input type="text" id="last_name" name="last_name" required><br><br>

        <button type="submit">Lisa
