<?php

require_once('./connection.php');

$id = $_POST['id'];

if ( !$id || !isset($_POST['action']) || $_POST['action'] != 'delete' ) {
    echo 'Viga: vigane URL!';
    exit();
}

$stmt = $pdo->prepare('UPDATE books SET is_deleted = 1 WHERE id = :id');
$stmt->execute([
    'id' => $id,
]);

header("Location: ./index.php");
die();