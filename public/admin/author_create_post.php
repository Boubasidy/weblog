<?php
// Similaire à create_post.php mais pour Author
$topics = getAllTopics();
$title = "";
$body = "";
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_post'])) {
    $title = trim($_POST['title']);
    $body = trim($_POST['body']);
    $topic_id = $_POST['topic_id'] ?? null;
    $published = 1;

    if (empty($title)) $errors[] = "Titre requis.";
    if (empty($body)) $errors[] = "Contenu requis.";
    if (empty($topic_id)) $errors[] = "Sujet requis.";

    $image = null;
    if (!empty($_FILES['featured_image']['name'])) {
        $image = $_FILES['featured_image']['name'];
        $image_path = ROOT_PATH . "/static/images/" . basename($image);
        if (!move_uploaded_file($_FILES['featured_image']['tmp_name'], $image_path)) {
            $errors[] = "Échec de l'upload de l'image.";
        }
    }

    if (empty($errors)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $user_id = $_SESSION['user']['id'];
        $success = createPost($user_id, $title, $slug, $image, $body, $published, $topic_id);
        if ($success) {
            echo "<p style='color: green;'>Post créé avec succès !</p>";
        } else {
            $errors[] = "Erreur lors de la création.";
        }
    }
}
?>

<div class="action create-post-div">
    <h1 class="page-title">Créer un nouveau post</h1>

    <form method="post" enctype="multipart/form-data" action="">

        <!-- Affichage des erreurs -->
        <?php foreach ($errors as $error): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endforeach; ?>

        <!-- Titre -->
        <input 
            type="text"
            name="title"
            value="<?php echo htmlspecialchars($title); ?>" 
            placeholder="Titre">

        <!-- Image -->
        <label style="float: left; margin: 5px auto 5px;">Image à la une</label>
        <input 
            type="file"
            name="featured_image"
        >

        <!-- Contenu -->
        <textarea name="body" id="body" cols="30" rows="10"><?php echo htmlspecialchars($body); ?></textarea>

        <!-- Sujet -->
        <select name="topic_id">
            <option value="" selected disabled>Choisir un sujet</option>
            <?php foreach ($topics as $topic): ?>
                <option value="<?php echo $topic['id']; ?>">
                    <?php echo htmlspecialchars($topic['name']); ?>
                </option>
            <?php endforeach ?>
        </select>

        <!-- Bouton -->
        <button type="submit" class="btn" name="create_post">Créer</button>

    </form>
</div>

<script>
    CKEDITOR.replace('body');


</script>

