<?php
session_start();

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
    <title>Accueil</title>
    <!-- Ajoutez les liens vers les styles Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>

    <div class="container mt-5">
        <?php

        try {
            $mysqli = new mysqli("127.0.0.1:3308", "root", null, "ecf_blog");

            // Vérifier la connexion
            if ($mysqli->connect_error) {
                die("Erreur de connexion à la base de données : " . $mysqli->connect_error);
            }

            // Définir la page actuelle
            $page = isset($_GET['page']) ? $_GET['page'] : 1;
            $limit = 12;
            $offset = ($page - 1) * $limit;

            // Récupérer les 12 derniers posts avec pagination
            $query = "SELECT * FROM posts ORDER BY createdAt DESC LIMIT $limit OFFSET $offset";
            $result = $mysqli->query($query);

            // Vérifier si la requête a réussi
            if (!$result) {
                die("Erreur dans la requête : " . $mysqli->error);
            }

            // Récupérer les données
            $posts = $result->fetch_all(MYSQLI_ASSOC);

            // Afficher les données sous forme de cartes Bootstrap
            echo '<div class="row">';
            foreach ($posts as $post) {
                echo '<div class="col-md-3 mb-4">';
                echo '<div class="card">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . $post['title'] . '</h5>';
                echo '<h6 class="card-subtitle mb-2 text-muted">Publié le ' . date("d/m/Y", strtotime($post['createdAt'])) . '</h6>';
                echo '<p class="card-text">' . substr($post['body'], 0, 150) . '...</p>';
                echo '<a href="post.php?id=' . $post['id'] . '">Voir l\'article</a>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';

            // Afficher la pagination comme précédemment
            if (count($posts) == $limit) {
                echo '<div class="mt-5 d-flex justify-content-between">';
                echo '<a href="?page=' . ($page - 1) . '" class="btn btn-primary">Précédent</a>';
                echo '<a href="?page=' . ($page + 1) . '" class="btn btn-primary ml-auto">Suivant</a>';
                echo '</div>';
            }

        } catch (mysqli_sql_exception $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }

        ?>
    </div>

    <!-- Ajoutez les scripts Bootstrap à la fin du corps -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
