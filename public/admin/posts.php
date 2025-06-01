<?php
// Initialisation sécurisée de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start(); //DCKR
}

// Inclusion des dépendances essentielles
require_once('../config.php'); //DCKR
require_once('../includes/admin_functions.php'); 
require_once('../includes/admin/header.php'); //DCKR
require_once('../includes/admin/head_section.php'); //DCKR

// Vérification que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php'); //DCKR redirection sécurisée
    exit();
}

$user_id = $_SESSION['user_id'];

// Connexion base de données
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Erreur de connexion à la base : " . $conn->connect_error); //DCKR
}

// Récupération de tous les articles
$posts = [];
$sql = "SELECT * FROM posts ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
}

// Récupération du rôle de l’utilisateur
$sqlRole = "
    SELECT r.name AS role
    FROM users u
    JOIN role_user ru ON ru.user_id = u.id
    JOIN roles r ON r.id = ru.role_id
    WHERE u.id = ?
";
$stmt = $conn->prepare($sqlRole);
if (!$stmt) {
    die("Erreur préparation rôle : " . $conn->error); //DCKR
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$role = $result->fetch_assoc()['role'] ?? null;

$stmt->close();
$conn->close(); //DCKR
?>

<body>
    <div class="container content">
        <!-- Menu -->
        <?php include('../includes/admin/menu.php'); //DCKR ?>

        <!-- Contenu principal -->
        <div class="table-div" style="width: 80%; margin: 0 auto;">
            <h2 class="page-title">Gérer les articles</h2>

            <a href="create_post.php" class="btn btn-big">+ Ajouter un article</a>

            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Titre</th>
                        <th>Auteur</th>
                        <th>Publié</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $key => $post): ?>
                        <tr>
                            <td><?= $key + 1 ?></td>
                            <td><?= htmlspecialchars($post['title']) ?></td>
                            <td><?= htmlspecialchars($post['author'] ?? '—') ?></td>
                            <td><?= $post['published'] ? 'Oui' : 'Non' ?></td>
                            <td>
                                <a href="edit_post.php?id=<?= $post['id'] ?>&role=<?= urlencode($role) ?>" class="edit">Modifier</a>
                                &nbsp;
                                <a href="delete_post.php?id=<?= $post['id'] ?>&role=<?= urlencode($role) ?>" class="delete" onclick="return confirm('Supprimer cet article ?')">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (count($posts) === 0): ?>
                        <tr>
                            <td colspan="5">Aucun article trouvé.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
