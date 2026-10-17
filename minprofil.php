<?php
session_start();

include 'database.php';

// Hvis brugeren ikke er logget ind, sender vi personen til login-siden
if (!isset($_SESSION["bruger_id"]))
{
    header("Location: login.php");
    exit();
}

$bruger_id = $_SESSION["bruger_id"];


// ---------- 1. Hent brugerens oplysninger ----------
$stmt = $conn->prepare("SELECT Brugernavn, Profilbillede, OmMig FROM ForumUsers WHERE id = ?");
$stmt->bind_param("i", $bruger_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Hvis brugeren ikke har et billede, bruger vi standardbilledet
if (!empty($user["Profilbillede"]))
{
    $profilbillede = $user["Profilbillede"];
}
else
{
    $profilbillede = "images/Ikon.png";
}


// ---------- 2. Hent de tråde brugeren har lavet ----------
$stmt2 = $conn->prepare("SELECT id, titel, indhold FROM Traade WHERE bruger_id = ? ORDER BY id DESC");
$stmt2->bind_param("i", $bruger_id);
$stmt2->execute();
$traade = $stmt2->get_result();

// Antal tråde
$antal_traade = $traade->num_rows;
?>
<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Min profil</title>
</head>
<body>

    <?php include 'header.php'; ?>

    <main>
        <div class="minprofil-side">

            <!-- Profilkort: billede, navn, om mig og knap -->
            <div class="minprofil-kort">

                <img src="<?php echo htmlspecialchars($profilbillede); ?>"
                     alt="Profilbillede" class="minprofil-avatar">

                <div class="minprofil-info">
                    <h1><?php echo htmlspecialchars($user["Brugernavn"]); ?></h1>

                    <p class="minprofil-ommig">
                        <?php
                        if (!empty($user["OmMig"]))
                        {
                            echo nl2br(htmlspecialchars($user["OmMig"]));
                        }
                        else
                        {
                            echo "Du har ikke skrevet noget om dig selv endnu.";
                        }
                        ?>
                    </p>
                    <a href="redigerprofil.php" class="minprofil-knap">Rediger profil</a>
                </div>

            </div>


            <!-- De tråde brugeren har lavet -->
            <h2 class="minprofil-overskrift">Mine tråde (<?php echo $antal_traade; ?>)</h2>

            <?php if ($antal_traade == 0) { ?>

                <p>
                    Du har ikke postet nogen tråde endnu.
                    <a href="opret_traad.php">Opret din første tråd</a>
                </p>

            <?php } else { ?>

                <?php while ($t = $traade->fetch_assoc()) { ?>

                    <div class="traad">
                        <h2>
                            <a href="vis_traad.php?id=<?php echo (int)$t["id"]; ?>">
                                <?php echo htmlspecialchars($t["titel"]); ?>
                            </a>
                        </h2>
                        <p class="traad-uddrag"><?php echo htmlspecialchars($t["indhold"]); ?></p>
                    </div>

                <?php } ?>

            <?php } ?>

        </div>
    </main>

    <?php include 'footer.php'; ?>

</body>
</html>