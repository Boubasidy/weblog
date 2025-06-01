<?php
// Démarrage de session avant tout output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('config.php');
include(ROOT_PATH . '/includes/public/head_section.php');

$errors = [];

if (isset($_POST['login_btn'])) {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username)) {
        $errors[] = "Le nom d'utilisateur est requis.";
    }
    if (empty($password)) {
        $errors[] = "Le mot de passe est requis.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("
            SELECT u.id, u.username, u.password, r.name AS role
            FROM users u
            JOIN role_user ru ON ru.user_id = u.id
            JOIN roles r ON r.id = ru.role_id
            WHERE u.username = ?
        "); 
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $db_username, $db_password_hash, $db_role);
            $stmt->fetch();

            if (password_verify($password, $db_password_hash)) {
                // Login OK
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $db_username;
                $_SESSION['role'] = $db_role;

                $_SESSION['user'] = [
                    'id' => $id,
                    'username' => $db_username,
                    'role' => $db_role
                ];

                $stmt->close();
                $conn->close();

                if ($db_role === 'Admin') {
                    header('Location: admin/dashboard.php');
                } else {
                    header('Location: index.php');
                }
                exit;
            } else {
                $errors[] = "Mot de passe incorrect.";
            }
        } else {
            $errors[] = "Nom d'utilisateur introuvable.";
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <title>MyWebSite | Sign in</title>
    <!-- ici on suppose que head_section.php contient les meta, link css, etc -->
</head>

<body>

    <div class="container">

        <!-- Navbar -->
        <?php include(ROOT_PATH . '/includes/public/navbar.php'); ?>
        <!-- // Navbar -->

        <div style="width: 40%; margin: 20px auto;">
            <form method="post" action="login.php"> 
                <h2>Login</h2>
                <?php if (!empty($errors)) : ?>
                    <div class="errors">
                        <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <input type="text" name="username" placeholder="Username" 
                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">

                <input type="password" name="password" value="" placeholder="Password">

                <button type="submit" class="btn" name="login_btn">Login</button>
                <p>
                    Not yet a member? <a href="register.php">Sign up</a>
                </p>
            </form>
        </div>

    </div>
    <!-- // container -->

    <!-- Footer -->
    <?php include(ROOT_PATH . '/includes/public/footer.php'); ?>
    <!-- // Footer -->

</body>

</html>


<?php
ob_end_flush(); // vide le buffer et envoie tout au navigateur proprement
?>
