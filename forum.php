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
            <span class="search-icon">
              <svg viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="11" cy="11" r="7"></circle>
                  <line x1="16.5" y1="16.5" x2="21" y2="21"></line>
             </svg>
            </span>

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

// Finder søgefeltet ud fra id="forum-search"
// Vi gemmer det i variablen searchInput, så vi kan bruge det senere
const searchInput = document.getElementById("forum-search");

// Finder boksen hvor søgeresultaterne skal vises
const forslagBoks = document.getElementById("search-forslag");


// Finder alle forumtråde på siden
// document.querySelectorAll(".traad") betyder:
// Find alle HTML-elementer som har class="traad"
const traade = document.querySelectorAll(".traad");


// addEventListener lytter efter noget der sker på søgefeltet
// "input" betyder at funktionen kører hver gang brugeren skriver eller sletter et tegn
searchInput.addEventListener("input", function()
{

    // Henter det som brugeren har skrevet i søgefeltet
    // toLowerCase() gør teksten til små bogstaver
    // Det gør det nemmere at sammenligne tekst
    // Fx "Naruto" og "naruto" bliver behandlet ens
    const soegning = searchInput.value.toLowerCase();


    // Fjerner gamle søgeresultater
    // Ellers ville nye forslag bare blive lagt oveni de gamle
    forslagBoks.innerHTML = "";


    // Hvis brugeren ikke har skrevet noget i søgefeltet
    if (soegning == "")
    {
        // Skjuler boksen med søgeresultater
        forslagBoks.style.display = "none";

        // Stopper funktionen her
        return;
    }


    // Går igennem alle forumtråde én efter én
    traade.forEach(function(traad)
    {

        // Inde i den aktuelle tråd finder vi linket i <h2>
        // Det er her trådens titel står
        const link = traad.querySelector("h2 a");


        // Henter teksten fra linket
        // Fx "Bedste Anime 2026"
        // og laver den om til små bogstaver
        const titel = link.textContent.toLowerCase();


        // includes() undersøger om titlen indeholder det brugeren har skrevet
        //
        // Eksempel:
        // titel = "bedste anime 2026"
        // soegning = "anime"
        //
        // Så bliver resultatet true
        if (titel.includes(soegning))
        {

            // Opretter et nyt <a>-element med JavaScript
            // Dette bliver et klikbart søgeresultat
            const forslag = document.createElement("a");


            // Giver søgeresultatet samme link som den rigtige forumtråd
            // Hvis man klikker på forslaget, åbnes tråden
            forslag.href = link.href;


            // Sætter teksten i søgeresultatet
            // Vi bruger den originale titel, så store og små bogstaver beholdes
            forslag.textContent = link.textContent;


            // Lægger det nye søgeresultat ind i boksen
            forslagBoks.appendChild(forslag);
        }
    });


    // children.length fortæller hvor mange søgeresultater
    // der er blevet lagt ind i forslagBoks
    if (forslagBoks.children.length > 0)
    {
        // Hvis der er mindst ét resultat,
        // viser vi boksen med søgeresultater
        forslagBoks.style.display = "flex";
    }
    else
    {
        // Hvis ingen tråde matcher søgningen,
        // skjuler vi boksen
        forslagBoks.style.display = "none";
    }

});

</script>
</body>
</html>