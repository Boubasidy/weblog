<?php 
include('../config.php');
include(ROOT_PATH . '/includes/admin_functions.php');
include(ROOT_PATH . '/admin/post_functions.php');

if (isset($_POST['update_post'])) {
    $id = $_POST['post_id'];
    $title = trim($_POST['title']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    $views = intval($_POST['views'] ?? 0); // si tu veux permettre de modifier les vues
    $image = $_POST['current_image'] ?? null; // image existante si pas d’upload
    $body = $_POST['body'];
    $published = isset($_POST['published']) ? 1 : 0;
    $topic_id = intval($_POST['topic_id']);

    // gérer upload image si nouvelle image uploadée (à faire ici)

    $errors = [];

    if (empty($title)) $errors[] = "Le titre est obligatoire";
    if (empty($body)) $errors[] = "Le contenu est obligatoire";
    if (empty($topic_id)) $errors[] = "Le sujet est obligatoire";

    if (count($errors) === 0) {
        $success = updatePost($id, $title, $slug, $views, $image, $body, $published, $topic_id);
        if ($success) {
            $_SESSION['message'] = "Post mis à jour avec succès.";
            header("Location: posts.php");
            exit;
        } else {
            $errors[] = "Erreur lors de la mise à jour.";
        }
    }
}?>
<?php include(ROOT_PATH . '/includes/admin/head_section.php'); ?>
</head>

<body>
<?php include(ROOT_PATH . '/includes/admin/header.php') ?>
<h2 style="text-align: center;">Modifier Post</h2>

<?php if (!empty($errors)): ?>
    <div class="error">
        <ul>
            <?php foreach ($errors as $err): ?>
                <li><?= e($err) ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif ?>

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
        <input type="number" name="views" value="<?= e($post['views']) ?>" min="0">
    </div>

    <div class="form-group">
        <label>Image actuelle</label><br>
        <?php if ($post['image']): ?>
            <img src="<?= BASE_URL . '/static/images/' . e($post['image']) ?>" alt="Image post" style="max-width: 200px;"><br>
        <?php else: ?>
            <p>Aucune image</p>
        <?php endif ?>
        <label>Changer d'image (optionnel)</label>
        <input type="file" name="featured_image" accept="image/*">
    </div>

    <div class="form-group">
        <label>Contenu</label>
        <textarea name="body" rows="8" required><?= e($post['body']) ?></textarea>
    </div>

    <div class="form-group">
        <label>Sujet</label>
        <select name="topic_id" required>
            <option value="">-- Choisir un sujet --</option>
            <?php foreach ($topics as $topic): ?>
                <option value="<?= e($topic['id']) ?>" <?= ($post['topic_id'] == $topic['id']) ? 'selected' : '' ?>>
                    <?= e($topic['name']) ?>
                </option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="form-group">
        <label>Publié</label>
        <input type="checkbox" name="published" value="1" <?= $post['published'] ? 'checked' : '' ?>>
    </div>

    <button type="submit" name="update_post">Mettre à jour</button>
</form>

<p style="text-align : center;"><a href="posts.php">← Retour à la liste</a></p>

<script>
// JS simple pour générer automatiquement le slug à partir du titre
document.getElementById('title').addEventListener('input', function() {
    let slug = this.value.toLowerCase()
        .trim()
        .replace(/[^a-z0-9-]+/g, '-')
        .replace(/^-+|-+$/g, '');
    document.getElementById('slug').value = slug;
});
</script>
</body>
</html>

?>