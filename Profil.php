<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Forbinder siden med vores fælles CSS-design -->
    <link rel="stylesheet" href="style.css">

    <title>Min Profil</title>
</head>

<body class="profil-body">

    <?php include 'header.php'; ?>

    <main>

        <div class="profil-container">

             <h1>Min Profil</h1>

             <p>Avatar</p>

             <p class="profil-info">
                Tilladte formater: JPEG og PNG
             </p>

             <img src="images/Ikon.png" alt="Profilbillede" class="profil-billede">

             <form method="POST" enctype="multipart/form-data" class="upload-form">

                <input
                    type="file"
                    name="Profilbillede"
                    accept=".jpg,.jpeg,.png"
                 >

                 <button type="submit" class="upload-button">
                    Upload billede
                 </button>

                 </form>

             <p>
                Brugernavn: Monkey
             </p>

             <p>
                Om mig: Jeg elsker anime og manga.
             </p>

             <button class="rediger-button">
                Rediger profil
             </button>

         </div>

     </main>

    <?php include 'footer.php'; ?>

</body>
</html>