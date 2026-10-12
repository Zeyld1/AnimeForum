<?php
session_start();
require "database.php";
$kategorier = $conn->query("SELECT id, navn FROM Kategorier");
$valgt = $_GET["kategori"] ?? "";

$sql = "
    SELECT Traade.id, Traade.titel, Traade.indhold,
       ForumUsers.Brugernavn,
       ForumUsers.Profilbillede,
       GROUP_CONCAT(Kategorier.navn SEPARATOR ', ') AS kategorier
    FROM Traade
    LEFT JOIN ForumUsers ON Traade.bruger_id = ForumUsers.id
    LEFT JOIN TraadKategorier ON Traade.id = TraadKategorier.traad_id
    LEFT JOIN Kategorier ON TraadKategorier.kategori_id = Kategorier.id
";

if ($valgt !== "") {
    $sql .= " WHERE Traade.id IN (SELECT traad_id FROM TraadKategorier WHERE kategori_id = ?)";
}

$sql .= " GROUP BY Traade.id, ForumUsers.Brugernavn, ForumUsers.Profilbillede ORDER BY Traade.id DESC";
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
            <div class="forum-top">

    <h1>Forum</h1>

    <div class="search-container">

        <div class="search-box">
            <span class="search-icon">🔍</span>

            <input
                type="text"
                id="forum-search"
                placeholder="Søg her"
                autocomplete="off"
            >
        </div>

        <div id="search-forslag" class="search-forslag"></div>

    </div>

</div>

            <?php while ($row = $result->fetch_assoc()) { ?>

            <?php

                      if (!empty($row["Profilbillede"]))
                         {
                            $avatar = $row["Profilbillede"];
                         }
                        else
                         {
                            $avatar = "images/Ikon.png";
                         }

?>
                <div class="traad">

    <div class="traad-hoved">

        <img
            src="<?php echo htmlspecialchars($avatar); ?>"
            class="traad-avatar"
            alt="Profilbillede"
        >

        <div class="traad-indhold">

            <h2>
                <a href="vis_traad.php?id=<?php echo (int)$row["id"]; ?>">
                    <?php echo htmlspecialchars($row["titel"]); ?>
                </a>
            </h2>

            <p class="traad-forfatter">
                Af <?php echo htmlspecialchars($row["Brugernavn"] ?? "Slettet bruger"); ?>
            </p>

            <p>
                <?php echo htmlspecialchars($row["indhold"]); ?>
            </p>

            <?php if (!empty($row["kategorier"])) { ?>
                <div class="traad-kategorier">
                    <?php foreach (explode(", ", $row["kategorier"]) as $navn) { ?>
                        <span class="kategori-tag">
                            <?php echo htmlspecialchars($navn); ?>
                        </span>
                    <?php } ?>
                </div>
            <?php } ?>

        </div>

    </div>

</div>
            <?php } ?>
        </section>

    </div>
</main>

<?php include 'footer.php'; ?>

<script>

const searchInput = document.getElementById("forum-search");
const forslagBoks = document.getElementById("search-forslag");

// Finder alle tråde på forumsiden
const traade = document.querySelectorAll(".traad");

searchInput.addEventListener("input", function()
{
    // Det brugeren skriver
    const soegning = searchInput.value.toLowerCase();

    // Tømmer gamle forslag
    forslagBoks.innerHTML = "";

    // Hvis søgefeltet er tomt, viser vi ingen forslag
    if (soegning == "")
    {
        forslagBoks.style.display = "none";
        return;
    }

    traade.forEach(function(traad)
    {
        // Finder titlen på tråden
        const link = traad.querySelector("h2 a");

        const titel = link.textContent.toLowerCase();

        // Tjekker om titlen indeholder det brugeren skriver
        if (titel.includes(soegning))
        {
            const forslag = document.createElement("a");

            forslag.href = link.href;
            forslag.textContent = link.textContent;

            forslagBoks.appendChild(forslag);
        }
    });

    // Hvis der blev fundet noget, viser vi boksen
    if (forslagBoks.children.length > 0)
    {
        forslagBoks.style.display = "flex";
    }
    else
    {
        forslagBoks.style.display = "none";
    }
});

</script>

</body>
</html>