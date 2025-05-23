<?php
session_start();
include('../config.php');
include('../includes/admin_functions.php'); // à créer ou compléter si besoin
include('../includes/admin/header.php');
include('../includes/admin/head_section.php');
// Vérifie que l'utilisateur est un admin

// Connexion à la base
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Erreur de connexion à la base : " . $conn->connect_error);
}

// Récupère tous les posts
$sql = "SELECT * FROM posts ORDER BY created_at DESC";
$result = $conn->query($sql);
$posts = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
}

$conn->close();
?>

<body>
    <!-- Header -->

    <div class="container content">
        <!-- Menu -->
        <?php include('../includes/admin/menu.php') ?>

        <!-- Page Content -->
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
                            <td><?php echo $key + 1; ?></td>
                            <td><?php echo htmlspecialchars($post['title']); ?></td>
                            <td><?php echo htmlspecialchars($post['author']); ?></td>
                            <td><?php echo $post['published'] ? 'Oui' : 'Non'; ?></td>
                            <td>
                                <a href="edit_post.php?id=<?php echo $post['id']; ?>" class="edit">Modifier</a>
                                &nbsp;
                                <a href="delete_post.php?id=<?php echo $post['id']; ?>" class="delete" onclick="return confirm('Supprimer cet article ?')">Supprimer</a>
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
        <!-- // Page Content -->
    </div>
</body>
</html>
