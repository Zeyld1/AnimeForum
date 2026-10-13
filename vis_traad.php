<?php
require "database.php";
session_start();

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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["bruger_id"])) {    $kommentar_indhold = $_POST["indhold"];
    $bruger_id = $_SESSION["bruger_id"];
    $kommentar_traad_id = $_POST["traad_id"];

    $stmt2 = $conn->prepare("INSERT INTO Kommentar (traad_id, bruger_id, indhold) VALUES (?, ?, ?)");
    $stmt2->bind_param("iis", $kommentar_traad_id, $bruger_id, $kommentar_indhold);
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
            $stmt3 = $conn->prepare("
                SELECT Kommentar.indhold, ForumUsers.Brugernavn, ForumUsers.Profilbillede
                FROM Kommentar
                JOIN ForumUsers ON Kommentar.bruger_id = ForumUsers.id
                WHERE Kommentar.traad_id = ?
            ");
            $stmt3->bind_param("i", $traad_id);
            $stmt3->execute();
            $kommentarer = $stmt3->get_result();

            while ($k = $kommentarer->fetch_assoc()) {

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

            <p>
                <?php echo htmlspecialchars($k["indhold"]); ?>
            </p>
        </div>

    </div>

<?php } ?>

        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>