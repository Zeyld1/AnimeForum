<?php
// Åbner den nuværende session, så vi kan slette den.
session_start();
// Sletter sessionen, så bruger_id forsvinder. Brugeren er nu en gæst.
session_destroy();

// Send videre til forum-siden. exit() stopper koden.
header("Location: forum.php");
exit();