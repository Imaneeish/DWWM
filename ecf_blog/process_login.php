<?php
session_start();

// Inclure la configuration de la base de données et d'autres fonctionnalités
require_once('config.php');

// Vérifier si des données de formulaire ont été soumises
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Valider les informations d'identification
    $query = "SELECT * FROM user WHERE (email = :email OR username = :email) AND password = :password";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Informations d'identification valides, créer une session utilisateur
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        // Rediriger en fonction du rôle
        if ($user['role'] == 'admin') {
            header("Location: admin.php");
            exit;
        } elseif ($user['role'] == 'user') {
            header("Location: index.php");
            exit;
        }
    } else {
        // Informations d'identification invalides, rediriger vers la page de connexion avec un message d'erreur
        header("Location: login.php?error=1");
        exit;
    }
} else {
    // Rediriger vers la page de connexion si aucune donnée de formulaire n'a été soumise
    header("Location: login.php");
    exit;
}
?>
