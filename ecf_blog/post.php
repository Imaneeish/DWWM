<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post</title>
    <!-- Ajoutez les liens vers les styles Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Blog</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Login</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container mt-5">

        <?php
// Récupérer l'ID du post depuis la requête GET
$postId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$postId) {
    // Gérer le cas où l'ID du post n'est pas fourni
    echo "ID du post non spécifié";
    exit;
}

try {
    $mysqli = new mysqli("127.0.0.1:3308", "root", null, "ecf_blog");

    // Vérifier la connexion
    if ($mysqli->connect_error) {
        die("Erreur de connexion à la base de données : " . $mysqli->connect_error);
    }

    // Récupérer les détails du post
    $query = "SELECT * FROM posts WHERE id = $postId";
    $result = $mysqli->query($query);

    // Vérifier si la requête a réussi
    if (!$result) {
        die("Erreur dans la requête : " . $mysqli->error);
    }

    // Récupérer les données du post
    $post = $result->fetch_assoc();

    // Vérifier si le post existe
    if (!$post) {
        echo "Post non trouvé";
        exit;
    }

    
    // Récupérer les commentaires associés à ce post
    $commentsQuery = "SELECT * FROM comments WHERE postId = $postId";
    $commentsResult = $mysqli->query($commentsQuery);

    if (!$commentsResult) {
        die("Erreur dans la requête pour récupérer les commentaires : " . $mysqli->error);
    }

    // Récupérer les données des commentaires
    $comments = $commentsResult->fetch_all(MYSQLI_ASSOC);

    // Afficher les détails du post
    echo '<h1>' . $post['title'] . '</h1>';
    echo '<h6>Publié le ' . date("d/m/Y", strtotime($post['createdAt'])) . '</h6>';
    echo '<h5>' . $post['body'] . '</h5>';

    // Afficher les commentaires en cartes Bootstrap
    echo '<h2>Commentaires</h2>';
    foreach ($comments as $comment) {
        echo '<div class="card mt-3">';
        echo '<div class="card-body">';
        echo '<h5 class="card-title">' . $comment['name'] . '</h5>';
        echo '<h6 class="card-subtitle mb-2 text-muted">Publié le ' . date("d/m/Y", strtotime($comment['createdAt'])) . '</h6>';
        echo '<p class="card-text">' . $comment['body'] . '</p>';
        echo '</div>';
        echo '</div>';
    }

} catch (mysqli_sql_exception $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>

<!-- Ajoutez ici le code pour afficher les commentaires associés au post -->

    <!-- Ajoutez les scripts Bootstrap à la fin du corps -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>
