<?php
session_start();
if (isset($_SESSION["bruger_id"])) {
    header("Location: forum.php");
    exit();
}
include 'database.php';

$fejl = "";

// Tjekker om formularen er blevet sendt med POST-metoden
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $Brugernavn = $_POST["Brugernavn"];
    $Adgangskoden = $_POST["Adgangskoden"];

    $stmt = $conn->prepare("SELECT Brugernavn, Adgangskoden, id FROM ForumUsers WHERE Brugernavn = ?");
    $stmt->bind_param("s", $Brugernavn);
    $stmt->execute();
    $result = $stmt->get_result();

    // Vi tjekker, om databasen fandt en bruger.
    if ($result->num_rows > 0)
    {
        $user = $result->fetch_assoc();

        // Sammenligner det indtastede password med hash'en i databasen
        if (password_verify($Adgangskoden, $user["Adgangskoden"]))
        {
            $_SESSION["bruger_id"] = $user["id"];

            // Send brugeren videre til forum
            header("Location: forum.php");
            exit();
        }
        else
        {
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

    <?php include 'header.php'; ?>

    <main class="signup-container">

        <div class="signup-box">

            <h1>Log ind</h1>

            <form method="POST">
                <input type="text" name="Brugernavn" placeholder="Brugernavn" required>
                <input type="password" name="Adgangskoden" placeholder="Adgangskode" required>
                <button type="submit">Log ind</button>

                <?php
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
<?php var_dump($_SESSION); ?>
</html>