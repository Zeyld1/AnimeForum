<!DOCTYPE html>
<html lang="da">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Forbinder siden med vores fælles CSS-design -->
    <link rel="stylesheet" href="style.css">

    <title>Sign up</title>
</head>

<body>

    
    <?php include 'header.php'; ?>

    <?php
   
    include 'database.php';

    // Tjekker om formularen er blevet sendt med POST-metoden
    if ($_SERVER["REQUEST_METHOD"] == "POST") 
        
        {

        // Vi opretter forskellige variabler med det,
        // som brugeren har skrevet i inputfelterne.
        $username = $_POST["username"];
        $email = $_POST["email"];
        $password = $_POST["password"];

        // Vi indsætter brugerens oplysninger i ForumUsers-tabellen.
        // ID behøver vi ikke skrive, fordi databasen selv laver det med AUTO_INCREMENT.
        $sql = "INSERT INTO ForumUsers (username, email, password)
                VALUES ('$username', '$email', '$password')";

        // Her kører vi SQL-koden.
        // Hvis den lykkes, får brugeren beskeden "Bruger oprettet!".
        // Hvis noget går galt, vises database-fejlen.
        if ($conn->query($sql) === TRUE) {
            echo "Bruger oprettet!";
        } else {
            echo "Fejl: " . $conn->error;
        }
    }
    ?>

    <main class="signup-container">

    <div class="signup-box">

        <h1>Opret bruger</h1>

        <form method="POST">
            <input type="text" name="username" placeholder="Username">

            <input type="email" name="email" placeholder="Email">

            <input type="password" name="password" placeholder="Password">

            <button type="submit">Sign up</button>
        </form>

    </div>

</main>

    <!-- Henter vores fælles footer -->
    <?php include 'footer.php'; ?>

</body>

</html>