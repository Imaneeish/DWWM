<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier si le formulaire de connexion a été soumis
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Connexion à la base de données
        $pdo = new PDO("mysql:host=127.0.0.1:3308;dbname=ecf_blog", "root", null);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Préparer la requête SQL pour récupérer l'utilisateur
        $query = "SELECT * FROM user WHERE email = :email AND password = :password";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // L'utilisateur est trouvé dans la base de données, récupérer ses informations
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            // Redirection en fonction du rôle
            if ($user['role'] == 'admin') {
                header("Location: admin.php");
                exit;
            } elseif ($user['role'] == 'user') {
                header("Location: index.php");
                exit;
            }
        } else {
            // L'utilisateur n'est pas trouvé dans la base de données, authentification échouée
            echo '<div class="alert alert-danger" role="alert">Identifiants incorrects!</div>';
        }
    }
}

// Vérifier si l'utilisateur est connecté
if (isset($_SESSION['user_id'])) {
    // L'utilisateur est connecté, afficher le lien ou le bouton de déconnexion
    echo '<nav class="navbar navbar-expand-lg navbar-light bg-light">';
    echo '<a class="navbar-brand" href="#">Blog</a>';
    echo '<div class="d-flex">';
    echo '<ul class="navbar-nav mr-auto">';
    echo '<li class="nav-item">';
    echo '<a class="nav-link" href="index.php">Accueil</a>';
    echo '</li>';
    echo '<li class="nav-item">';
    echo '<a class="nav-link" href="admin.php">Administration</a>';
    echo '</li>';
    echo '</ul>';
    echo '<ul class="navbar-nav">';
    echo '<li class="nav-item">';
    echo '<a class="nav-link" href="logout.php">Déconnexion</a>';
    echo '</li>';
    echo '</ul>';
    echo '</div>';
    echo '</nav>';
} else {
    // L'utilisateur n'est pas connecté, afficher le lien ou le bouton de connexion
    echo '<nav class="navbar navbar-expand-lg navbar-light bg-light">';
    echo '<a class="navbar-brand" href="#">Blog</a>';
    echo '<div class="d-flex">';
    echo '<ul class="navbar-nav mr-auto">';
    echo '<li class="nav-item">';
    echo '<a class="nav-link" href="index.php">Accueil</a>';
    echo '</li>';
    echo '<li class="nav-item">';
    echo '<a class="nav-link" href="login.php">Se connecter</a>';
    echo '</li>';
    echo '</ul>';
    echo '</div>';
    echo '</nav>';
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>

    <div class="container mt-5">
        <h1 class="mb-4">Connectez-vous à votre compte</h1>

        <div class="row">
            <div class="col-md-6 offset-md-3">
                <form action="process_login.php" method="post">
                    <div class="form-group">
                        <label for="email">Email :</label>
                        <input type="text" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Mot de passe :</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Se connecter</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
