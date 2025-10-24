<?php

require_once 'connection.php';


$stmt = $pdo->query('SELECT title FROM books');

$stmt = $pdo->query('SELECT id, title FROM books');

while ($row = $stmt->fetch())
{
    echo "<a href='book.php?id={$row['id']}'>{$row['title']}</a><br>";
}