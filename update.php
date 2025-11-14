<?php

require_once('./connection.php');

$id = $_POST['id'];

if ( !$id || !isset($_POST['action']) || $_POST['action'] != 'save' ) {
    echo 'Viga: vigane URL!';
    exit();
}

$stmt = $pdo->prepare('UPDATE books SET title = :title WHERE id = :id');
$stmt->execute([
    'id' => $id,
    'title' => $_POST['title'],
]);

header("Location: ./book.php?id={$id}");
die();