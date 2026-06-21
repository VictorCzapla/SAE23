<?php
session_start();

// Redirect to admin page if already logged in as admin
if (isset($_SESSION['user']) && $_SESSION['role'] === 'Administrateur') {
    header('Location: admin.php');
    exit();
}

$erreur = "";

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Database connection
    $connexion = mysqli_connect('localhost', 'arahalinony', 'sae23.blagnac', 'SAE23_V2');
    
    if (!$connexion) {
        die("Erreur de connexion à la base de données : " . mysqli_connect_error());
    }

    // Secure inputs to prevent SQL injection
    $identifiant = mysqli_real_escape_string($connexion, $_POST['identifiant']);
    $password = mysqli_real_escape_string($connexion, $_POST['password']);
    
    // Check credentials against the Administration table
    $requete = mysqli_query($connexion, "SELECT * FROM Administrateurs WHERE login_admin = '$identifiant' AND mdp_admin = '$password'");
    
    if (!$requete) {
        die("Erreur SQL : " . mysqli_error($connexion));
    }

    $user = mysqli_fetch_assoc($requete);
    
    if ($user) {
        // Store user info in session and redirect to admin panel
        $_SESSION['user'] = $identifiant;
        $_SESSION['role'] = 'Administrateur';
        header('Location: admin.php');
        exit();
    } else {
        $erreur = "Identifiants incorrects";
    }
    
    mysqli_close($connexion);
}
?>
<!DOCTYPE html>
<html lang="fr">
 <head>
  <title>Administration - Connexion</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="CZAPLA VICTOR, MASSIOT CLEMENT, RAHALINONY-NUNES, MEHDI-NAFAA">
  <meta name="description" content="SAe23 - Supervision des capteurs IUT de Blagnac">
  <link rel="stylesheet" href="styles-css/style2RWD.css">
 </head>
 <body>
 
  <header>
   <h1> Administration : Page de Connexion</h1>
   <nav class="navbar1">
    <ul>
     <li><a href="index.php">Accueil</a></li>
     <li><a href="#" class="couleur1">Administration</a></li>
     <li><a href="gestion_connexion.php">Gestion</a></li>
     <li><a href="consultation.php">Consultation</a></li> 
     <li><a href="gestion_proj.html">Gestion de projet</a></li>
    </ul>
   </nav>
  </header>
  
  <main>

  <section class="box-noir">
    <h2> Formulaire de connexion </h2>
    <form name="connexion_admin" action="admin_connexion.php" method="post" class="form-admin">
        <fieldset class="form-fieldset-clean">
            <p>
                <label for="identifiant">Identifiant Administrateur : </label>
                <input type="text" name="identifiant" id="identifiant" size="30" required />
            </p>
            <p>
                <label for="password">Mot de passe : </label>
                <input type="password" name="password" id="password" size="30" required />
            </p>
            <?php if ($erreur != "") { echo "<p class='text-alert-error'>" . htmlspecialchars($erreur) . "</p>"; } ?>
        </fieldset>
        <p>
            <input type="submit" value="Se connecter" />
        </p>
    </form>
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
    
 </body>
</html>