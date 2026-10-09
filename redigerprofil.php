
<?php

session_start();

include 'database.php';

// Hvis brugeren ikke er logget ind,
// sender vi personen til login-siden
if (!isset($_SESSION["bruger_id"]))
{
    header("Location: login.php");
    exit();
}

// Vi gemmer id'et på den bruger som er logget ind
$bruger_id = $_SESSION["bruger_id"];

// Hvis brugeren har valgt et profilbillede
if (
    isset($_FILES["Profilbillede"]) &&
    $_FILES["Profilbillede"]["error"] == 0
)
{
    $filnavn = $_FILES["Profilbillede"]["name"];
    $midlertidig_fil = $_FILES["Profilbillede"]["tmp_name"];

    $mappe = "Uploades/";
    $sti = $mappe . $filnavn;

    move_uploaded_file($midlertidig_fil, $sti);

    // Gemmer stien til billedet i databasen
    $sql = "UPDATE ForumUsers
            SET Profilbillede = '$sti'
            WHERE id = '$bruger_id'";

    $conn->query($sql);
}

// Hvis brugeren har trykket på Gem-knappen
if (isset($_POST["gem_ommig"]))
{
    // Vi gemmer det som brugeren har skrevet
    $OmMig = $_POST["OmMig"];

    // Vi opdaterer OmMig i databasen
    $sql = "UPDATE ForumUsers
            SET OmMig = '$OmMig'
            WHERE id = '$bruger_id'";

    $conn->query($sql);
}

$fejl = "";

// Hvis brugeren har trykket på "Gem brugernavn"
if (isset($_POST["gem_brugernavn"]))
{
    // trim() fjerner mellemrum i begyndelsen og slutningen
    $nytBrugernavn = trim($_POST["Brugernavn"]);

    if ($nytBrugernavn == "")
    {
        $fejl = "Brugernavnet må ikke være tomt.";
    }
    else
    {
        // Tjekker om en ANDEN bruger allerede har det navn
        $stmt = $conn->prepare(
            "SELECT id FROM ForumUsers WHERE Brugernavn = ? AND id != ?"
        );
        $stmt->bind_param("si", $nytBrugernavn, $bruger_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0)
        {
            $fejl = "Brugernavnet er allerede i brug.";
        }
        else
        {
            $stmt = $conn->prepare(
                "UPDATE ForumUsers SET Brugernavn = ? WHERE id = ?"
            );
            $stmt->bind_param("si", $nytBrugernavn, $bruger_id);
            $stmt->execute();
        }
    }
}


// Henter den bruger fra databasen som er logget ind
$sql = "SELECT * FROM ForumUsers WHERE id = '$bruger_id'";

$result = $conn->query($sql);


// Gemmer brugerens oplysninger
if ($result->num_rows > 0)
{
    $user = $result->fetch_assoc();
}

// Hvis brugeren ikke har et billede, bruger vi standardbilledet
if ($user["Profilbillede"] == "")
{
    $profilbillede = "images/Ikon.png";
}
else
{
    $profilbillede = $user["Profilbillede"];
}

?>
<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Forbinder siden med vores fælles CSS-design -->
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="profil.css">

    <title>Min Profil</title>
</head>

<body class="profil-body">

    <?php include 'header.php'; ?>

    <main>

        <div class="profil-container">

            <!-- Klik på billedet for at vælge et nyt. Formularen sendes automatisk. -->
            <form method="POST" enctype="multipart/form-data" class="avatar-form">

                <label class="avatar-wrap" for="profilbillede-input">

                    <img
                        src="<?php echo htmlspecialchars($profilbillede); ?>"
                        alt="Dit profilbillede"
                        class="profil-billede">

                    <span class="avatar-edit" title="Skift profilbillede">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                            <circle cx="12" cy="13" r="4"/>
                        </svg>
                    </span>

                </label>

                <input
                    type="file"
                    id="profilbillede-input"
                    class="skjult-input"
                    name="Profilbillede"
                    accept=".jpg,.jpeg,.png"
                    onchange="this.form.submit()">

            </form>

<form method="POST" class="brugernavn-form">

    <label for="Brugernavn">Brugernavn</label>

    <input
        type="text"
        id="Brugernavn"
        name="Brugernavn"
        class="brugernavn-felt"
        maxlength="30"
        value="<?php echo htmlspecialchars($user["Brugernavn"]); ?>">

    <?php if ($fejl != "") { ?>
        <p class="fejl-besked"><?php echo $fejl; ?></p>
    <?php } ?>

    <button type="submit" name="gem_brugernavn" class="gem-button">
        Gem brugernavn
    </button>

</form>
            <p class="profil-info">
                Klik på billedet for at skifte. Tilladte formater: JPEG og PNG
            </p>

            <form method="POST" class="ommig-form">

                <label for="OmMig">Om mig</label>

                <textarea
                    id="OmMig"
                    name="OmMig"
                    class="om-mig-felt"
                    placeholder="Skriv lidt om dig selv..."
                ><?php echo htmlspecialchars($user["OmMig"]); ?></textarea>

                <button type="submit" name="gem_ommig" class="gem-button">
                    Gem ændringer
                </button>

            </form>

            <button type="button" class="rediger-button">
                Rediger profil
            </button>

        </div>

    </main>

    <?php include 'footer.php'; ?>

</body>
</html>