<?php
session_start();
include('../config.php');
include('../includes/admin_functions.php');
// Traitement du formulaire
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $user_id = $_POST['id'];
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);

    if (empty($username)) $errors[] = "Le nom d'utilisateur est requis.";
    if (empty($email)) $errors[] = "L'email est requis.";

    if (empty($errors)) {
        if (updateUser($user_id, $username, $email)) {
            $_SESSION['message'] = "Utilisateur mis à jour avec succès.";
            header("Location: users.php");
            exit;
        } else {
            $errors[] = "Erreur lors de la mise à jour.";
        }
    }
}

// Charger les données à éditer
if (isset($_GET['edit-admin'])) {
    $user = getUserById($_GET['edit-admin']);
    if (!$user) {
        die("Utilisateur introuvable.");
    }
} else {
    die("Aucun ID fourni.");
}
?>

<?php include(ROOT_PATH . '/includes/admin/head_section.php'); ?>
</head>

<body>
    <?php include(ROOT_PATH . '/includes/admin/header.php') ?>
    <h2 style="text-align: center;">Modifier Utilisateur</h2>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= e($err) ?></li>
                <?php endforeach ?>
            </ul>
        </div>
    <?php endif ?>

    <form method="POST" action="users_updater.php?id=<?= e($user['id']) ?>">
        <input type="hidden" name="id" value="<?= e($user['id']) ?>">

        <div class="form-group">
            <label>Nom d'utilisateur</label>
            <input type="text" name="username" value="<?= e($user['username']) ?>">
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?= e($user['email']) ?>">
        </div>

        <button type="submit" name="update_user">Mettre à jour</button>
    </form>

    <p style="text-align : center;"><a href="users.php">← Retour à la liste</a></p>
</body>

</html>