<?php
require "database.php";
$kategorier = $conn->query("SELECT id, navn FROM Kategorier");
$valgt = $_GET["kategori"] ?? "";

if ($valgt !== "") {
    $stmt = $conn->prepare("
        SELECT Traade.id, Traade.titel, Traade.indhold
        FROM Traade
        JOIN TraadKategorier ON Traade.id = TraadKategorier.traad_id
        WHERE TraadKategorier.kategori_id = ?
        ORDER BY Traade.id DESC
    ");
    $stmt->bind_param("i", $valgt);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT id, titel, indhold FROM Traade ORDER BY id DESC");
}
?>
<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>

<main>
    <div class="forum-layout">

        <aside class="sidebar">
            <a href="opret_traad.php" class="knap">Opret tråd</a>

            <nav class="sidebar-menu">
                <a href="forum.php">Alle</a>
                <?php while ($kat = $kategorier->fetch_assoc()) { ?>
                    <a href="forum.php?kategori=<?php echo (int)$kat["id"]; ?>">
                        <?php echo htmlspecialchars($kat["navn"]); ?>
                    </a>
                <?php } ?>
            </nav>
        </aside>

        <section class="forum-indhold">
            <h1>Forum</h1>

            <?php while ($row = $result->fetch_assoc()) { ?>
                <div class="traad">
                    <h2><a href="vis_traad.php?id=<?php echo (int)$row["id"]; ?>"><?php echo htmlspecialchars($row["titel"]); ?></a></h2>
                    <p><?php echo htmlspecialchars($row["indhold"]); ?></p>
                </div>
            <?php } ?>
        </section>

    </div>
</main>

<?php include 'footer.php'; ?>

</body>
</html>