<?php

require_once('./connection.php');

if (!isset($_POST['id']) || !$_POST['id'] || !isset($_POST['action']) || $_POST['action'] != 'save') {
    echo 'Viga: vigane URL!';
    exit();
}

$id = (int)$_POST['id'];

$fields = [];
$params = ['id' => $id];

if (isset($_POST['title'])) {
    $fields[] = 'title = :title';
    $params['title'] = $_POST['title'];
}

$fieldMapping = [
    'year' => ['release_date', 'year'],
    'publisher' => ['type', 'publisher'],
    'price' => ['price'],
    'quantity' => ['pages', 'quantity'],
    'isbn' => ['language', 'isbn'],
    'description' => ['summary', 'description'],
    'image' => ['cover_path', 'image'],
];


$columnNames = [
    'year' => 'release_date',
    'publisher' => 'type',
    'quantity' => 'pages',
    'isbn' => 'language',
    'description' => 'summary',
    'image' => 'cover_path',
];

foreach ($fieldMapping as $formField => $dbColumns) {
    if (isset($_POST[$formField]) && $_POST[$formField] !== '') {
        $dbColumn = $columnNames[$formField] ?? $formField;
        $fields[] = "{$dbColumn} = :{$dbColumn}";
        $params[$dbColumn] = $_POST[$formField];
    }
}

if (empty($fields)) {
    header("Location: ./book.php?id={$id}");
    exit();
}

try {
    $sql = 'UPDATE books SET ' . implode(', ', $fields) . ' WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    header("Location: ./book.php?id={$id}");
    exit();
    
} catch (PDOException $e) {
    $fields = [];
    $params = ['id' => $id];
    
    if (isset($_POST['title'])) {
        $fields[] = 'title = :title';
        $params['title'] = $_POST['title'];
    }
    
    $altColumnNames = [
        'year' => 'year',
        'publisher' => 'publisher',
        'quantity' => 'quantity',
        'isbn' => 'isbn',
        'description' => 'description',
        'image' => 'image',
    ];
    
    foreach ($fieldMapping as $formField => $dbColumns) {
        if (isset($_POST[$formField]) && $_POST[$formField] !== '') {
            $dbColumn = $altColumnNames[$formField] ?? $formField;
            $fields[] = "{$dbColumn} = :{$dbColumn}";
            $params[$dbColumn] = $_POST[$formField];
        }
    }
    
    if (!empty($fields)) {
        try {
            $sql = 'UPDATE books SET ' . implode(', ', $fields) . ' WHERE id = :id';
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            header("Location: ./book.php?id={$id}");
            exit();
        } catch (PDOException $e2) {
            echo 'Viga andmebaasi uuendamisel: ' . htmlspecialchars($e2->getMessage());
            echo '<br><a href="./edit.php?id=' . $id . '">Tagasi muutmise lehele</a>';
            exit();
        }
    } else {
        echo 'Viga: ei leitud uuendatavaid väljasid';
        echo '<br><a href="./edit.php?id=' . $id . '">Tagasi muutmise lehele</a>';
        exit();
    }
}