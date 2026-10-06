<?php
require "database.php";
session_start();

$traad_id = $_GET["id"];

$stmt = $conn->prepare("SELECT * FROM Traade WHERE id = ?");
$stmt->bind_param("i", $traad_id);
$stmt->execute();
$result = $stmt->get_result();
$traad = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kommentar_indhold = $_POST["indhold"];
    $bruger_id = $_SESSION["bruger_id"];
    $kommentar_traad_id = $_POST["traad_id"];

    $stmt2 = $conn->prepare("INSERT INTO Kommentar (traad_id, bruger_id, indhold) VALUES (?, ?, ?)");
    $stmt2->bind_param("iis", $kommentar_traad_id, $bruger_id, $kommentar_indhold);
    $stmt2->execute();

    header("Location: vis_traad.php?id=" . $kommentar_traad_id);
    exit();
}
?>
<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Tråd</title>
</head>
<body>
    <?php include 'header.php'; ?>

    <main>
        <h1><?php echo $traad["titel"]; ?></h1>
        <p><?php echo $traad["indhold"]; ?></p>

        <form method="POST">
            <input type="hidden" name="traad_id" value="<?php echo $traad["id"]; ?>">
            <textarea name="indhold" required></textarea>
            <button type="submit">Send kommentar</button>
        </form>
        
<h3>Kommentarer</h3>
<?php
$stmt3 = $conn->prepare("SELECT * FROM Kommentar WHERE traad_id = ?");
$stmt3->bind_param("i", $traad_id);
$stmt3->execute();
$kommentarer = $stmt3->get_result();

while ($k = $kommentarer->fetch_assoc()) { 
?>
    <div class="kommentar">
        <p><?php echo htmlspecialchars($k["indhold"]); ?></p>
    </div>
<?php } ?>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>