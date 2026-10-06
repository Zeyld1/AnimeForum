<?php
require "database.php";

$traad_id = $_GET["____"];

$stmt = $conn->prepare("SELECT * FROM Traade WHERE id = ?");
$stmt->bind_param("i", $traad_id);
$stmt->execute();
$result = $stmt->get_result();
$traad = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Tråd</title>
</head>
<body>
    <?php include 'header.php'; ?>

    <main>
        <h1><?php echo $traad["titel"]; ?></h1>
        <p><?php echo $traad["indhold"]; ?></p>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>