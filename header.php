<!-- Menuen. Indsættes på alle sider med include 'header.php'. Siden skal have kaldt session_start() først. -->
<header class="top-header">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Profil-linket står altid i højre hjørne (CSS: .profil-link). Er man ikke logget ind, sender minprofil.php en videre til login. -->
    <a href="minprofil.php" class="profil-link">Profil</a>

    <h1><a href="index.php">AniMecca </a></h1>

    <nav class="menu-tekst">
        <a href="forum.php">Forum</a>

        <!-- Er man logget ind, vises "Log ud". Ellers vises "Login" og "Opret Bruger". -->
        <?php if (isset($_SESSION["bruger_id"])) { ?>
            <a href="logout.php">Log ud</a>
        <?php } else { ?>
            <a href="login.php">Login</a>
            <a href="signup.php">Opret Bruger</a>
        <?php } ?>
    </nav>

</header>