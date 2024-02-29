<?php
session_start();

// Vérifier si l'utilisateur est connecté et a le rôle d'administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Rediriger l'utilisateur non autorisé vers la page de connexion
    header("Location: index.php");
    exit;
}

require_once 'config.php';

// Fonction pour récupérer la liste des posts depuis la base de données
function getPosts($pdo) {
    $query = "SELECT * FROM posts ORDER BY createdAt DESC";
    $result = $pdo->query($query);
    return $result->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour mettre à jour un post dans la base de données
function updatePost($pdo, $postId, $title, $body) {
    $query = "UPDATE posts SET title = :title, body = :body, createdAt = NOW() WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindParam(':body', $body, PDO::PARAM_STR);
    $stmt->bindParam(':id', $postId, PDO::PARAM_INT);
    return $stmt->execute();
}

// Fonction pour ajouter un nouveau post dans la base de données
function addPost($pdo, $title, $body) {
    $query = "INSERT INTO posts (title, body, createdAt) VALUES (:title, :body, NOW())";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindParam(':body', $body, PDO::PARAM_STR);
    return $stmt->execute();
}

// Fonction pour supprimer un post de la base de données
function deletePost($pdo, $postId) {
    $query = "DELETE FROM posts WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $postId, PDO::PARAM_INT);
    return $stmt->execute();
}

// Gérer les actions (ajouter, modifier, supprimer)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        switch ($action) {
            case 'update':
                if (isset($_POST['post_id']) && isset($_POST['title']) && isset($_POST['body'])) {
                    updatePost($pdo, $_POST['post_id'], $_POST['title'], $_POST['body']);
                }
                break;

            case 'add':
                if (isset($_POST['title']) && isset($_POST['body'])) {
                    addPost($pdo, $_POST['title'], $_POST['body']);
                }
                break;

            case 'delete':
                if (isset($_POST['post_id'])) {
                    deletePost($pdo, $_POST['post_id']);
                }
                break;
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

// Récupérer la liste des posts
$posts = getPosts($pdo);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>

    <div class="container mt-5">
        <h1 class="mb-4">Administration</h1>

        <!-- Bouton pour ajouter un nouveau post -->
        <button class="btn btn-success mb-4" data-toggle="modal" data-target="#addPostModal">Ajouter un post</button>

        <!-- Tableau pour afficher la liste des posts -->
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Titre</th>
                    <th scope="col">Date de publication</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post) : ?>
                    <tr>
                        <th scope="row"><?= $post['id'] ?></th>
                        <td><?= $post['title'] ?></td>
                        <td><?= $post['createdAt'] ?></td>
                        <td>
                            <!-- Boutons pour modifier et supprimer un post -->
                            <button class="btn btn-primary" data-toggle="modal" data-target="#editPostModal<?= $post['id'] ?>">Modifier</button>
                            <button class="btn btn-danger" data-toggle="modal" data-target="#deletePostModal<?= $post['id'] ?>">Supprimer</button>
                        </td>
                    </tr>

                    <!-- Modal pour modifier un post -->
                    <div class="modal fade" id="editPostModal<?= $post['id'] ?>" tabindex="-1" role="dialog"
                        aria-labelledby="editPostModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editPostModalLabel">Modifier le post</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="admin.php" method="post">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                        <div class="form-group">
                                            <label for="editTitle">Titre :</label>
                                            <input type="text" class="form-control" id="editTitle" name="title"
                                                value="<?= $post['title'] ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="editBody">Contenu :</label>
                                            <textarea class="form-control" id="editBody" name="body"
                                                rows="4" required><?= $post['body'] ?></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal pour supprimer un post -->
                    <div class="modal fade" id="deletePostModal<?= $post['id'] ?>" tabindex="-1" role="dialog"
                        aria-labelledby="deletePostModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deletePostModalLabel">Supprimer le post</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p>Êtes-vous sûr de vouloir supprimer ce post ?</p>
                                    <form action="admin.php" method="post">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                        <button type="submit" class="btn btn-danger">Oui, supprimer</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal pour ajouter un nouveau post -->
    <div class="modal fade" id="addPostModal" tabindex="-1" role="dialog" aria-labelledby="addPostModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPostModalLabel">Ajouter un post</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="admin.php" method="post">
                        <input type="hidden" name="action" value="add">
                        <div class="form-group">
                            <label for="addTitle">Titre :</label>
                            <input type="text" class="form-control" id="addTitle" name="title" required>
                        </div>
                        <div class="form-group">
                            <label for="addBody">Contenu :</label>
                            <textarea class="form-control" id="addBody" name="body" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Ajouter le post</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
