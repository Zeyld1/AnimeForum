<?php
session_start();
require "database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titel = $_POST["titel"];
    $indhold = $_POST["indhold"];
    $kategori_ids = $_POST["kategori_id"];
    $bruger_id = $_SESSION["bruger_id"];

    $stmt = $conn->prepare("INSERT INTO Traade (bruger_id, titel, indhold) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $bruger_id, $titel, $indhold);
    $stmt->execute();

    $traad_id = $conn->insert_id;

    $stmt2 = $conn->prepare("INSERT INTO TraadKategorier (traad_id, kategori_id) VALUES (?, ?)");
    foreach ($kategori_ids as $kategori_id) {
        $stmt2->bind_param("ii", $traad_id, $kategori_id);
        $stmt2->execute();
    }

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

    <main>
        <div class="opret-container">
            <h1>Opret tråd</h1>

            <form method="POST" action="opret_traad.php" class="opret-form">
                <label for="titel">Titel</label>
                <input type="text" name="titel" id="titel" required>

                <label for="kategori">Kategori (hold Ctrl nede for at vælge flere)</label>
                <select name="kategori_id[]" id="kategori" multiple required>
                    <?php
                    $result = $conn->query("SELECT id, navn FROM Kategorier");
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . (int)$row['id'] . "'>" . htmlspecialchars($row['navn']) . "</option>";
                    }
                    ?>
                </select>

                <label for="indhold">Indhold</label>
                <textarea name="indhold" id="indhold" rows="8" required></textarea>

                <button type="submit">Opret tråd</button>
            </form>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>