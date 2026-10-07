<!DOCTYPE html>
<html lang="da">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Forbinder siden med vores fælles CSS-design -->
    <link rel="stylesheet" href="style.css">

    <title>Opret Bruger</title>
</head>

<body>

    <?php include 'header.php'; ?>

    <?php

    include 'database.php';

    $Oprettet = "";
    $fejl = "";

    // Tjekker om formularen er blevet sendt med POST-metoden
    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        // Vi opretter forskellige variabler med det,
        // som brugeren har skrevet i inputfelterne.
        $Brugernavn = $_POST["Brugernavn"];
        $Email = $_POST["Email"];
        $Adgangskoden = $_POST["Adgangskoden"];


        // Brugernavn skal være mellem 3 og 20 tegn
        if (strlen($Brugernavn) < 3 || strlen($Brugernavn) > 20)
        {
            $fejl = "Brugernavn skal være mellem 3 og 20 tegn";
        }


        // Adgangskoden skal være mindst 8 tegn
        elseif (strlen($Adgangskoden) < 8)
        {
            $fejl = "Adgangskoden skal være mindst 8 tegn";
        }


        // Adgangskoden skal indeholde mindst et stort bogstav
        elseif (!preg_match('/[A-Z]/', $Adgangskoden))
        {
            $fejl = "Adgangskoden skal indeholde mindst et stort bogstav";
        }


        // Adgangskoden skal indeholde mindst et tal
        elseif (!preg_match('/[0-9]/', $Adgangskoden))
        {
            $fejl = "Adgangskoden skal indeholde mindst et tal";
        }


        else
        {
            // Vi tjekker først om emailen allerede findes i databasen
            $stmt = $conn->prepare("SELECT id FROM ForumUsers WHERE Email = ?");
            $stmt->bind_param("s", $Email);
            $stmt->execute();
            $result = $stmt->get_result();


            // Hvis vi finder mindst én bruger med emailen,
            // betyder det at emailen allerede bliver brugt
            if ($result->num_rows > 0)
            {
                $fejl = "Denne email bruges allerede. Brug en anden email.";
            }

            else
            {
                // Vi indsætter brugerens oplysninger i ForumUsers-tabellen.
                // ID behøver vi ikke skrive, fordi databasen selv laver det med AUTO_INCREMENT.

                $hash = password_hash($Adgangskoden, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO ForumUsers (Brugernavn, Email, Adgangskoden) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $Brugernavn, $Email, $hash);

if ($stmt->execute())
                {
                    $Oprettet = "Du er nu oprettet";
                }

                else
                {
                    $fejl = "Kunne ikke oprette en bruger: " . $stmt->error;
                }
            }
        }
    }

    ?>


    <main class="signup-container">

        <div class="signup-box">

            <h1>Opret bruger</h1>

            <form method="POST">

                <!-- required betyder at feltet ikke må være tomt -->
                <!-- minlength og maxlength bestemmer længden på brugernavnet -->
<input
    type="text"
    name="Brugernavn"
    placeholder="Brugernavn"
    minlength="3"
    maxlength="20"
    value="<?php echo isset($Brugernavn) ? htmlspecialchars($Brugernavn) : ''; ?>"    
    required
>

<input
    type="email"
    name="Email"
    placeholder="Email"
    value="<?php echo isset($Email) ? htmlspecialchars($Email) : ''; ?>"
    required
>

<input
    type="password"
    name="Adgangskoden"
    placeholder="Adgangskode"
    minlength="8"
    required
>


                <button type="submit">Opret bruger</button>


                <?php

                if ($Oprettet != "")
                {
                    echo '<p class="Bruger-oprettet">' . $Oprettet . '</p>';
                }

                ?>


                <?php

                if ($fejl != "")
                {
                    echo '<p class="forkert-input">' . $fejl . '</p>';
                }

                ?>

                       </form>

            <p class="skift-side">
                Har du allerede en konto? <a href="login.php">Log ind</a>
            </p>

        </div>

    </main>


    <!-- Henter vores fælles footer -->
    <?php include 'footer.php'; ?>

</body>

</html>