<?php
require_once('./connection.php');


if (!isset($_GET['id']) || !$_GET['id']) {
    echo "Viga: raamatu ID puudub.";
    exit();
}

$id = $_GET['id'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $price = $_POST['price'] ?? '';
    $language = $_POST['language'] ?? '';
    $pages = $_POST['pages'] ?? '';
    $summary = $_POST['summary'] ?? '';

    
    $stmt = $pdo->prepare("
        UPDATE books 
        SET title = :title,
            price = :price,
            language = :language,
            pages = :pages,
            summary = :summary
        WHERE id = :id
    ");

    $stmt->execute([
        'title' => $title,
        'price' => $price,
        'language' => $language,
        'pages' => $pages,
        'summary' => $summary,
        'id' => $id
    ]);

    
    header("Location: book.php?id=$id");
    exit();
}


$stmt = $pdo->prepare('SELECT * FROM books WHERE id = :id');
$stmt->execute(['id' => $id]);
$book = $stmt->fetch();

if (!$book) {
    echo "Raamatut ei leitud.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Muuda raamatut - <?= htmlspecialchars($book['title']) ?></title>
</head>
<body>
    <h1>Muuda raamatut</h1>

    <form method="POST">
        <label>Pealkiri:</label><br>
        <input type="text" name="title" value="<?= ($book['title']) ?>"><br><br>

        <label>Hind (EUR):</label><br>
        <input type="number" step="0.01" name="price" value="<?= ($book['price']) ?>"><br><br>

        <label>Keel:</label><br>
        <input type="text" name="language" value="<?= ($book['language']) ?>"><br><br>

        <label>Lehekülgede arv:</label><br>
        <input type="number" name="pages" value="<?= ($book['pages']) ?>"><br><br>

        <label>Kirjeldus:</label><br>
        <textarea name="summary" rows="4" cols="40"><?= ($book['summary']) ?></textarea><br><br>

        <button type="submit">💾 Salvesta</button>
        <a href="book.php?id=<?= $id ?>">⬅ Tagasi</a>
    </form>

</body>
</html>
