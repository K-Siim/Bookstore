<?php

require_once('./connection.php');

if ( !isset($_GET['id']) || !$_GET['id'] ) {
    echo 'Viga: raamatut ei leitud!';
    exit();
}

$id = $_GET['id'];

$stmt = $pdo->prepare('SELECT * FROM books WHERE id = :id');
$stmt->execute(['id' => $id]);
$book = $stmt->fetch();

$stmt = $pdo->prepare('SELECT a.id, a.first_name, a.last_name FROM book_authors ba LEFT JOIN authors a ON ba.author_id = a.id WHERE ba.book_id = :book_id');
$stmt->execute(['book_id' => $id]);
$authors = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($book['title']) ?></title>
</head>
<body>
    <h1><?= htmlspecialchars($book['title']) ?></h1>

    <h2>Raamatu info</h2>
    <p>Pealkiri: <?= htmlspecialchars($book['title'] ?? '') ?></p>
    <p>Aasta: <?= htmlspecialchars($book['year'] ?? $book['release_date'] ?? '') ?></p>
    <p>Tüüp: <?= htmlspecialchars($book['type'] ?? $book['publisher'] ?? '') ?></p>
    <p>Hind: <?= htmlspecialchars($book['price'] ?? '') ?></p>
    <p>Lehekülgi: <?= htmlspecialchars($book['pages'] ?? $book['quantity'] ?? '') ?></p>
    <p>Keel: <?= htmlspecialchars($book['language'] ?? $book['isbn'] ?? '') ?></p>
    <p>Sisu: <?= htmlspecialchars($book['summary'] ?? $book['description'] ?? '') ?></p>
    <?php if (!empty($book['cover_path']) || !empty($book['image'])): ?>
        <p>Pilt:</p>
        <img src="<?= htmlspecialchars($book['cover_path'] ?? $book['image']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" style="max-width:200px; height:auto;">
    <?php else: ?>
        <p>Puudub pilt</p>
    <?php endif; ?>

    <h2>Autorid</h2>
    <ul>
        <?php foreach ($authors as $author): ?>
            <li>
                <?= htmlspecialchars($author['first_name'] . ' ' . $author['last_name']) ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <a href="./edit.php?id=<?= $id ?>">Muuda</a>
    <br>

    <form action="./delete.php" method="POST">
        <input type="hidden" name="id" value="<?= $id; ?>">
        <button type="submit" name="action" value="delete" onclick="return confirm('Oled kindel, et tahad kustutada?');">Kustuta raamat</button>   
    </form>
    
    <a href="index.php">    
        <button>Tagasi</button>
    </a>

</body>
</html>