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

    $Oprettet= "";
    $fejl="";

    // Tjekker om formularen er blevet sendt med POST-metoden
if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    // Vi opretter forskellige variabler med det,
    // som brugeren har skrevet i inputfelterne.
    $Brugernavn = $_POST["Brugernavn"];
    $Email = $_POST["Email"];
    $Adgangskoden = $_POST["Adgangskoden"];

    // hvis et felt er tom
    if ($Brugernavn == "" || $Email == "" || $Adgangskoden == "") 
    {
        $fejl = "Alle felter skal udfyldes";
    } 
    else
    {
        // Vi indsætter brugerens oplysninger i ForumUsers-tabellen.
        // ID behøver vi ikke skrive, fordi databasen selv laver det med AUTO_INCREMENT.

        $sql = "INSERT INTO ForumUsers (Brugernavn, Email, Adgangskoden)
        VALUES ('$Brugernavn', '$Email', '$Adgangskoden')";
        
        // Her kører vi SQL-koden.
        // Hvis den lykkes, får brugeren beskeden "Bruger oprettet!".
        // Hvis noget går galt, vises database-fejlen.
        if ($conn->query($sql) === TRUE)
        {
            $Oprettet =" Du er nu oprettet";
        } 
        else 
        {
            $fejl="kunne ikke oprette en bruger";
        }
    }
}
?>

    <main class="signup-container">

    <div class="signup-box">

        <h1>Opret bruger</h1>

        <form method="POST">

    <input type="text" name="Brugernavn" placeholder="Brugernavn" required>

    <input type="email" name="Email" placeholder="Email"required>

    <input type="password" name="Adgangskoden" placeholder="Adgangskode"required>

    <button type="submit">Opret bruger</button>
    <?php
        if ($Oprettet!= "")   
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

    </div>

</main>

    <!-- Henter vores fælles footer -->
    <?php include 'footer.php'; ?>

</body>

</html>