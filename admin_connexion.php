<?php
session_start();
$erreur = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $identifiant = $_POST['identifiant'];
    $password = $_POST['password'];
    $connexion = mysqli_connect('localhost', 'root', 'sae23.blagnac', 'sae23');
    $requete = mysqli_query($connexion, "SELECT * FROM Administrateurs WHERE login_admin = '$identifiant' AND mdp_admin = '$password'");
    $user = mysqli_fetch_assoc($requete);
    if ($user) {
        $_SESSION['user'] = $identifiant;
        $_SESSION['role'] = 'Administrateur';
        header('Location: admin.php');
    } else {
        $erreur = "Identifiants incorrects";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
 <head>
  <title>Administration</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="CZAPLA VICTOR">
  <meta name="description" content="SAe23">
  <link rel="stylesheet" href="styles-css/style2RWD.css">
 </head>
 <body>
 
  <header>
   <h1> Administration : Page de Connexion</h1>
   <nav class="navbar1">
    <ul>
     <li><a href="index.html">Accueil</a></li>
     <li><a href="#" class="couleur1">Administration</a></li>
	 <li><a href="gestion_connexion.php">Gestion</a></li>
     <li><a href="consult.html">Consultation</a></li>
     <li><a href="gestion_proj.html">Gestion de projet</a></li>
     <li><a href="mentions.html">Mentions</a></li>
    </ul>
   </nav>
  </header>
  <main>
  <section class="box-noir">
    <h2> Formulaire de connexion </h2>
    <form name="connexion_admin" action="admin_connexion.php" method="post">
        <fieldset>
            <p>
                <label for="identifiant">Identifiant : </label>
                <input type="text" name="identifiant" id="identifiant" size="30" />
            </p>
            <p>
                <label for="password">Mot de passe : </label>
                <input type="password" name="password" id="password" size="30" />
            </p>
            <?php if ($erreur != "") { echo "<p>$erreur</p>"; } ?>
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
	
    <a href="https://validator.w3.org/nu/?doc=http%3A%2F%2Fczapla.atwebpages.com%2FSaE14%2Feportfolio.html" target="_blank"> 
        <img class="badge" src="images/html5-validator-badge-blue.png" alt="HTML5 Valide !">
    </a>
	
    <a href="http://jigsaw.w3.org/css-validator/validator?uri=http%3A%2F%2Fczapla.atwebpages.com%2FSaE14%2Fstyles-css%2Fstyle2RWD.css" target="_blank">
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