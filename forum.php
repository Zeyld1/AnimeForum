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
    <p>Her kommer forum-opslagene.</p>

    <a href="opret_traad.php">Opret Tråd</a>

    <?php while ($row = $result->fetch_assoc()) { ?>
        <div class="traad">
            <h2><?php echo $row["titel"]; ?></h2>
            <p><?php echo $row["indhold"]; ?></p>
        </div>
    <?php } ?>
</main>

<?php include 'footer.php'; ?>

</body>
</html>