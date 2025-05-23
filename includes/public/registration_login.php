<?php
session_start();
include('config.php');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errors = [];

if (isset($_POST['login_btn'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username)) {
        $errors[] = "Le nom d'utilisateur est requis.";
    }
    if (empty($password)) {
        $errors[] = "Le mot de passe est requis.";
    }

    if (empty($errors)) {
        // Récupérer aussi le rôle
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
                $_SESSION['role'] = $db_role;  // Stocke le rôle en session

                $stmt->close();
                $conn->close();

                // Redirection selon rôle
                if ($db_role === 'Admin') {
                    header('Location: ../weblog_v0/admin/dashboard.php');
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
}

$conn->close();
?>
