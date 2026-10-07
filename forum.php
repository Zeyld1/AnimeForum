<?php
require "database.php";
$kategorier = $conn->query("SELECT id, navn FROM Kategorier");
$valgt = $_GET["kategori"] ?? "";

$sql = "
    SELECT Traade.id, Traade.titel, Traade.indhold,
           ForumUsers.Brugernavn,
           GROUP_CONCAT(Kategorier.navn SEPARATOR ', ') AS kategorier
    FROM Traade
    LEFT JOIN ForumUsers ON Traade.bruger_id = ForumUsers.id
    LEFT JOIN TraadKategorier ON Traade.id = TraadKategorier.traad_id
    LEFT JOIN Kategorier ON TraadKategorier.kategori_id = Kategorier.id
";

if ($valgt !== "") {
    $sql .= " WHERE Traade.id IN (SELECT traad_id FROM TraadKategorier WHERE kategori_id = ?)";
}

$sql .= " GROUP BY Traade.id, ForumUsers.Brugernavn ORDER BY Traade.id DESC";

$stmt = $conn->prepare($sql);
if ($valgt !== "") {
    $stmt->bind_param("i", $valgt);
}
$stmt->execute();
$result = $stmt->get_result();
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
                    <p class="traad-forfatter">Af <?php echo htmlspecialchars($row["Brugernavn"] ?? "Slettet bruger"); ?></p>
                    <p><?php echo htmlspecialchars($row["indhold"]); ?></p>

                    <?php if (!empty($row["kategorier"])) { ?>
                        <div class="traad-kategorier">
                            <?php foreach (explode(", ", $row["kategorier"]) as $navn) { ?>
                                <span class="kategori-tag"><?php echo htmlspecialchars($navn); ?></span>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </section>

    </div>
</main>

<?php include 'footer.php'; ?>

</body>
</html>