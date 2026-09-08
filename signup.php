<link rel="design" href="style.css">
<?php include 'header.php'?>

<?php
include 'database.php';
 
if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    // Vi oprette foresklige variabler som brugere kan indtæste.
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Vi henter det input fra brugeren i vores intastafelter og inserter det i vores DB.
    $sql = "INSERT INTO ForumUsers (username, email, password)
            VALUES ('$username', '$email', '$password')";
    // her køre vi koden som står i $sql. Hvis koden lykkedes kommer svaret "bruger oprettet"
    // hvis der opstår fejl kommer melding "Fejl" 
    if ($conn->query($sql) === TRUE) {
        echo "Bruger oprettet!";
    } else {
        echo "Fejl: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sign up</title>
</head>

<body>

<h1>Opret bruger</h1>
<!--
Denne formular bruger POST-metoden til at sende brugerens oplysninger.
Når brugeren trykker på "Sign up", bliver værdierne fra inputfelterne
sendt til PHP, hvor de kan hentes med $_POST.
-->
<form method="POST">
    // vi definere metoden POST. 

    <input type="text" name="username" placeholder="Username">
    <br><br>

    <input type="email" name="email" placeholder="Email">
    <br><br>

    <input type="password" name="password" placeholder="Password">
    <br><br>

    <button type="submit">Sign up</button>

</form>

</body>
</html>