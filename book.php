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

$stmt = $pdo->prepare('SELECT first_name, last_name FROM book_authors ba LEFT JOIN authors a ON ba.author_id = a.id WHERE book_id = :book_id;');
$stmt->execute(['book_id' => $id]);
$authors = $stmt->fetchAll();

// var_dump($authors);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $book['title']; ?></title>
</head>
<body>
    <h1><?= $book['title']; ?></h1>

    Autorid:
    <ul>
<?php foreach ( $authors as $author ) { ?>
    <li><?= "{$author['first_name']} {$author['last_name']}"; ?></li>
<?php } ?>
    </ul>

    <a href="./edit.php?id=<?= $id; ?>">Muuda</a>
    <br>

    <form action="./delete.php" method="post">
        <input type="hidden" name="id" value="<?= $id; ?>">
        <button type="submit" name="action" value="delete">Kustuta</button>
    </form>

    <a href="index.php">
        <button>Tagasi</button>
    </a>

</body>
</html>