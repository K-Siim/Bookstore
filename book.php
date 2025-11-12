<?php
require_once('./connection.php');
if (!isset($_GET['id']) || !$_GET['id']) {
    echo 'Sutshka';
    exit();
}
$id = $_GET['id'];
$stmt = $pdo->prepare('SELECT * FROM books WHERE id = :id');
$stmt->execute(['id' => $id]);
$book = $stmt->fetch();

$stmt = $pdo->prepare('SELECT first_name, last_name FROM book_authors ba left join authors a on ba.author_id = a.id WHERE book_id =:book_id');
$stmt->execute(['book_id' => $id]);
$authors = $stmt->fetchAll();

var_dump($authors);
var_dump($book);



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $book['title'] ?></title>
</head>

<body>
    <h1><?= $book['title'] ?></h1>

    <p>Autorid:
    <ul>
        <?php foreach ($authors as $author) { ?>
            <li><?= "{$author['first_name']} {$author['last_name']}"; ?></li>
        <?php } ?>
    </ul>
    </p>


    <p>Kirjeldus: <?= $book['summary'] ?></p>
    <p>Lehekülgi: <?= $book['pages'] ?></p>
    <p>Keel: <?= $book['language'] ?></p>
    <p>Teose ilmumise aasta: <?= $book['release_date'] ?></p>
    <p>Hind: <?= $book['price'] ?> EUR</p>
    <p>Raamatu tüüp: <?= $book['type'] ?></p>
    <p>E-poe saadavus: <?= $book['stock_saldo'] ?> EUR</p>

    <img src="<?= $book['cover_path'] ?>" alt="">

        <br>
        <a href="./edit.php?id=<?= $book['id'] ?>">Muuda</a>
        <a href="./delete.php?id=<?= $book['id'] ?>">Kustuta</a>


</body>

</html>