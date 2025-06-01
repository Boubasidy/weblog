<?php
// DCKR : Vérification de l’état de session avant de l’ouvrir
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // DCKR : Correction du démarrage de session sécurisé
}

include('../config.php');
include(ROOT_PATH . '/includes/admin_functions.php');
include(ROOT_PATH . '/admin/post_functions.php');
include(ROOT_PATH . '/includes/admin/head_section.php');

// Get all topics
$topics = getAllTopics();
?>

<?php
// Initialiser les variables utilisées dans le formulaire
$title = "";
$body = "";
$published = 0;
$errors = [];
$isEditingPost = false;

// Si l'utilisateur soumet le formulaire pour créer un post
if (isset($_POST['create_post'])) {
    $title = trim($_POST['title']);
    $body = trim($_POST['body']);
    $body = preg_replace('#</?p\s*>#i', '', $body);
    $topic_id = isset($_POST['topic_id']) ? intval($_POST['topic_id']) : null;
    $published = 1;

    // Valider les champs
    if (empty($title)) $errors[] = "Le titre est requis";
    if (empty($body)) $errors[] = "Le contenu est requis";
    if (empty($topic_id)) $errors[] = "Le sujet est requis";

    // Gérer l'image
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === 0) {
        $image = $_FILES['featured_image']['name'];
        $image_path = ROOT_PATH . "/static/images/" . basename($image);
        $upload = move_uploaded_file($_FILES['featured_image']['tmp_name'], $image_path);
        if (!$upload) {
            $errors[] = "Échec de l'upload de l'image.";
        }
    } else {
        $image = null;
    }

    // Si pas d'erreurs, enregistrer le post
    if (count($errors) == 0) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $user_id = $_SESSION['user_id'];
        $success = createPost($user_id, $title, $slug, $image, $body, $published);
        if ($success) {
            $_SESSION['message'] = "Le post a été créé avec succès.";
            header('Location: posts.php'); // DCKR 
            exit();
        } else {
            $errors[] = "Échec lors de la création du post.";
        }
    }
}
?>

<title>Admin | Create Post</title>
</head>
<body>

<!-- admin navbar -->
<?php include(ROOT_PATH . '/includes/admin/header.php') ?>

<div class="container content">
    <!-- Left side menu -->
    <?php include(ROOT_PATH . '/includes/admin/menu.php') ?>

    <!-- Middle form - to create and edit  -->
    <div class="action create-post-div">
        <h1 class="page-title">Create/Edit Post</h1>

        <form method="post" enctype="multipart/form-data" action="<?php echo BASE_URL . 'admin/create_post.php'; ?>">

            <!-- validation errors for the form -->
            <?php include(ROOT_PATH . '/includes/public/errors.php') ?>

            <!-- if editing post, the id is required to identify that post -->
            <?php if ($isEditingPost === true): ?>
                <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
            <?php endif ?>

            <input 
                type="text"
                name="title"
                value="<?php echo $title; ?>" 
                placeholder="Title">

            <label style="float: left; margin: 5px auto 5px;">Featured image</label>
            <input 
                type="file"
                name="featured_image"
            >

            <textarea name="body" id="body" cols="30" rows="10"><?php echo $body; ?></textarea>

            <select name="topic_id">
                <option value="" selected disabled>Choose topic</option>
                <?php foreach ($topics as $topic): ?>
                    <option value="<?php echo $topic['id']; ?>">
                        <?php echo $topic['name']; ?>
                    </option>
                <?php endforeach ?>
            </select>

            <!-- if editing post, display the update button instead of create button -->
            <?php if ($isEditingPost === true): ?> 
                <button type="submit" class="btn" name="update_post">UPDATE</button>
            <?php else: ?>
                <button type="submit" class="btn" name="create_post">Save Post</button>
            <?php endif ?>

        </form>
    </div>
    <!-- // Middle form - to create and edit -->
</div>

</body>
</html>

<script>
    CKEDITOR.replace('body');
</script>
