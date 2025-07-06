<?php
// Inclure les variables de l'environnement
include_once '.env.php';

// Connexion à la base de données avec mysqli
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}
?>
