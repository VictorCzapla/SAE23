<?php
// Database connection
$connexion = mysqli_connect('localhost', 'arahalinony', 'sae23.blagnac', 'SAE23_V2');

if (!$connexion) {
    die("Erreur de connexion à la base de données : " . mysqli_connect_error());
}

// Fetch all rooms with their building info, sorted by building then room name
$requete_salles = mysqli_query($connexion, "
    SELECT Salle.nom_salle, Batiment.id_bat, Batiment.nom_bat 
    FROM Salle
    JOIN Batiment ON Salle.id_bat = Batiment.id_bat
    ORDER BY Batiment.nom_bat, Salle.nom_salle
");

// Group rooms by building name into an associative array
$batiments = [];
if ($requete_salles) {
    while ($ligne = mysqli_fetch_assoc($requete_salles)) {
        $nom_bat = $ligne['nom_bat'];
        $batiments[$nom_bat][] = $ligne['nom_salle'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
 <head>
  <title>Accueil</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="CZAPLA VICTOR, MASSIOT CLEMENT, RAHALINONY-NUNES, MEHDI-NAFAA">
  <meta name="description" content="SAe23 - Supervision des capteurs IUT de Blagnac">
  <link rel="stylesheet" href="styles-css/style2RWD.css">
 </head>
 <body>

  <header>
   <h1>Supervision des capteurs - IUT de Blagnac</h1>
   <nav class="navbar1">
    <ul>
     <li><a href="#" class="couleur1">Accueil</a></li>
     <li><a href="admin_connexion.php">Administration</a></li>
     <li><a href="gestion_connexion.php">Gestion</a></li>
     <li><a href="consultation.php">Consultation</a></li>
     <li><a href="gestion_proj.html">Gestion de projet</a></li>
    </ul>
   </nav>
  </header>

  <main>

   <section class="box-noir">
    <h2>Présentation du projet</h2>
    <p>
     Ce site a été réalisé dans le cadre du projet SAé 23 (Situation d'Apprentissage et d'Évaluation)
     par un groupe de 4 étudiants en BUT Réseaux &amp; Télécommunications à l'IUT de Blagnac :
    </p>
    <ul>
     <li>Czapla Victor</li>
     <li>Massiot Clément</li>
     <li>Rahalinony-Nunes Angelo</li>
     <li>Mehdi-Nafaa Nura</li>
    </ul>
    
    <h3>Objectif du projet</h3>
    <p>
     L'objectif principal est de concevoir une solution complète de supervision et de centralisation en temps réel des mesures environnementales (température) récoltées au sein des différents bâtiments de l'IUT. Ce projet met en œuvre une chaîne IoT (Internet des Objets) complète, assurant la collecte, le transport sécurisé, le stockage et la restitution visuelle des valeurs sur des pages web.
    </p>

    <h3>Architecture technique et acheminement des données</h3>
    <p>
     Le flux d'informations traverse plusieurs étapes  :
    </p>
    <ul>
     <li>
      <strong>1. Collecte et Publication (IoT) :</strong> Les capteurs mesurent la température ambiante des salles et publient ces données toutes les 10 minutes.
     </li>
     <li>
      <strong>2. Transmission et Routage (MQTT Mosquitto) :</strong> Les données transitent via le protocole réseau <strong>MQTT</strong>. Le broker <strong>Mosquitto</strong> centralise ces flux en gérant un système d'abonnements (Publish/Subscribe) qui transmet les messages vers les scripts de traitement.
     </li>
     <li>
      <strong>3. Stockage persistant (Base de données MySQL PHPMYADMIN) :</strong> Un script d'écoute intercepte les messages du broker pour les envoyer automatiquement dans une base de données relationnelle <strong>MySQL</strong>. Les tables (Bâtiment, Salle, Capteur, Mesure) permettent d'historiser les relevés et de lier chaque mesure à sa structure.
     </li>
     <li>
      <strong>4. Restitution Dynamique (PHP &amp; Web) :</strong> Le serveur web interroge la base MySQL en temps réel pour générer l'interface utilisateur. Les données complexes sont ainsi traduites en tableaux lisibles et segmentées par profils d'accès.
     </li>
    </ul>

    <h3>Technologies utilisées pour l'interface</h3>
    <ul>
     <li><strong>HTML5</strong> – Structuration sémantique des pages, des vues tabulaires et des formulaires d'authentification.</li>
     <li><strong>CSS3</strong> – aspect graphique moderne et intégration du <em>Responsive Web Design</em> (RWD) pour s'adapter aux écrans d'ordinateurs, tablettes et smartphones.</li>
     <li><strong>PHP</strong> – Traitement dynamique côté serveur, sécurisation des requêtes MySQL (injections SQL), gestion des sessions utilisateurs (Gestionnaires / Administrateurs) et déconnexions.</li>
    </ul>
   </section>

   <section class="box-noir">
    <?php 
    if (empty($batiments)) {
        echo "<h2>Configuration des locaux</h2>";
        echo "<p class='text-center-italic'>Aucune salle n'est actuellement configurée par l'administrateur.</p>";
    } else {
        // Loop through each building and display its rooms in a table
        foreach ($batiments as $nom_du_batiment => $liste_des_salles) {
        ?>
        <h2><?php echo htmlspecialchars($nom_du_batiment); ?></h2>
        <table>
         <thead>
          <tr>
           <th>Salle</th>
           <th>Type de capteur(s)</th>
           <th>Accès aux mesures</th>
          </tr>
         </thead>
         <tbody>
          <?php foreach ($liste_des_salles as $salle) { ?>
          <tr>
           <td><strong><?php echo htmlspecialchars($salle); ?></strong></td>
           <td>Température</td>
           <td><a href="consultation.php" class="menu-lien">Consultation publique</a></td>
          </tr>
          <?php } ?>
         </tbody>
        </table>
        <?php 
        } 
    }
    ?>
   </section>

   <section class="box-noir">
    <h2>Accès rapide</h2>
    <ul>
     <li><a href="consultation.php" class="menu-lien">Consultation publique des dernières mesures</a></li>
     <li><a href="gestion_connexion.php" class="menu-lien">Espace gestionnaire (connexion requise)</a></li>
     <li><a href="admin_connexion.php" class="menu-lien">Espace administrator (connexion requise)</a></li>
    </ul>
   </section>
   
   <section class="box-noir">
    <h2>Mentions légales</h2>
    <p>En vigueur au 11/11/2025 Conformément aux dispositions de la loi n°2004-575 du 21 juin 2004 pour la Confiance en l’économie numérique, 
	il est porté à la connaissance des utilisateurs et visiteurs, ci-après l' "Utilisateur", du site http://czapla.atwebpages.com/SaE23 / http://localhost/sae23, ci-après le "Site", 
	les présentes mentions légales. La connexion et la navigation sur le Site par l’Utilisateur implique acceptation intégrale et sans réserve des présentes mentions légales. 
	Ces dernières sont accessibles sur le Site à la rubrique "Mentions légales". 
	EDITION DU SITE L’édition et la direction de la publication du Site est assurée par Victor Czapla, Clément Massiot, Nura Mehdi-Nafaa, Angelo Rahalinony-Nunes, 
	et l'adresse e-mail victor.czapla@ikmail.com. ci-après l'"Editeur". 
	HEBERGEUR Le site est actuellement développé et testé dans un environnement local (Serveur XAMPP / Machine Virtuelle). En condition de production, le site est destiné à être hébergé par la société eohost, dont le siège social est situé au Schauenburgerstr 116 24118 Kiel, Allemagne . 
	ACCES AU SITE Le Site est normalement accessible, à tout moment, à l'Utilisateur. 
	Toutefois, l'Editeur pourra, à tout moment, suspendre, limiter ou interrompre le Site afin de procéder, notamment, à des mises à jour ou des modifications de son contenu. 
	L'Editeur ne pourra en aucun cas être tenu responsable des conséquences éventuelles de cette indisponibilité sur les activités de l'Utilisateur. 
	Toute utilisation, reproduction, diffusion, commercialisation, modification de toute ou partie du Site, 
	sans autorisation expresse de l’Editeur est prohibée et pourra entraîner des actions et poursuites judiciaires telles que prévues par la règlementation en vigueur.
	</p>
   </section>

  </main>

  <aside id="last">
   <hr>
   <p><em>Validation de la page HTML5 - CSS3</em></p>
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

  <?php
  // Close database connection
  mysqli_close($connexion); 
  ?>
 </body>
</html>