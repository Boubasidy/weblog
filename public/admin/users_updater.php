<?php
// Démarrage sécurisé de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../config.php');
include('../includes/admin_functions.php');

// Pour debug, à activer seulement en dev
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $user_id = $_POST['id'] ?? null;
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (empty($username)) {
        $errors[] = "Le nom d'utilisateur est requis.";
    }
    if (empty($email)) {
        $errors[] = "L'email est requis.";
    }

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

// Charger les données à éditer (via GET edit-admin)
if (isset($_GET['edit-admin'])) {
    $user = getUserById($_GET['edit-admin']);
    if (!$user) {
        die("Utilisateur introuvable.");
    }
} else {
    die("Aucun ID fourni.");
}

// Fonction d'échappement HTML 
if (!function_exists('e')) {
    function e($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}
?>

<?php include(ROOT_PATH . '/includes/admin/head_section.php'); ?>
<title>Modifier Utilisateur</title>
</head>

<body>
<?php include(ROOT_PATH . '/includes/admin/header.php') ?>

<h2 style="text-align: center;">Modifier Utilisateur</h2>

<?php if (!empty($errors)) : ?>
    <div class="error" style="color: red; text-align: center; margin-bottom: 1em;">
        <ul>
            <?php foreach ($errors as $err) : ?>
                <li><?= e($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" action="users_updater.php?edit-admin=<?= e($user['id']) ?>">
    <input type="hidden" name="id" value="<?= e($user['id']) ?>">

    <div class="form-group">
        <label for="username">Nom d'utilisateur</label><br>
        <input id="username" type="text" name="username" value="<?= e($user['username']) ?>" required>
    </div>

    <div class="form-group">
        <label for="email">Email</label><br>
        <input id="email" type="email" name="email" value="<?= e($user['email']) ?>" required>
    </div>

    <button type="submit" name="update_user">Mettre à jour</button>
</form>

<p style="text-align: center; margin-top: 1em;"><a href="users.php">← Retour à la liste</a></p>

</body>
</html>
