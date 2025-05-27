<?php
include('config.php');
include(ROOT_PATH . '/includes/admin_functions.php');
include(ROOT_PATH . '/admin/post_functions.php');
// Connexion à la base de données
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
    // Récupérer et nettoyer les données du formulaire
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $user_type = $_POST['user_type'] ?? '';
    $topic_type = $_POST['topic_type'] ?? '';

    // Validation simple
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

    // Validation user_type
    if (empty($user_type) || !in_array($user_type, ['author', 'subscriber'])) {
        $errors[] = "Le type d'utilisateur est requis et doit être valide.";
    }

    // Si subscriber, topic_type est requis
    if ($user_type === 'subscriber') {
        if (empty($topic_type) || !in_array($topic_type, ['journal', 'motivation', 'inspiration', 'conseil'])) {
            $errors[] = "Le type de topic est requis pour un abonné.";
        }
    } else {
        $topic_type = null; // pas applicable si author
    }

    // Vérifier que le username ou l’email n’existent pas déjà
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

    // Insérer dans users (sans topic_type)
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    if ($stmt === false) {
        die("Erreur de préparation : " . $conn->error);
    }
    $stmt->bind_param("sss", $username, $email, $password_hash);

    if ($stmt->execute()) {
            // Récupérer l'id du nouvel utilisateur
            $user_id = $conn->insert_id;

            // Si subscriber, insérer dans role_user avec le role_id correspondant au topic
            if ($user_type === 'subscriber' && !empty($topic_type)) {
                // Récupérer le role_id correspondant au topic_type
                $role_stmt = $conn->prepare("SELECT id FROM roles WHERE name = ?");
                $role_stmt->bind_param("s", $user_type);
                $role_stmt->execute();
                $role_stmt->bind_result($role_id);
                if ($role_stmt->fetch()) {
                    // Insérer dans role_user
                    $role_stmt->close();

                    $insert_role_stmt = $conn->prepare("INSERT INTO role_user (user_id, role_id) VALUES (?, ?)");
                    $insert_role_stmt->bind_param("ii", $user_id, $role_id);
                    $insert_role_stmt->execute();
                    $insert_role_stmt->close();
                } else {
                    $errors[] = "Le type de topic choisi est invalide.";
                    $role_stmt->close();
                    // Ici, tu peux décider si tu annules l'inscription ou pas
                }
            }

            if (empty($errors)) {
                $safe_topic_type = urlencode($topic_type);
                header("Location: login.php?topic_type=$safe_topic_type");
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

        <div id="topic-container" style="display: none; margin-top:10px;">
            <select name="topic_type" id="topic_type">
            <option value="">--Type de topic--</option>
            <?php foreach ($topics as $topic): ?>
                <?php
                    $value = $topic['slug'];
                    $label = ucfirst($topic['name']);
                    $selected = ($topic_type === $value) ? 'selected' : '';
                ?>
                <option value="<?= htmlspecialchars($value) ?>" <?= $selected ?>>
                    <?= htmlspecialchars($label) ?>
                </option>
        <?php endforeach; ?>
</select>

        <button type="submit">S'inscrire</button>
    </form>

    <p style="text-align:center;">
        Déjà membre ? <a href="login.php">Se connecter</a>
    </p>

    <script>
        const userTypeSelect = document.getElementById('user_type');
        const topicContainer = document.getElementById('topic-container');
        const topicSelect = document.getElementById('topic_type');

        function toggleTopicSelect() {
            if(userTypeSelect.value === 'subscriber') {
                topicContainer.style.display = 'block';
                topicSelect.setAttribute('required', 'required');
            } else {
                topicContainer.style.display = 'none';
                topicSelect.removeAttribute('required');
                topicSelect.value = '';
            }
        }

        userTypeSelect.addEventListener('change', toggleTopicSelect);

        // Affiche correctement au chargement si formulaire renvoyé avec erreurs
        window.addEventListener('load', toggleTopicSelect);
    </script>
</body>
</html>
