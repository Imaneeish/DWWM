<?php
// Connectez-vous à la base de données (assurez-vous que votre configuration est correcte)
$mysqli = new mysqli("127.0.0.1:3308", "root", null, "ecf_blog");

// Vérifiez la connexion
if ($mysqli->connect_error) {
    die("Erreur de connexion à la base de données : " . $mysqli->connect_error);
}

// Récupérez les données du formulaire
$postId = $_POST['postId'];
$offset = $_POST['offset'];

// Requête pour récupérer les commentaires supplémentaires
$commentsQuery = "SELECT * FROM comments WHERE postId = $postId LIMIT $offset, 2";
$commentsResult = $mysqli->query($commentsQuery);

if ($commentsResult) {
    // Récupérez les données des commentaires
    $comments = $commentsResult->fetch_all(MYSQLI_ASSOC);

    // Affichez les commentaires
    foreach ($comments as $comment) {
        echo '<div class="card mt-3">';
        echo '<div class="card-body">';
        echo '<h5 class="card-title">' . $comment['name'] . '</h5>';
        echo '<h6 class="card-subtitle mb-2 text-muted">Publié le ' . date("d/m/Y", strtotime($comment['createdAt'])) . '</h6>';
        echo '<p class="card-text">' . $comment['body'] . '</p>';
        echo '</div>';
        echo '</div>';
    }
} else {
    echo "Erreur dans la requête pour récupérer les commentaires : " . $mysqli->error;
}


?>

