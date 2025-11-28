<?php
require_once 'connection.php';

$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$authorMessage = '';
$authorFirst = '';
$authorLast = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create-author') {
    $authorFirst = trim($_POST['first_name'] ?? '');
    $authorLast = trim($_POST['last_name'] ?? '');

    if ($authorFirst === '' || $authorLast === '') {
        $authorMessage = 'Viga: palun täida autorite kõik väljad.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO authors (first_name, last_name) VALUES (:first_name, :last_name)');
            $stmt->execute([
                'first_name' => $authorFirst,
                'last_name'  => $authorLast,
            ]);
            $authorMessage = 'Autor edukalt lisatud!';
            $authorFirst = '';
            $authorLast = '';
        } catch (PDOException $e) {
            $authorMessage = 'Viga autori lisamisel: ' . htmlspecialchars($e->getMessage());
        }
    }
}


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

    <section style="margin-bottom: 24px;">
        <h2>Lisa uus autor</h2>
        <?php if ($authorMessage): ?>
            <p style="color: <?= str_starts_with($authorMessage, 'Viga') ? 'red' : 'green'; ?>;">
                <?= htmlspecialchars($authorMessage); ?>
            </p>
        <?php endif; ?>
        <form method="post" action="index.php">
            <input type="hidden" name="action" value="create-author">
            <div>
                <label for="first_name">Eesnimi:</label>
                <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($authorFirst); ?>" required>
            </div>
            <div>
                <label for="last_name">Perekonnanimi:</label>
                <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($authorLast); ?>" required>
            </div>
            <button type="submit">Lisa autor</button>
        </form>
        <p style="margin-top: 8px;">
            Või kasuta eraldi lehte: <a href="create-author.php">Lisa uus autor</a>
        </p>
    </section>

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
