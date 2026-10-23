<?php
// Åbner sessionen, så vi kan gemme, at brugeren er logget ind.
session_start();
// Er man allerede logget ind, giver login-siden ingen mening. Send videre til forum.
if (isset($_SESSION["bruger_id"])) {
    header("Location: forum.php");
    exit();
}
// Forbindelsen til databasen ($conn).
include 'database.php';

// Tom, indtil noget går galt. Vises i formularen, hvis den ikke er tom.
$fejl = "";

// Tjekker om formularen er blevet sendt med POST-metoden
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    // Det brugeren har skrevet i formularen.
    $Brugernavn = $_POST["Brugernavn"];
    $Adgangskoden = $_POST["Adgangskoden"];

    // Vi henter brugeren ud fra brugernavnet alene. Passwordet tjekkes bagefter i PHP med password_verify().
    $stmt = $conn->prepare("SELECT Brugernavn, Adgangskoden, id FROM ForumUsers WHERE Brugernavn = ?");
    $stmt->bind_param("s", $Brugernavn);
    $stmt->execute();
    $result = $stmt->get_result();

    // Vi tjekker, om databasen fandt en bruger.
    if ($result->num_rows > 0)
    {
        // Gemmer den fundne række i $user (fx $user["id"]).
        $user = $result->fetch_assoc();

        // Sammenligner det indtastede password med hash'en i databasen
        if (password_verify($Adgangskoden, $user["Adgangskoden"]))
        {
            // HER bliver brugeren logget ind: id'et gemmes i sessionen. Alle andre sider tjekker, om det findes.
            $_SESSION["bruger_id"] = $user["id"];

            // Send brugeren videre til forum
            header("Location: forum.php");
            exit();
        }
        else
        {
            // Samme fejlbesked i begge tilfælde (forkert password / ukendt brugernavn), så man ikke kan se, om et brugernavn findes.
            $fejl = "Brugernavn eller adgangskode er forkert";
        }
    }
    else
    {
        $fejl = "Brugernavn eller adgangskode er forkert";
    }
}
?>
<!DOCTYPE html>
<html lang="da">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Forbinder siden med vores fælles CSS-design -->
    <link rel="stylesheet" href="style.css">

    <title>Log ind</title>
</head>

<body>

    <!-- Menuen (header.php) indsættes her. -->
    <?php include 'header.php'; ?>

    <main class="signup-container">

        <div class="signup-box">

            <h1>Log ind</h1>

            <!-- POST sender oplysningerne til PHP-koden øverst, og de står ikke i adresselinjen. type="password" skjuler det, man skriver. -->
            <form method="POST">
                <input type="text" name="Brugernavn" placeholder="Brugernavn" required>
                <input type="password" name="Adgangskoden" placeholder="Adgangskode" required>
                <button type="submit">Log ind</button>

                <?php
                // Vis kun fejlbeskeden, hvis der er en.
                if ($fejl != "")
                {
                    echo '<p class="forkert-input">' . $fejl . '</p>';
                }
                ?>
            </form>

            <p class="skift-side">
                Har du ikke en konto? <a href="signup.php">Opret bruger</a>
            </p>

        </div>

    </main>

    <!-- Henter vores fælles footer -->
    <?php include 'footer.php'; ?>

</body>
</html>