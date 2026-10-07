<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    

    <!-- Forbinder siden med vores fælles CSS-design -->
    <link rel="stylesheet" href="style.css">

    <title>Document</title>
</head>
<body class="profil-body">
    <?php include 'header.php'; ?>
    <main>
        <div class="profil-container">
        <h1>Min Profil</h1>

        <p>
            Avatar
            Tilladte Formater: JPEG og PNG. 
            
            <img src="images/Ikon.png" alt="Profilbillede">
         </p>
          <p>
            Brugernavn : Monkey
         </p>

         <p>
            Om mig: Jeg elsker anime og manga.
         </p>

         <button>Rediger profil</button>
        </div>
     </main>
     <?php include 'footer.php'; ?>

</body>
</html>