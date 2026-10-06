<?php
session_start();
require "database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titel = $_POST["titel"];
    $indhold = $_POST["indhold"];
    $kategori_id = $_POST["kategori_id"];
    $bruger_id = $_SESSION["bruger_id"];

    $stmt = $conn->prepare("INSERT INTO Traade (bruger_id, kategori_id, titel, indhold) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $bruger_id, $kategori_id, $titel, $indhold);
    $stmt->execute();

    header("Location: forum.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Opret Tråd</title>
</head>
<body>
    <?php include 'header.php'; ?>

    <form method="POST" action="opret_traad.php">
        <label for="titel">Titel:</label>
        <input type="text" name="titel" id="titel" required>

        <label for="kategori">Kategori:</label>
        <select name="kategori_id" id="kategori">
            <?php
            $result = $conn->query("SELECT id, navn FROM Kategorier");
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['id'] . "'>" . $row['navn'] . "</option>";
            }
            ?>
        </select>

        <label for="indhold">Indhold:</label>
        <textarea name="indhold" id="indhold" required></textarea>

        <button type="submit">Opret tråd</button>
    </form>

    <?php include 'footer.php'; ?>
</body>
</html>