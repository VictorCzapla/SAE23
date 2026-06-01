<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'gestionnaire') {
    header('Location: gestion_connexion.php');
    exit();
}

$connexion = mysqli_connect('localhost', 'root', 'sae23.blagnac', 'sae23');
$login = $_SESSION['user'];

$requete_derniere = mysqli_query($connexion, "
    SELECT Salle.nom_salle, Capteur.nom_capteur, Capteur.unite, Mesure.valeur
    FROM Bâtiment
    JOIN Salle ON Salle.id_bat = Bâtiment.id_bat
    JOIN Capteur ON Capteur.nom_salle = Salle.nom_salle
    JOIN Mesure ON Mesure.nom_capteur = Capteur.nom_capteur
    WHERE Bâtiment.login_gestionnaire = '$login'
    AND Capteur.type = 'température'
    AND Mesure.id_mesure = (
        SELECT id_mesure FROM Mesure
        WHERE Mesure.nom_capteur = Capteur.nom_capteur
        ORDER BY id_mesure DESC
        LIMIT 1
    )
    ORDER BY Salle.nom_salle
");

$requete_stats = mysqli_query($connexion, "
    SELECT Capteur.nom_capteur,
        MAX(Mesure.valeur) AS valeur_max,
        MIN(Mesure.valeur) AS valeur_min
    FROM Capteur
    JOIN Mesure ON Mesure.nom_capteur = Capteur.nom_capteur
    WHERE Capteur.type = 'température'
    GROUP BY Capteur.nom_capteur
");

$stats = [];
while ($ligne = mysqli_fetch_assoc($requete_stats)) {
    $stats[$ligne['nom_capteur']] = $ligne;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Gestion</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="styles-css/style2RWD.css">
</head>
<body>

<header>
    <h1>Gestion : Tableau de bord</h1>
    <nav class="navbar1">
        <ul>
            <li><a href="index.html">Accueil</a></li>
            <li><a href="admin_connexion.php">Administration</a></li>
            <li><a href="#" class="couleur1">Gestion</a></li>
            <li><a href="consult.html">Consultation</a></li>
            <li><a href="gestion_proj.html">Gestion de projet</a></li>
            <li><a href="mentions.html">Mentions</a></li>
            <li><a href="deconnexion.php">Se déconnecter</a></li>
        </ul>
    </nav>
</header>

<main>
    <section class="box-noir">
        <h2>Dernières températures relevées</h2>
        <p>Connecté en tant que : <strong><?php echo $login; ?></strong></p>
        <table>
            <tr>
                <th>Salle</th>
                <th>Capteur</th>
                <th>Dernière valeur</th>
                <th>Maximum</th>
                <th>Minimum</th>
            </tr>
            <?php while ($ligne = mysqli_fetch_assoc($requete_derniere)) { ?>
            <tr>
                <td><?php echo $ligne['nom_salle']; ?></td>
                <td><?php echo $ligne['nom_capteur']; ?></td>
                <td><?php echo $ligne['valeur'] . ' ' . $ligne['unite']; ?></td>
                <td><?php echo $stats[$ligne['nom_capteur']]['valeur_max'] . ' ' . $ligne['unite']; ?></td>
                <td><?php echo $stats[$ligne['nom_capteur']]['valeur_min'] . ' ' . $ligne['unite']; ?></td>
            </tr>
            <?php } ?>
        </table>
    </section>
</main>

<aside id="last">
    <hr>
    <p><em>Validation de la page HTML5 - CSS3</em></p>
    <a href="https://validator.w3.org/nu/" target="_blank">
        <img class="badge" src="images/html5-validator-badge-blue.png" alt="HTML5 Valide !">
    </a>
    <a href="http://jigsaw.w3.org/css-validator/" target="_blank">
        <img class="badge" src="http://jigsaw.w3.org/css-validator/images/vcss-blue" alt="CSS Valide !">
    </a>
</aside>

<footer>
    <ul>
        <li>IUT de Blagnac</li>
        <li>Département Réseaux et Télécommunications</li>
        <li>BUT1&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li>
    </ul>
</footer>

</body>
</html>