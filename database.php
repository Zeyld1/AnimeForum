<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php

$host = "mysql111.unoeuro.com";
$username = "onepieceisreal_dk";
$password = "anxGhbrmFB4eRzkc369D";
$database = "onepieceisreal_dk_db";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Forbindelsen fejlede: " . $conn->connect_error);
}

?>
</body>
</html>