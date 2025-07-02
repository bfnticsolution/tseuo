<?php
$servername = "localhost"; // Remplacez par le nom de votre serveur si nécessaire
$username = "root"; // Remplacez par votre nom d'utilisateur MySQL
$password = "6722Le@-"; // Remplacez par votre mot de passe MySQL
$dbname = "ouest_deal"; // Le nom de votre base de données

// Créer une connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("La connexion a échoué: " . $conn->connect_error);
}

?>
