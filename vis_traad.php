<?php
require "database.php";
session_start();


// ---------- FUNKTION: skriver én kommentar ----------
// Bruges både til hovedkommentarer og til svar,
// så vi kun har koden ét sted.
// $k       = kommentaren (en række fra databasen)
// $hoved_id = id på hovedkommentaren (bruges når man trykker "Svar")
function skriv_kommentar($k, $hoved_id)
{
    // Hvis brugeren ikke har et billede, bruger vi standardbilledet
    if (!empty($k["Profilbillede"]))
    {
        $kommentar_avatar = $k["Profilbillede"];
    }
    else
    {
        $kommentar_avatar = "images/Ikon.png";
    }
    ?>

    <div class="kommentar">

        <img
            src="<?php echo htmlspecialchars($kommentar_avatar); ?>"
            class="kommentar-avatar"
            alt="Profilbillede"
        >

        <div class="kommentar-indhold">
            <strong>
                <?php echo htmlspecialchars($k["Brugernavn"]); ?>
            </strong>

            <span class="kommentar-dato">
                <?php echo date("d/m/Y H:i", strtotime($k["oprettet"])); ?>
            </span>

            <p>
                <?php echo htmlspecialchars($k["indhold"]); ?>
            </p>

            <!-- Svar-knap: kun hvis brugeren er logget ind -->
            <?php if (isset($_SESSION["bruger_id"])) { ?>

                <details class="svar-boks">
                    <summary>Svar</summary>

                    <form method="POST" class="kommentar-form">
                        <input type="hidden" name="traad_id" value="<?php echo (int)$_GET["id"]; ?>">
                        <input type="hidden" name="svar_til_id" value="<?php echo (int)$hoved_id; ?>">
                        <textarea name="indhold" rows="3" placeholder="Skriv et svar..." required></textarea>
                        <button type="submit">Send svar</button>
                    </form>
                </details>

            <?php } ?>
        </div>

    </div>

    <?php
}


$traad_id = $_GET["id"];

$stmt = $conn->prepare("
    SELECT Traade.*, ForumUsers.Brugernavn,
           GROUP_CONCAT(Kategorier.navn SEPARATOR ', ') AS kategorier
    FROM Traade
    LEFT JOIN ForumUsers ON Traade.bruger_id = ForumUsers.id
    LEFT JOIN TraadKategorier ON Traade.id = TraadKategorier.traad_id
    LEFT JOIN Kategorier ON TraadKategorier.kategori_id = Kategorier.id
    WHERE Traade.id = ?
    GROUP BY Traade.id, ForumUsers.Brugernavn
");
$stmt->bind_param("i", $traad_id);
$stmt->execute();
$result = $stmt->get_result();
$traad = $result->fetch_assoc();

// Henter billedet fra den bruger der har lavet tråden
$stmt_billede = $conn->prepare("SELECT Profilbillede FROM ForumUsers WHERE id = ?");
$stmt_billede->bind_param("i", $traad["bruger_id"]);
$stmt_billede->execute();
$billede_result = $stmt_billede->get_result();
$forfatter = $billede_result->fetch_assoc();

// Hvis brugeren ikke har et billede, bruger vi standardbilledet
if (!empty($forfatter["Profilbillede"]))
{
    $traad_avatar = $forfatter["Profilbillede"];
}
else
{
    $traad_avatar = "images/Ikon.png";
}


// ---------- NY KOMMENTAR ELLER NYT SVAR ----------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["bruger_id"]))
{
    $kommentar_indhold = $_POST["indhold"];
    $bruger_id = $_SESSION["bruger_id"];
    $kommentar_traad_id = $_POST["traad_id"];

    // Hvis formularen har et svar_til_id, er det et svar.
    // Ellers er det en almindelig kommentar (så er svar_til_id tomt).
    $svar_til_id = null;

    if (!empty($_POST["svar_til_id"]))
    {
        $svar_til_id = $_POST["svar_til_id"];
    }

    $stmt2 = $conn->prepare("INSERT INTO Kommentar (traad_id, bruger_id, indhold, svar_til_id) VALUES (?, ?, ?, ?)");
    $stmt2->bind_param("iisi", $kommentar_traad_id, $bruger_id, $kommentar_indhold, $svar_til_id);
    $stmt2->execute();

    header("Location: vis_traad.php?id=" . $kommentar_traad_id);
    exit();
}
?>
<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Tråd</title>
</head>
<body>
    <?php include 'header.php'; ?>

    <main>
        <div class="traad-side">

            <div class="traad-hoved">

                <img src="<?php echo htmlspecialchars($traad_avatar); ?>" class="traad-avatar">

                <div class="traad-indhold">
                    <h1><?php echo htmlspecialchars($traad["titel"]); ?></h1>
                    <p class="traad-forfatter">
                        Af <?php echo htmlspecialchars($traad["Brugernavn"] ?? "Slettet bruger"); ?>
                        den <?php echo date("d/m/Y H:i", strtotime($traad["oprettet"])); ?>
                    </p>
                    <p><?php echo htmlspecialchars($traad["indhold"]); ?></p>

                    <?php if (!empty($traad["kategorier"])) { ?>
                        <div class="traad-kategorier">
                            <?php foreach (explode(", ", $traad["kategorier"]) as $navn) { ?>
                                <span class="kategori-tag"><?php echo htmlspecialchars($navn); ?></span>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>

            </div>

            <?php if (isset($_SESSION["bruger_id"])) { ?>

                <form method="POST" class="kommentar-form">
                    <input type="hidden" name="traad_id" value="<?php echo (int)$traad["id"]; ?>">
                    <textarea name="indhold" rows="4" placeholder="Skriv en kommentar..." required></textarea>
                    <button type="submit">Send kommentar</button>
                </form>

            <?php } else { ?>

                <p><a href="login.php">Log ind</a> for at skrive en kommentar.</p>

            <?php } ?>

            <h3>Kommentarer</h3>

            <?php
            // Henter ALLE kommentarer i tråden (både hovedkommentarer og svar)
            $stmt3 = $conn->prepare("
                SELECT Kommentar.id, Kommentar.indhold, Kommentar.oprettet, Kommentar.svar_til_id,
                       ForumUsers.Brugernavn, ForumUsers.Profilbillede
                FROM Kommentar
                JOIN ForumUsers ON Kommentar.bruger_id = ForumUsers.id
                WHERE Kommentar.traad_id = ?
                ORDER BY Kommentar.oprettet ASC
            ");
            $stmt3->bind_param("i", $traad_id);
            $stmt3->execute();
            $alle = $stmt3->get_result()->fetch_all(MYSQLI_ASSOC);

            // Går igennem alle kommentarer
            foreach ($alle as $k)
            {
                // Er det en hovedkommentar? (den svarer ikke på noget)
                if ($k["svar_til_id"] === null)
                {
                    skriv_kommentar($k, $k["id"]);

                    // Her kommer de svar der hører til denne kommentar
                    echo '<div class="svar-liste">';

                    foreach ($alle as $svar)
                    {
                        if ($svar["svar_til_id"] == $k["id"])
                        {
                            skriv_kommentar($svar, $k["id"]);
                        }
                    }

                    echo '</div>';
                }
            }
            ?>

        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>