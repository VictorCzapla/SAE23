<?php
session_start();

$identifiant = $_POST['identifiant'];
$password = $_POST['password'];

$connexion = mysqli_connect('localhost', 'root', 'sae23.blagnac', 'sae23');

$requête = mysqli_query($connexion, "SELECT * FROM Batiment WHERE login_gestionnaire = '$identifiant' AND mdp_gestionnaire = '$password'");
$user = mysqli_fetch_assoc($requête);

if ($user) {
    $_SESSION['user'] = $identifiant;
    $_SESSION['role'] = 'gestionnaire';
    header('Location: dashboard.php');
} else {
    header('Location: erreur.html');
}
?>