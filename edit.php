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

$stmt = $pdo->prepare('SELECT a.id, first_name, last_name FROM book_authors ba LEFT JOIN authors a ON ba.author_id = a.id WHERE book_id = :book_id;');
$stmt->execute(['book_id' => $id]);
$bookAuthors = $stmt->fetchAll();
$bookAuthorIds = [];

$stmt = $pdo->query('SELECT * FROM authors');
$authors = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $book['title']; ?></title>
</head>
<body>
    <form action="update.php" method="post">
        <input type="hidden" name="id" value="<?= $id; ?>">
        <input type="text" name="title" value="<?= $book['title']; ?>">
        <br><br>
        <button type="submit" name="action" value="save">Salvesta</button>
    </form>
    
    <br>

    <div>
        Autorid:
    </div>
    <ul>
<?php foreach ( $bookAuthors as $author ) {
        $bookAuthorIds[] = $author['id']; ?>
    <li>
        <form action="./remove-author.php" method="post">
            <?= "{$author['first_name']} {$author['last_name']}"; ?>
            <input type="hidden" name="book_id" value="<?= $id; ?>">
            <input type="hidden" name="author_id" value="<?= $author['id']; ?>">
            <button type="submit" name="action" value="remove-author" style="border: none; background: rgba(0,0,0,0); cursor: pointer;">✖</button>
        </form>
    </li>
<?php } ?>
    </ul>

    <br>

    <div>
        Lisa autor:
    </div>
    <form action="./add-author.php" method="post">
        <input type="hidden" name="book_id" value="<?= $id; ?>">
        <select name="author_id">
            <option></option>
<?php foreach ( $authors as $author ) {
        if ( !in_array($author['id'], $bookAuthorIds) ) { ?>
            <option value="<?= $author['id']; ?>"><?= $author['first_name']; ?> <?= $author['last_name']; ?></option>
<?php }} ?>
        </select>
        <button type="submit" name="action" value="add-author">Lisa</button>
    </form>

</body>
</html>