<?php
require "database.php";
$result = $conn->query("SELECT id, titel, indhold FROM Traade");
?>
<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>

<main>
    <h1>Forum</h1>

    <a href="opret_traad.php">Opret Tråd</a>

    <?php while ($row = $result->fetch_assoc()) { ?>
        <div class="traad">
            <h2><a href="vis_traad.php?id=<?php echo $row["id"]; ?>"><?php echo $row["titel"]; ?></a></h2>
            <p><?php echo $row["indhold"]; ?></p>
        </div>
    <?php } ?>
</main>

<?php include 'footer.php'; ?>

</body>
</html>