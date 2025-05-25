<?php 
include('../config.php');
include(ROOT_PATH . '/includes/admin_functions.php');
include(ROOT_PATH . '/admin/post_functions.php');

// Initialiser variables
$errors = [];
$title = '';
$body = '';
$topic_id = '';
$image = '';
$views = 0;
$published = 0;
$post_id = 0;
$isEditingPost = false;

// Si le post existe déjà, charger ses données
if (isset($_GET['edit-post']) and $_GET['edit-post'] === "TRUE") {
    $isEditingPost = true;
    $post_id = intval($_GET['id']);
    $post = getPostById($post_id);
    $title = $post['title'];
    $body = $post['body'];
    $topic_id = $post['topic_id'];
    $image = $post['image'];
    $views = $post['views'];
    $published = $post['published'];

}

if (isset($_POST['update_post'])) {
    $post_id = intval($_POST['post_id']);
    $title = trim($_POST['title']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    $views = intval($_POST['views'] ?? 0);
    $body = $_POST['body'];
    $published = isset($_POST['published']) ? 1 : 0;
    $topic_id = intval($_POST['topic_id']);
    $image = $_POST['current_image'] ?? null;

    // Validation
    if (empty($title)) $errors[] = "Le titre est obligatoire";
    if (empty($body)) $errors[] = "Le contenu est obligatoire";
    if (empty($topic_id)) $errors[] = "Le sujet est obligatoire";

    // Gestion upload image
    if (!empty($_FILES['featured_image']['name'])) {
        $image_name = time() . '_' . basename($_FILES['featured_image']['name']);
        $target = ROOT_PATH . "/static/images/" . $image_name;

        if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $target)) {
            $image = $image_name;
        } else {
            $errors[] = "Échec de l’upload de l’image.";
        }
    }

    // Mise à jour si pas d’erreurs
    if (count($errors) === 0) {
        $success = updatePost($post_id, $title, $slug, $views, $image, $body, $published);
        if ($success) {
            $_SESSION['message'] = "Post mis à jour avec succès.";
            header("Location: posts.php");
            exit;
        } else {
            $errors[] = "Erreur lors de la mise à jour.";
        }
    }
}

// Récupérer tous les topics pour le select
$topics = getAllTopics();

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
                <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="container content">	
    <?php include(ROOT_PATH . '/includes/admin/menu.php') ?>
    <div class="action create-post-div">

        <form method="post" enctype="multipart/form-data" action="<?php echo htmlspecialchars(BASE_URL . 'weblog_v0/admin/edit_post.php'); ?>">

            <?php if ($isEditingPost): ?>
                <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post_id); ?>">
                <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($image); ?>">
            <?php endif; ?>

            <input 
                type="text"
                name="title"
                value="<?php echo htmlspecialchars($title); ?>" 
                placeholder="Title">

            <label style="float: left; margin: 5px auto 5px;">Featured image</label>
            <input 
                type="file"
                name="featured_image">

            <?php if ($image): ?>
                <p>Image actuelle : <strong><?php echo htmlspecialchars($image); ?></strong></p>
            <?php endif; ?>

            <textarea name="body" id="body" cols="30" rows="10"><?php echo htmlspecialchars($body); ?></textarea>

            <select name="topic_id">
                <option value="" disabled>Choisissez un sujet</option>
                <?php foreach ($topics as $topic): ?>
                    <option value="<?php echo $topic['id']; ?>" <?php if ($topic_id == $topic['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($topic['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label><input type="checkbox" name="published" <?php if ($published) echo 'checked'; ?>> Publier</label>

            <button type="submit" class="btn" name="update_post">Mettre à jour</button>

        </form>
    </div>
</div>

<script>
    CKEDITOR.replace('body');
</script>

</body>
</html>
