<?php
session_start();

// Paramètres de connexion à la base de données
$host = "127.0.0.1:3308";
$dbname = "ecf_blog";
$username = "root";
$password = "";
$charset = "utf8mb4"; // Ajout du jeu de caractères

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=$charset", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
