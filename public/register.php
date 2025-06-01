<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

include_once(__DIR__ . '/config.php');
include(ROOT_PATH . '/includes/admin_functions.php');
include(ROOT_PATH . '/admin/post_functions.php');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errors = [];
$username = '';
$email = '';
$user_type = '';
$topic_type = '';
$topics = getAllTopics();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $user_type = $_POST['user_type'] ?? '';
    $topic_type = $_POST['topic_type'] ?? '';

    if (empty($username)) {
        $errors[] = "Le nom d'utilisateur est requis.";
    }

    if (empty($email)) {
        $errors[] = "L'adresse email est requise.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'adresse email n'est pas valide.";
    }

    if (empty($password)) {
        $errors[] = "Le mot de passe est requis.";
    }

    if ($password !== $password_confirm) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    if (empty($user_type) || !in_array($user_type, ['author', 'subscriber'])) {
        $errors[] = "Le type d'utilisateur est requis et doit être valide.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Un utilisateur avec ce nom ou email existe déjà.";
        }
        $stmt->close();
    }

    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        if ($stmt === false) {
            die("Erreur de préparation : " . $conn->error);
        }
        $stmt->bind_param("sss", $username, $email, $password_hash);

        if ($stmt->execute()) {
            $user_id = $conn->insert_id;

            $role_stmt = $conn->prepare("SELECT id FROM roles WHERE name = ?");
            $role_stmt->bind_param("s", $user_type);
            $role_stmt->execute();
            $role_stmt->bind_result($role_id);

            if ($role_stmt->fetch()) {
                $role_stmt->close();
                $insert_role_stmt = $conn->prepare("INSERT INTO role_user (user_id, role_id) VALUES (?, ?)");
                $insert_role_stmt->bind_param("ii", $user_id, $role_id);
                $insert_role_stmt->execute();
                $insert_role_stmt->close();
            } else {
                $errors[] = "Le rôle sélectionné est invalide.";
                $role_stmt->close();
            }

            if ($user_type === 'subscriber' && !empty($topic_type)) {
                // Vérification facultative d’un topic valide ici si nécessaire
            }

            if (empty($errors)) {
                header("Location: login.php");
                exit;
            }

        } else {
            $errors[] = "Erreur lors de l'inscription, veuillez réessayer.";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <?php include('includes/public/head_section.php'); ?>
    <title>Inscription | MyWebSite</title>
    <style>
        form { width: 300px; margin: 30px auto; }
        input, select, button { width: 100%; margin: 6px 0; padding: 8px; }
        .error { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>
    <?php include(ROOT_PATH . '/includes/public/navbar.php'); ?>
    <h2 style="text-align:center;">Inscription</h2>

    <form method="post" action="register.php">
        <?php if (!empty($errors)) : ?>
            <div class="error">
                <ul>
                    <?php foreach ($errors as $error) : ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <input type="text" name="username" placeholder="Nom d'utilisateur" value="<?= htmlspecialchars($username) ?>" required>
        <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($email) ?>" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <input type="password" name="password_confirm" placeholder="Confirmer mot de passe" required>

        <select name="user_type" id="user_type" required>
            <option value="">--Type d'utilisateur--</option>
            <option value="author" <?= ($user_type === 'author') ? 'selected' : '' ?>>Author</option>
            <option value="subscriber" <?= ($user_type === 'subscriber') ? 'selected' : '' ?>>Subscriber</option>
        </select>

        <button type="submit" style="margin-top: 15px;">S'inscrire</button>
    </form>

    <p style="text-align:center;">
        Déjà membre ? <a href="login.php">Se connecter</a>
    </p>
</body>
</html>
