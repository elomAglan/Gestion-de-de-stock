<?php
$servername = "localhost"; // ou l'adresse de votre serveur de base de données
$username = "root"; // votre nom d'utilisateur MySQL
$password = ""; // votre mot de passe MySQL
$dbname = "gestion_stock"; // nom de votre base de données

// Créer une connexion avec mysqli
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("La connexion a échoué: " . $conn->connect_error);
}
echo "Connexion réussie";
?>
