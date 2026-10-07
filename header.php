<header class="top-header">

    <a href="Profil.php" class="profil-link">Profil</a>

    <h1><a href="index.php">AniMecca </a></h1>

    <nav class="menu-tekst">
        <a href="forum.php">Forum</a>

        <?php if (isset($_SESSION["bruger_id"])) { ?>
            <a href="Profil.php">Profil</a>
            <a href="logout.php">Log ud</a>
        <?php } else { ?>
            <a href="login.php">Login</a>
            <a href="signup.php">Opret Bruger</a>
        <?php } ?>
    </nav>

</header>