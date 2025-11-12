<?php
require_once('./connection.php');


if (!isset($_GET['id']) || !$_GET['id']) {
    echo "Viga: raamatu ID puudub.";
    exit();
}

$id = $_GET['id'];


$stmt = $pdo->prepare('UPDATE books SET is_deleted = 1 WHERE id = :id');
$stmt->execute(['id' => $id]);


header('Location: index.php');
exit();
?>
