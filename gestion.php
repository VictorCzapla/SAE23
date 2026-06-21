<?php
session_start();

// Redirect to login page if user is not logged in as manager
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'gestionnaire') {
    header('Location: gestion_connexion.php');
    exit();
}

// Database connection
$connexion = mysqli_connect('localhost', 'arahalinony', 'sae23.blagnac', 'SAE23_V2');
if (!$connexion) {
    die("Erreur de connexion : " . mysqli_connect_error());
}

// Sanitize the manager's login from session
$login = mysqli_real_escape_string($connexion, $_SESSION['user']);

// Fetch the latest temperature reading per room for this manager's building
$requete_derniere = mysqli_query($connexion, "
    SELECT Salle.nom_salle, Capteur.nom_capteur, Capteur.unite, Mesure.valeur
    FROM Batiment
    JOIN Salle ON Salle.id_bat = Batiment.id_bat
    JOIN Capteur ON Capteur.nom_salle = Salle.nom_salle
    JOIN Mesure ON Mesure.nom_capteur = Capteur.nom_capteur
    WHERE Batiment.login_gestionnaire = '$login'
    AND Capteur.type LIKE '%emp%rature%'
    AND Mesure.id_mesure = (
        SELECT MAX(id_mesure) 
        FROM Mesure 
        WHERE Mesure.nom_capteur = Capteur.nom_capteur
    )
    ORDER BY Salle.nom_salle
");

// Fetch temperature statistics (min, max, avg) grouped by room for this manager's building
$requete_stats = mysqli_query($connexion, "
    SELECT Salle.nom_salle,
        MAX(Mesure.valeur) AS valeur_max,
        MIN(Mesure.valeur) AS valeur_min,
        ROUND(AVG(Mesure.valeur), 2) AS valeur_moy
    FROM Batiment
    JOIN Salle ON Salle.id_bat = Batiment.id_bat
    JOIN Capteur ON Capteur.nom_salle = Salle.nom_salle
    JOIN Mesure ON Mesure.nom_capteur = Capteur.nom_capteur
    WHERE Capteur.type LIKE '%emp%rature%'
    AND Batiment.login_gestionnaire = '$login'
    GROUP BY Salle.nom_salle
");

// Store stats in an associative array indexed by room name for easy lookup
$stats = [];
if ($requete_stats) {
    while ($ligne = mysqli_fetch_assoc($requete_stats)) {
        $stats[$ligne['nom_salle']] = $ligne;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Gestion - Tableau de bord</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="CZAPLA VICTOR, MASSIOT CLEMENT, RAHALINONY-NUNES, MEHDI-NAFAA">
    <meta name="description" content="SAe23 - Supervision des capteurs IUT de Blagnac">
    <link rel="stylesheet" href="styles-css/style2RWD.css">
</head>
<body>

<header>
    <h1>Gestion : Tableau de bord</h1>
    <nav class="navbar1">
        <ul>
            <li><a href="index.php">Accueil</a></li>
            <li><a href="admin_connexion.php">Administration</a></li>
            <li><a href="#" class="couleur1">Gestion</a></li>
            <li><a href="consultation.php">Consultation</a></li> 
            <li><a href="gestion_proj.html">Gestion de projet</a></li>
            <li><a href="deconnexion.php" class="btn-deconnexion">Se déconnecter</a></li>
        </ul>
    </nav>
</header>

<main>
    <section class="box-noir section-padding-bottom">
        <h2>Suivi des températures de votre bâtiment</h2>
        <p>Connecté en tant que gestionnaire : <span class="text-primary-bold"><?php echo htmlspecialchars($login); ?></span></p>
        
        <div class="container-padding-80">
            <table>
                <thead>
                    <tr>
                        <th>Salle</th>
                        <th>Capteur</th>
                        <th>Dernière valeur</th>
                        <th>Moyenne globale</th>
                        <th>Maximum</th>
                        <th>Minimum</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Show a message if no data is available for this manager
                    if (!$requete_derniere || mysqli_num_rows($requete_derniere) == 0) {
                        echo "<tr><td colspan='6' class='text-center'>Aucune donnée disponible pour votre bâtiment ou aucune salle ne vous est attribuée.</td></tr>";
                    } else {
                        // Loop through each room and display its latest value and stats
                        while ($ligne = mysqli_fetch_assoc($requete_derniere)) { 
                            $salle = $ligne['nom_salle'];
                            $capteur = $ligne['nom_capteur'];
                            
                            // Retrieve precomputed stats for this room, fallback to '--' if missing
                            $moyen = isset($stats[$salle]['valeur_moy']) ? $stats[$salle]['valeur_moy'] : '--';
                            $max = isset($stats[$salle]['valeur_max']) ? $stats[$salle]['valeur_max'] : '--';
                            $min = isset($stats[$salle]['valeur_min']) ? $stats[$salle]['valeur_min'] : '--';
                        ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($salle); ?></strong></td>
                            <td><?php echo htmlspecialchars($capteur); ?></td>
                            <td class="text-primary-bold"><?php echo htmlspecialchars($ligne['valeur']) . ' ' . htmlspecialchars($ligne['unite']); ?></td>
                            <td><?php echo htmlspecialchars($moyen) . ' ' . htmlspecialchars($ligne['unite']); ?></td>
                            <td class="text-danger"><?php echo htmlspecialchars($max) . ' ' . htmlspecialchars($ligne['unite']); ?></td>
                            <td class="text-info"><?php echo htmlspecialchars($min) . ' ' . htmlspecialchars($ligne['unite']); ?></td>
                        </tr>
                        <?php 
                        } 
                    }
                    ?>
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