<?php session_start(); ?>
<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Privatlivspolitik</title>
</head>
<body>

    <?php include 'header.php'; ?>

    <main>
        <div class="opret-container">
            <h1>Privatlivspolitik</h1>

            <h2>Hvad gemmer vi?</h2>
            <p>
                Når du opretter en bruger, gemmer vi dit brugernavn, din e-mail og din adgangskode
                (som en hash, så vi ikke kan læse den). Du kan selv tilføje et profilbillede og en
                tekst om dig selv. Vi gemmer også dine tråde, dine kommentarer og tidspunktet for,
                hvornår du blev oprettet.
            </p>

            <h2>Hvorfor?</h2>
            <p>
                Oplysningerne bruges kun til at lade dig logge ind og deltage i forummet.
                Vi deler dem ikke med andre, og brugernavn, profilbillede, tråde og kommentarer
                er synlige for andre brugere.
            </p>

            <h2>Dine rettigheder</h2>
            <p>
                Du kan rette dit brugernavn, dit billede og din tekst under "Profil".
                Du kan kontakte os på kontakt@animeforum.dk, hvis du vil se, hvad vi har gemt om dig,
                eller have din bruger slettet.
            </p>
        </div>
    </main>

    <?php include 'footer.php'; ?>

</body>
</html>