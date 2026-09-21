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

    <?php
   
    include 'database.php';

    $fejl="";

    // Tjekker om formularen er blevet sendt med POST-metoden
    if ($_SERVER["REQUEST_METHOD"] == "POST") 
        
        {
        // vi definere de to variabler da vi bruger dem i skrive felter med deres name
        $Brugernavn = $_POST["Brugernavn"];
        $Adgangskoden = $_POST["Adgangskoden"];
        // Her selecter vi den adgangskode og brugernavn fra den bruger som brugeren skriver.
        // derfor bruger vi WHERE = Brugernavn
        $sql = "SELECT Brugernavn, Adgangskoden FROM ForumUsers WHERE Brugernavn = '$Brugernavn'";
        

        // Vi kører SQL-koden og gemmer resultatet i $result.
        $result = $conn->query($sql);

    // Vi tjekker, om databasen fandt en bruger.   
    if ($result->num_rows > 0) 
    {
        // Vi henter den fundne række fra databasen og gemmer den i variablen $user.
        $user = $result->fetch_assoc();
  
        // nu kan vi sammenligne vores adgangskoden som brugeren skrev
        // med databasens adgangskoden
        if ($Adgangskoden == $user["Adgangskoden"]) {
            echo "Du er logget ind!";
        } else {
            $fejl="Brugernavn eller adgangskode er forkert";
        }

    } else {
            $fejl="Brugernavn eller adgangskode er forkert";
    }
}
    ?>

    <main class="signup-container">

    <div class="signup-box">

        <h1>log ind</h1>

        <form method="POST">
            <input type="text" name="Brugernavn" placeholder="Brugernavn">
            <input type="password" name="Adgangskoden" placeholder="Adgangskode">
            <button type="submit">log ind</button>
            <?php
                // ! betyder ikke
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