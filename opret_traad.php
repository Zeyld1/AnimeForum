<?php
session_start();
require "connect.php"; // sørg for at stien passer til din forbindelsesfil

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titel = $_POST["titel"];
    $indhold = $_POST["indhold"];
    $kategori_id = $_POST["kategori_id"];
    $bruger_id = $_SESSION["bruger_id"]; // kræver at brugeren er logget ind

    $stmt = $conn->prepare("INSERT INTO Traade (bruger_id, kategori_id, titel, indhold) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $bruger_id, $kategori_id, $titel, $indhold);
    $stmt->execute();

    header("Location: forum.php");
    exit();
}
?>