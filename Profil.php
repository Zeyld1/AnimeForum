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
if (isset($_FILES["Profilbillede"]))
{
    $filnavn = $_FILES["Profilbillede"]["name"];
    $midlertidig_fil = $_FILES["Profilbillede"]["tmp_name"];

    // Vi bestemmer hvor billedet skal gemmes
    $mappe = "Uploades/";
    $sti = $mappe . $filnavn;

    // Flytter billedet fra den midlertidige placering til vores Uploads-mappe
    move_uploaded_file($midlertidig_fil, $sti);
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


// Henter den bruger fra databasen som er logget ind
$sql = "SELECT * FROM ForumUsers WHERE id = '$bruger_id'";

$result = $conn->query($sql);


// Gemmer brugerens oplysninger
if ($result->num_rows > 0)
{
    $user = $result->fetch_assoc();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Forbinder siden med vores fælles CSS-design -->
    <link rel="stylesheet" href="style.css">

    <title>Min Profil</title>
</head>

<body class="profil-body">

    <?php include 'header.php'; ?>

    <main>

        <div class="profil-container">

             <h1>Min Profil</h1>

             <p>Avatar</p>

             <p class="profil-info">
                Tilladte formater: JPEG og PNG
             </p>

             <img src="images/Ikon.png" alt="Profilbillede" class="profil-billede">

             <form method="POST" enctype="multipart/form-data" class="upload-form">

                 <input
                    type="file"
                    name="Profilbillede"
                    accept=".jpg,.jpeg,.png">

                 <button type="submit" class="upload-button">
                    Upload billede
                 </button>

             </form>

             <form method="POST">

             <textarea
             name="OmMig"
             class="om-mig-felt"
             placeholder="Skriv lidt om dig selv..."
             ><?php echo $user["OmMig"]; ?></textarea>

             <button type="submit" name="gem_ommig">
              Gem
             </button>

            </form>

             <p>
                 Brugernavn: <?php echo $user["Brugernavn"]; ?>
             </p>

             <button class="rediger-button">
                Rediger profil
             </button>

         </div>

     </main>

    <?php include 'footer.php'; ?>

</body>
</html>