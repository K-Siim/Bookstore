<?php
require_once 'connection.php';

$search = isset($_GET['q']) ? trim($_GET['q']) : '';


if ($search) {
    $stmt = $pdo->prepare("SELECT id, title 
                           FROM books 
                           WHERE is_deleted = 0 AND title LIKE :search");
    $stmt->execute(['search' => "%$search%"]);
} else {
   
    $stmt = $pdo->query("SELECT id, title FROM books WHERE is_deleted = 0");
}

$books = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raamatupood</title>
</head>
<body>
    <h1>Raamatupoe nimekiri</h1>

    <form method="get" action="index.php">
        <input type="text" name="q" placeholder="Otsi raamatu pealkirja..." 
               value="<?= ($search) ?>">
        <button type="submit">Otsi</button>
    </form>

    <hr>

    <?php if (count($books) === 0): ?>
        <p>Raamatuid ei leitud.</p>
    <?php else: ?>
        <?php foreach ($books as $book): ?>
            <a href="book.php?id=<?= $book['id'] ?>">
                <?= ($book['title']) ?>
            </a><br>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>
