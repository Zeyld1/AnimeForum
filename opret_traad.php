<?php
// Åbner sessionen, så vi kan se, om brugeren er logget ind.
session_start();
// Er brugeren ikke logget ind, sendes hun til login-siden, og exit() stopper resten af siden.
if (!isset($_SESSION["bruger_id"])) {
    header("Location: login.php");
    exit();
}
// Forbindelsen til databasen ($conn).
require "database.php";

// Koden herunder kører kun, når formularen er sendt (POST). Første gang siden åbnes, springes den over.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Henter det, brugeren har skrevet i formularen.
    // OBS: serveren tjekker ikke længden her (kun maxlength i browseren). Det gør vis_traad.php for kommentarer.
    $titel = $_POST["titel"];
    $indhold = $_POST["indhold"];
    // kategori_id[] er en liste, fordi man kan vælge flere kategorier.
    $kategori_ids = $_POST["kategori_id"];
    // Bruger-id kommer fra sessionen og ikke fra formularen, så man ikke kan skrive som en anden.
    $bruger_id = $_SESSION["bruger_id"];

    // 1) Gem selve tråden. ? er pladsholdere (prepared statement mod SQL injection).
    $stmt = $conn->prepare("INSERT INTO Traade (bruger_id, titel, indhold) VALUES (?, ?, ?)");
    // "iss" = heltal (bruger_id), tekst (titel), tekst (indhold).
    $stmt->bind_param("iss", $bruger_id, $titel, $indhold);
    $stmt->execute();

    // insert_id er id'et på den tråd, vi lige har oprettet. Vi skal bruge det til kategorierne.
    $traad_id = $conn->insert_id;

    // 2) Gem kategorierne i koblingstabellen TraadKategorier: én række pr. valgt kategori.
    $stmt2 = $conn->prepare("INSERT INTO TraadKategorier (traad_id, kategori_id) VALUES (?, ?)");
    // foreach kører én gang for hver valgt kategori.
    foreach ($kategori_ids as $kategori_id) {
        $stmt2->bind_param("ii", $traad_id, $kategori_id);
        $stmt2->execute();
    }

    // Send brugeren tilbage til forum-siden. exit() stopper koden.
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
    <!-- Menuen (header.php) indsættes her. -->
    <?php include 'header.php'; ?>

    <main>
        <div class="opret-container">
            <h1>Opret tråd</h1>

            <!-- POST sender data til PHP-koden øverst i filen. maxlength begrænser længden i browseren, required betyder at feltet skal udfyldes. -->
            <form method="POST" action="opret_traad.php" class="opret-form">
                <label for="titel">Titel</label>
                <input type="text" name="titel" id="titel" maxlength="100" required>

                <label for="kategori">Kategori (hold Ctrl nede for at vælge flere)</label>
                <select name="kategori_id[]" id="kategori" multiple required>
                    <?php
                    // Henter kategorierne fra databasen, så listen altid passer med tabellen.
                    $result = $conn->query("SELECT id, navn FROM Kategorier");
                    // Laver én <option> pr. kategori. value = kategoriens id, som sendes til PHP.
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . (int)$row['id'] . "'>" . htmlspecialchars($row['navn']) . "</option>";
                    }
                    ?>
                </select>

                <label for="indhold">Indhold</label>
                <textarea name="indhold" id="indhold" rows="8" maxlength="5000" required></textarea>

                <button type="submit">Opret tråd</button>
            </form>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>