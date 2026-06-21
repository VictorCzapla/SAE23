<?php
// Database connection
$connexion = mysqli_connect('localhost', 'arahalinony', 'sae23.blagnac', 'SAE23_V2');

// Fetch each room with its building and latest sensor measurement
$requete_salles = mysqli_query($connexion, "
    SELECT s.nom_salle, s.id_bat, m.valeur, m.heure, m.date_mesure 
    FROM Salle s
    LEFT JOIN Capteur c ON s.nom_salle = c.nom_salle
    LEFT JOIN Mesure m ON c.nom_capteur = m.nom_capteur
    WHERE m.id_mesure = (
        SELECT MAX(m2.id_mesure) 
        FROM Mesure m2 
        WHERE m2.nom_capteur = c.nom_capteur
    ) OR m.id_mesure IS NULL
    GROUP BY s.nom_salle
");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Consultation</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="CZAPLA VICTOR, MASSIOT CLEMENT, RAHALINONY-NUNES, MEHDI-NAFAA">
    <meta name="description" content="SAe23 - Supervision des capteurs IUT de Blagnac">
    <link rel="stylesheet" href="styles-css/style2RWD.css">
</head>
<body>
    <header>
        <h1>Consultation des Salles Activées</h1>
        <nav class="navbar1">
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="admin_connexion.php">Administration</a></li>
                <li><a href="gestion_connexion.php">Gestion</a></li>
                <li><a href="#" class="couleur1">Consultation</a></li>
                <li><a href="gestion_proj.html">Gestion de projet</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="box-noir section-padding-bottom">
            <h2>Mesures en temps réel</h2>
            <div class="container-padding-80">
                <table>
                    <thead>
                        <tr>
                            <th>Bâtiment</th>
                            <th>Salle</th>
                            <th>Dernière Température</th>
                            <th>Date du relevé</th>
                            <th>Heure</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Show a message if no rooms are configured
                        if(mysqli_num_rows($requete_salles) == 0) {
                            echo "<tr><td colspan='5' class='text-center'>Aucune salle configurée par l'administrateur.</td></tr>";
                        }
                        // Loop through each room and display its latest measurement
                        while($data = mysqli_fetch_assoc($requete_salles)) { 
                        ?>
                        <tr>
                            <td><strong>Bâtiment <?php echo htmlspecialchars($data['id_bat']); ?></strong></td>
                            <td><?php echo htmlspecialchars($data['nom_salle']); ?></td>
                            <td class="text-primary-bold">
                                <?php echo $data['valeur'] !== null ? htmlspecialchars($data['valeur']) . " °C" : "En attente de données..."; ?>
                            </td>
                            <td><?php echo $data['date_mesure'] ? htmlspecialchars($data['date_mesure']) : "-"; ?></td>
                            <td><?php echo $data['heure'] ? htmlspecialchars($data['heure']) : "-"; ?></td>
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