<?php
$host = 'sql100.infinityfree.com';
$username = 'if0_39369296';
$password = '67222863L'; // remplacez par votre mot de passe réel
$database = 'if0_39369296_deal'; // remplacez XXX par le nom réel de la base

$conn = new mysqli($host, $username, $password, $database);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}
?>
