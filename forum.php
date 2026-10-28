<?php
// session_start() åbner sessionen. Så kan siden (og header.php) se, om brugeren er logget ind.
session_start();
// Henter database.php, som laver forbindelsen $conn til MySQL-databasen.
require "database.php";
// Henter alle kategorier fra databasen. De bruges til menuen i sidebaren.
$kategorier = $conn->query("SELECT id, navn FROM Kategorier");
// Læser ?kategori=... fra adressen. Er der ingen, bliver $valgt tom (""), og alle tråde vises.
// ?? "" betyder: brug "" som standard, hvis der ikke står noget i adressen.
$valgt = $_GET["kategori"] ?? "";

// Her bygger vi SQL-sætningen op som tekst. Vi henter hver tråd sammen med forfatterens brugernavn og profilbillede.
// LEFT JOIN betyder: tråden vises også, selvom brugeren eller kategorierne mangler.
// GROUP_CONCAT samler trådens kategorier i én tekst, fx "anime, manga".
$sql = "
SELECT Traade.id, Traade.titel, Traade.indhold, Traade.oprettet, ForumUsers.Brugernavn,
       ForumUsers.Profilbillede,
       GROUP_CONCAT(Kategorier.navn SEPARATOR ', ') AS kategorier
    FROM Traade
    LEFT JOIN ForumUsers ON Traade.bruger_id = ForumUsers.id
    LEFT JOIN TraadKategorier ON Traade.id = TraadKategorier.traad_id
    LEFT JOIN Kategorier ON TraadKategorier.kategori_id = Kategorier.id
";

// Er der valgt en kategori, tilføjer vi et filter: kun tråde, der er koblet til den kategori i TraadKategorier.
// ? er en pladsholder, som får sin værdi længere nede (bind_param).
if ($valgt !== "") {
    $sql .= " WHERE Traade.id IN (SELECT traad_id FROM TraadKategorier WHERE kategori_id = ?)";
}

// GROUP BY gør, at hver tråd kun vises én gang (ellers kommer der én række pr. kategori).
// ORDER BY Traade.id DESC betyder, at den nyeste tråd står øverst.
$sql .= " GROUP BY Traade.id, Traade.oprettet, ForumUsers.Brugernavn, ForumUsers.Profilbillede ORDER BY Traade.id DESC";
// prepare() gør SQL'en klar. Værdierne sendes bagefter for sig selv, så brugerens input aldrig bliver til SQL-kode (beskytter mod SQL injection).
$stmt = $conn->prepare($sql);
// Har SQL'en et ?, skal det have en værdi. "i" betyder, at værdien er et heltal (kategoriens id).
if ($valgt !== "") {
    $stmt->bind_param("i", $valgt);
}
// Kører forespørgslen mod databasen.
$stmt->execute();
// Gemmer resultatet (alle tråde), så vi kan løbe dem igennem nede i HTML'en med fetch_assoc().
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
    <!-- Menuen (header.php) indsættes her. -->
    <?php include 'header.php'; ?>

<main>
    <div class="forum-layout">

        <!-- Sidebar til venstre: knap til at oprette tråd og kategorilinks. -->
        <aside class="sidebar">
            <a href="opret_traad.php" class="knap">Opret tråd</a>

            <nav class="sidebar-menu">
                <!-- "Alle" fjerner filteret, så alle tråde vises. -->
                <a href="forum.php">Alle</a>
                <!-- Laver ét link pr. kategori fra databasen. (int) sikrer, at id'et er et tal, og htmlspecialchars() beskytter mod XSS. -->
                <?php while ($kat = $kategorier->fetch_assoc()) { ?>
                    <a href="forum.php?kategori=<?php echo (int)$kat["id"]; ?>">
                        <?php echo htmlspecialchars($kat["navn"]); ?>
                    </a>
                <?php } ?>
            </nav>
        </aside>

         <!-- Højre side: søgefelt og trådene. -->
         <section class="forum-indhold">
            <div class="forum-top">

    <h1>Forum</h1>

    <!-- Søgefeltet. Selve søgningen er JavaScript nederst på siden. -->
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

            <!-- Går igennem alle tråde. fetch_assoc() henter én række ad gangen, indtil der ikke er flere. -->
            <?php while ($row = $result->fetch_assoc()) { ?>

            <?php

                      // Har brugeren ikke et profilbillede, bruger vi standardbilledet (images/Ikon.png).
                      if (!empty($row["Profilbillede"]))
                         {
                            $avatar = $row["Profilbillede"];
                         }
                        else
                         {
                            $avatar = "images/Ikon.png";
                         }

?>
                <!-- Ét tråd-kort. Alt fra databasen vises med htmlspecialchars() for at beskytte mod XSS. -->
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
                <!-- ?? betyder: findes brugernavnet ikke (brugeren er slettet), vises "Slettet bruger". -->
                Af <?php echo htmlspecialchars($row["Brugernavn"] ?? "Slettet bruger"); ?>
                <!-- strtotime() + date() laver tidspunktet fra databasen om til fx 22/10/2026 14:30. -->
                den <?php echo date("d/m/Y H:i", strtotime($row["oprettet"])); ?>
            </p>

            <p>
                <?php echo htmlspecialchars($row["indhold"]); ?>
            </p>

            <!-- Vis kun kategorier, hvis tråden har nogen. explode() deler teksten "anime, manga" op i en liste, og foreach laver ét tag pr. kategori. -->
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

<!-- JavaScript til søgefeltet. Kører i browseren (ikke på serveren) og søger kun i trådenes titler. -->
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