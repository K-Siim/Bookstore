<?php

require_once('./connection.php');

$bookId = $_POST['book_id'];
$authorId = $_POST['author_id'];

if ( !$bookId || !$authorId || !isset($_POST['action']) || $_POST['action'] != 'remove-author' ) {
    echo 'Viga: vigane URL!';
    exit();
}

$stmt = $pdo->prepare('DELETE FROM book_authors WHERE book_id = :book_id AND author_id = :author_id');
$stmt->execute([
    'book_id' => $bookId,
    'author_id' => $authorId,
]);

header("Location: ./edit.php?id={$bookId}");
die();