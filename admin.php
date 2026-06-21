<?php
session_start();

// Redirect to login page if user is not logged in as admin
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'Administrateur') {
    header('Location: admin_connexion.php');
    exit();
}

// Database connection
$connexion = mysqli_connect('localhost', 'arahalinony', 'sae23.blagnac', 'SAE23_V2');
$message = "";

// Add a new room from the POST form
if (isset($_POST['ajouter_salle'])) {
    $nom_salle = mysqli_real_escape_string($connexion, $_POST['nom_salle']);
    
    // Extract the building ID from the first letter of the room name (e.g. 'E' from 'E208')
    $id_bat = strtoupper($nom_salle[0]); 
    
    // Auto-create the building if it doesn't exist yet
    $nom_bat_auto = "Batiment " . $id_bat;
    mysqli_query($connexion, "INSERT IGNORE INTO Batiment (id_bat, nom_bat) VALUES ('$id_bat', '$nom_bat_auto')");
    
    // Check if the room already exists before inserting
    $verif = mysqli_query($connexion, "SELECT * FROM Salle WHERE nom_salle='$nom_salle'");
    if (mysqli_num_rows($verif) == 0) {
        $query = "INSERT INTO Salle (nom_salle, id_bat) VALUES ('$nom_salle', '$id_bat')";
        
        if (mysqli_query($connexion, $query)) {
            $message = "<p class='msg-success'>La salle " . htmlspecialchars($nom_salle) . " a été ajoutée avec succès au bâtiment " . htmlspecialchars($id_bat) . " !</p>";
        } else {
            $message = "<p class='msg-error'>Erreur lors de l'insertion de la salle dans la base de données.</p>";
        }
    } else {
        $message = "<p class='msg-error'>Cette salle est déjà suivie sur le site.</p>";
    }
}

// Delete a room from the GET parameter (CASCADE removes related sensors and measures)
if (isset($_GET['supprimer'])) {
    $salle_a_supprimer = mysqli_real_escape_string($connexion, $_GET['supprimer']);
    mysqli_query($connexion, "DELETE FROM Salle WHERE nom_salle='$salle_a_supprimer'");
    $message = "<p class='msg-warning'>La salle " . htmlspecialchars($salle_a_supprimer) . " a été retirée du site.</p>";
}

// Fetch all rooms to display in the table
$liste_salles = mysqli_query($connexion, "SELECT * FROM Salle");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Administration</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="CZAPLA VICTOR, MASSIOT CLEMENT, RAHALINONY-NUNES, MEHDI-NAFAA">
    <meta name="description" content="SAe23 - Supervision des capteurs IUT de Blagnac">
    <link rel="stylesheet" href="styles-css/style2RWD.css">
</head>
<body>
    <header>
        <h1>Administration : Gestion des Salles</h1>
        <nav class="navbar1">
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="#" class="couleur1">Administration</a></li>
                <li><a href="gestion_connexion.php">Gestion</a></li>
                <li><a href="consultation.php">Consultation</a></li> 
                <li><a href="gestion_proj.html">Gestion de projet</a></li>
                <li><a href="deconnexion.php" class="btn-deconnexion">Déconnexion</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <?php echo $message; ?>
        
        <section class="box-noir">
            <h2>Ajouter une salle à afficher</h2>
            <form action="admin.php" method="post" class="form-admin">
                <p>
                    <label for="nom_salle">Nom de la salle (ex: E208) : </label>
                    <input type="text" name="nom_salle" id="nom_salle" required size="20" />
                </p>
                <p>
                    <input type="submit" name="ajouter_salle" value="Valider l'ajout" />
                </p>
            </form>
        </section>

        <section class="box-noir section-margin-top">
            <h2>Salles affichées sur le site</h2>
            <div class="container-padding-80">
                <table>
                    <thead>
                        <tr>
                            <th>Nom de la Salle</th>
                            <th>Bâtiment</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($salle = mysqli_fetch_assoc($liste_salles)) { ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($salle['nom_salle']); ?></strong></td>
                            <td>Bâtiment <?php echo htmlspecialchars($salle['id_bat']); ?></td>
                            <td>
                                <a href="admin.php?supprimer=<?php echo urlencode($salle['nom_salle']); ?>" class="text-alert-error font-weight-bold">Supprimer</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <aside id="last">
        <hr>
        <p><em> Validation de la page HTML5 - CSS3 </em></p>
            <img class="badge" src="images/html5-validator-badge-blue.png" alt="HTML5 Valide !">
            <img class="badge" src="http://jigsaw.w3.org/css-validator/images/vcss-blue" alt="CSS Valide !">
    </aside>

    <footer>
        <ul>
            <li>IUT de Blagnac</li>
            <li>Département Réseaux et Télécommunications</li>
            <li>BUT1</li>
        </ul>  
    </footer>
    
    <?php // Close database connection
    mysqli_close($connexion); ?>
</body>
</html>