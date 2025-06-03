<?php
include('../config.php');
include_once(ROOT_PATH . '/includes/admin_functions.php');
include(ROOT_PATH . '/admin/post_functions.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // DCKR : Sécurisation du démarrage de session
}
// Initialiser variables
$errors = [];
$post = [];
$topics = getAllTopics(); // Récupération des sujets

// Récupération du post à éditer si ID présent en GET
if (isset($_GET['id'])) {
    $post_id = intval($_GET['id']);
    $post = getPostById($post_id);
    if (!$post) {
        $_SESSION['message'] = "Post introuvable.";
        header("Location: posts.php");
        exit;
    }

    // Définir $topic_id à partir du post récupéré
    $topic_id = $post['topic_id'] ?? '';
}

// Traitement du formulaire de mise à jour
if (isset($_POST['update_post'])) {
    $post_id = intval($_POST['post_id']);
    $title = trim($_POST['title']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    $views = intval($_POST['views'] ?? 0);
    $image = $_POST['current_image'] ?? null;
    $body = $_POST['body'];
    $published = isset($_POST['published']) ? 1 : 0;
    $topic_id = intval($_POST['topic_id']);

    // Validation
    if (empty($title)) $errors[] = "Le titre est obligatoire";
    if (empty($body)) $errors[] = "Le contenu est obligatoire";
    if (empty($topic_id)) $errors[] = "Le sujet est obligatoire";

    // Gestion de l'image si upload
    if (!empty($_FILES['featured_image']['name'])) {
        $image_name = time() . '_' . basename($_FILES['featured_image']['name']);
        $target = ROOT_PATH . "/static/images/" . $image_name;

        if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $target)) {
            $image = $image_name;
        } else {
            $errors[] = "Échec de l’envoi de l’image.";
        }
    }

    // Mise à jour si pas d'erreurs
    if (count($errors) === 0) {
        $success = updatePost($post_id, $title, $slug, $views, $image, $body, $published, $topic_id);
        if ($success) {
            $_SESSION['message'] = "Post mis à jour avec succès.";
            header("Location: posts.php");
            exit;
        } else {
            $errors[] = "Erreur lors de la mise à jour.";
        }
    } else {
        // En cas d’erreurs, repopuler $post pour re-remplir le formulaire
        $post = [
            'id' => $post_id,
            'title' => $title,
            'slug' => $slug,
            'views' => $views,
            'image' => $image,
            'body' => $body,
            'published' => $published,
            'topic_id' => $topic_id
        ];
    }
}

include(ROOT_PATH . '/includes/admin/head_section.php');
?>
</head>
<body>
<?php include(ROOT_PATH . '/includes/admin/header.php'); ?>
<h2 style="text-align: center;">Modifier Post</h2>

<?php if (!empty($errors)): ?>
    <div class="error">
        <ul>
            <?php foreach ($errors as $err): ?>
                <li><?= e($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (!empty($post)): ?>
<form method="POST" action="edit_post.php?id=<?= e($post['id']) ?>" enctype="multipart/form-data">
    <input type="hidden" name="post_id" value="<?= e($post['id']) ?>">
    <input type="hidden" name="current_image" value="<?= e($post['image']) ?>">

    <div class="form-group">
        <label>Titre</label>
        <input type="text" name="title" id="title" value="<?= e($post['title']) ?>" required>
    </div>

    <div class="form-group">
        <label>Slug (automatique)</label>
        <input type="text" name="slug" id="slug" value="<?= e($post['slug']) ?>" readonly>
    </div>

    <div class="form-group">
        <label>Vues</label>
        <input type="number" name="views" value="<?php echo e($post['views'] ?? 0); ?>" min="0">
    </div>

    <div class="form-group">
        <label>Image actuelle</label><br>
        <?php if (!empty($post['image'])): ?>
            <img src="<?= BASE_URL . '/static/images/' . e($post['image']) ?>" alt="Image post" style="max-width: 200px;"><br>
        <?php else: ?>
            <p>Aucune image</p>
        <?php endif; ?>
        <label>Changer d'image (optionnel)</label>
        <input type="file" name="featured_image" accept="image/*">
    </div>

    <div class="form-group">
        <label>Contenu</label>
        <textarea name="body" id="body" rows="8" required><?= e($post['body']) ?></textarea>
    </div>

    <div class="form-group">
        <label>Sujet</label>
        <select name="topic_id" required>
            <option value="">-- Choisir un sujet --</option>
            <?php foreach ($topics as $topic): ?>
                <option value="<?php echo $topic['id']; ?>" <?php if ($topic_id == $topic['id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($topic['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label>Publié</label>
        <input type="checkbox" name="published" value="1" <?= $post['published'] ? 'checked' : '' ?>>
    </div>

    <button type="submit" name="update_post">Mettre à jour</button>
</form>
<?php endif; ?>

<p style="text-align: center;"><a href="posts.php">← Retour à la liste</a></p>

<script>
document.getElementById('title').addEventListener('input', function() {
    let slug = this.value.toLowerCase()
        .trim()
        .replace(/[^a-z0-9-]+/g, '-')
        .replace(/^-+|-+$/g, '');
    document.getElementById('slug').value = slug;
});
</script>

<!-- Si CKEditor est nécessaire -->
<script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
<script>CKEDITOR.replace('body');</script>

</body>
</html>
