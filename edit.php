<?php
require_once('./connection.php');

if (!isset($_GET['id']) || !$_GET['id']) {
    echo "Viga: raamatu ID puudub.";
    exit();
}

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove_author'])) {
        $remove_id = $_POST['remove_author'];
        $stmt = $pdo->prepare("
            DELETE FROM book_authors 
            WHERE book_id = :book_id AND author_id = :author_id
        ");
        $stmt->execute(['book_id' => $id, 'author_id' => $remove_id]);
        header("Location: edit.php?id=$id");
        exit();
    }

    if (isset($_POST['title'])) {
        $title = $_POST['title'] ?? '';
        $price = $_POST['price'] !== '' ? $_POST['price'] : 0;
        $language = $_POST['language'] ?? '';
        $pages = $_POST['pages'] !== '' ? $_POST['pages'] : 0;
        $summary = $_POST['summary'] ?? '';
        $author_ids = $_POST['author_ids'] ?? [];
        $new_author_first = trim($_POST['new_author_first'] ?? '');
        $new_author_last = trim($_POST['new_author_last'] ?? '');

        if ($new_author_first && $new_author_last) {
            $stmt = $pdo->prepare("
                INSERT INTO authors (first_name, last_name) 
                VALUES (:first, :last)
            ");
            $stmt->execute(['first' => $new_author_first, 'last' => $new_author_last]);
            $author_ids[] = $pdo->lastInsertId();
        }

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

        $pdo->prepare("DELETE FROM book_authors WHERE book_id = :id")
            ->execute(['id' => $id]);

        $stmt = $pdo->prepare("
            INSERT INTO book_authors (book_id, author_id) 
            VALUES (:book_id, :author_id)
        ");
        foreach ($author_ids as $aid) {
            $stmt->execute(['book_id' => $id, 'author_id' => $aid]);
        }

        header("Location: edit.php?id=$id");
        exit();
    }
}

$stmt = $pdo->prepare('SELECT * FROM books WHERE id = :id');
$stmt->execute(['id' => $id]);
$book = $stmt->fetch();

if (!$book) {
    echo "Raamatut ei leitud.";
    exit();
}

$stmt = $pdo->prepare("
    SELECT a.id, a.first_name, a.last_name 
    FROM authors a 
    JOIN book_authors ba ON a.id = ba.author_id 
    WHERE ba.book_id = :id
");
$stmt->execute(['id' => $id]);
$book_authors = $stmt->fetchAll();
$book_author_ids = array_column($book_authors, 'id');

$stmt = $pdo->prepare('SELECT * FROM authors ORDER BY last_name, first_name');
$stmt->execute();
$authors = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Muuda raamatut - <?= htmlspecialchars($book['title']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 20px auto;
            padding: 0 20px;
        }
        .dropdown-box { 
            border: 1px solid #aaa; 
            padding: 8px; 
            border-radius: 6px; 
            width: 300px; 
            cursor: pointer; 
            position: relative; 
            background: white;
        }
        .dropdown-options { 
            display: none; 
            position: absolute; 
            background: white; 
            border: 1px solid #ccc; 
            border-radius: 6px; 
            margin-top: 4px; 
            max-height: 150px; 
            overflow-y: auto; 
            width: 300px; 
            z-index: 10; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .dropdown-options label { 
            display: block; 
            padding: 6px 8px; 
            cursor: pointer;
        }
        .dropdown-options label:hover { 
            background: #f0f0f0; 
        }
        input[type="text"], 
        input[type="number"], 
        textarea {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        input[type="text"], 
        textarea {
            width: 400px;
            max-width: 100%;
        }
        textarea {
            resize: vertical;
        }
        button {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        button[type="submit"] {
            background: #007bff;
            color: white;
        }
        button[type="submit"]:hover {
            background: #0056b3;
        }
        button[type="button"] {
            background: #6c757d;
            color: white;
        }
        button[type="button"]:hover {
            background: #545b62;
        }
        .remove-btn {
            background: #dc3545;
            color: white;
            padding: 4px 8px;
            font-size: 12px;
            margin-left: 10px;
        }
        .remove-btn:hover {
            background: #c82333;
        }
        .author-item {
            margin: 8px 0;
            padding: 8px;
            background: #f8f9fa;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <h1>Muuda raamatut</h1>

    <form method="POST">
        <label>Pealkiri:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($book['title']) ?>" required><br>

        <label>Autorid:</label>
        <div style="position: relative; display: inline-block;">
            <div class="dropdown-box" id="authorDropdown">Vali autorid →</div>
            <div class="dropdown-options" id="authorOptions">
                <?php foreach ($authors as $author): ?>
                    <label>
                        <input type="checkbox" 
                               name="author_ids[]" 
                               value="<?= $author['id'] ?>" 
                               <?= in_array($author['id'], $book_author_ids) ? 'checked' : '' ?>>
                        <?= htmlspecialchars($author['first_name'] . ' ' . $author['last_name']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
        <br><br>

        <div style="margin-bottom: 20px;">
            <strong>Praegused autorid:</strong><br>
            <?php if (empty($book_authors)): ?>
                <span style="color: #666; font-style: italic;">Autoreid pole lisatud</span>
            <?php else: ?>
                <?php foreach ($book_authors as $author): ?>
                    <div class="author-item" style="margin-top: 8px;">
                        <span><?= htmlspecialchars($author['first_name'] . ' ' . $author['last_name']) ?></span>
                        <form method="POST" style="display: inline; margin: 0;">
                            <input type="hidden" name="remove_author" value="<?= $author['id'] ?>">
                            <button type="submit" class="remove-btn" onclick="return confirm('Kas oled kindel, et soovid seda autorit eemaldada?')">
                                Eemalda
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <label>Lisa uus autor:</label>
        <input type="text" name="new_author_first" placeholder="Eesnimi" style="width: 180px;">
        <input type="text" name="new_author_last" placeholder="Perekonnanimi" style="width: 180px;">

        <label>Hind (EUR):</label>
        <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($book['price']) ?>" style="width: 120px;">

        <label>Keel:</label>
        <input type="text" name="language" value="<?= htmlspecialchars($book['language']) ?>" style="width: 120px;">

        <label>Lehekülgede arv:</label>
        <input type="number" name="pages" value="<?= htmlspecialchars($book['pages']) ?>" style="width: 120px;">

        <label>Kirjeldus:</label>
        <textarea name="summary" rows="4"><?= htmlspecialchars($book['summary']) ?></textarea>

        <div style="margin-top: 20px;">
            <button type="submit">Salvesta</button>
            <a href="book.php?id=<?= $id ?>">
                <button type="button">Tagasi</button>
            </a>
        </div>
    </form>

    <script>
        const dropdown = document.getElementById('authorDropdown');
        const options = document.getElementById('authorOptions');

        dropdown.addEventListener('click', () => {
            const isVisible = options.style.display === 'block';
            options.style.display = isVisible ? 'none' : 'block';
        });

        document.addEventListener('click', (e) => {
            if (!dropdown.contains(e.target) && !options.contains(e.target)) {
                options.style.display = 'none';
            }
        });

        options.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    </script>
</body>
</html>