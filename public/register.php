<?php
include('config.php');

// Connexion à la base de données
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errors = [];
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer et nettoyer les données du formulaire
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

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

    // Si pas d’erreur, insérer en base
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password_hash);

        if ($stmt->execute()) {
            // Inscription OK, redirection vers login ou page d’accueil
            header("Location: login.php");
            exit;
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
        input, button { width: 100%; margin: 6px 0; padding: 8px; }
        .error { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>
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
        <button type="submit">S'inscrire</button>
    </form>

    <p style="text-align:center;">
        Déjà membre ? <a href="login.php">Se connecter</a>
    </p>
</body>
</html>
