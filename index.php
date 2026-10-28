<?php session_start(); ?>
<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anime Forum</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="forside">

<!-- Menuen (header.php) indsættes her. -->
<?php include 'header.php'; ?>

<main>

    <!-- Forsidens velkomstområde med billede (CSS: .hero). -->
    <section class="hero">

        <h1>Velkommen til AniMecca</h1>

        <p>
            Diskuter dine yndlingsanime, find nye serier
            og del dine meninger med andre animefans.
        </p>

        <!-- Knap til signup.php. -->
        <a href="signup.php" class="hero-button">Opret bruger</a>

    </section>

</main>

<?php include 'footer.php'; ?>

</body>
</html>