<?php
// var_dump($_POST);
if (isset($_POST["action-submit"]) && isset($_POST["user-name"])) {
    $user = $_POST["user-name"];
}

$names = ['Mari', 'Jüri', 'Kati', 'Peeter', 'Triin'];
foreach ($names as $key => $name) {
    echo ($key + 1) . ". {$name}<br>";
}

for ($i = 0; $i < count($names); $i++) {
    echo ($i + 1) . ". {$names[$i]}<br>";
}

$i = 0;
while ($i < count($names)) {
    echo ($i + 1) . ". {$names[$i]}<br>";
    $i++;
}

$i = 0;
do {
    echo ($i + 1) . ". {$names[$i]}<br>";
    $i++;
} while ($i < count($names));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sissejuhatus PHPsse</title>
</head>
<body>
    <form action="./hello.php" method="POST">
        <label for="user">Nimi</label>
        <input type="text" name="user-name" id="user">
        <input type="submit" value="Saada" name="action-submit">
    </form>

    <?php if (isset($user)) { ?>
        Hello, <?= $user; ?>!
    <?php } ?>
</body>
</html>
