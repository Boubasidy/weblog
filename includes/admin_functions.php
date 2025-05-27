<?php
session_start();
include('../config.php');

// Connexion à la base, à appeler dans les fonctions
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Erreur de connexion à la base: " . $conn->connect_error);
    }
    return $conn;
}

// Vérifie si un admin est connecté, sinon redirige vers login
function adminOnly() {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        header('Location: ../login.php');
        exit;
    }
}

// Fonction pour sécuriser l'affichage (échapper les sorties)
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Exemple : récupérer tous les utilisateurs
function getAllUsers() {
    $conn = getDBConnection();
    $users = [];
    $sql = "SELECT id, username, email, role FROM users ORDER BY id DESC";
    $result = $conn->query($sql);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }

    $conn->close();
    return $users;
}

// Exemple : récupérer un utilisateur par ID
function getUserById($id) {
    $conn = getDBConnection();

    $stmt = $conn->prepare("
        SELECT u.id, u.username, u.email, roles.name AS role
        FROM users u
        JOIN role_user ru ON ru.user_id = u.id
        JOIN roles ON roles.id = ru.role_id
        WHERE u.id = ?
    ");

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $stmt->close();
    $conn->close();

    return $user;
}

// Mettre à jour un utilisateur
function updateUser($id, $username, $email) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $username, $email, $id);
    $success = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $success;
}

// Exemple : supprimer un utilisateur par ID
function deleteUserById($id) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $success = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $success;
}
// recuperation  des roles 
function getAdminRoles() {
    $conn = getDBConnection(); 
    $roles = [];

    $sql = "SELECT * from roles";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $roles[] = $row;
        }
    }
    $conn->close();
    return $roles;
}
// recuperation des admins
function getAdminUsers() {
    $conn = getDBConnection();
    $admins = [];

    $sql = "
        SELECT u.*, r.name AS role
        FROM users u
        JOIN role_user ru ON ru.user_id = u.id
        JOIN roles r ON r.id = ru.role_id
        WHERE r.name = 'Admin'
    ";

    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $admins[] = $row;
        }
    }

    $conn->close();
    return $admins;
}

// recuperation des Authors
function getOthers() {
    $conn = getDBConnection();
    $others = [];

    $sql = "
        SELECT u.*, r.name AS role
        FROM users u
        JOIN role_user ru ON ru.user_id = u.id
        JOIN roles r ON r.id = ru.role_id
        WHERE r.name in ('Author' , 'Subscriber')
    ";

    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $others[] = $row;
        }
    }

    $conn->close();
    return $others;
}

// Plus bas, fonctions pour créer/modifier/supprimer un admin user à compléter selon ton besoin

// Tu peux rajouter d’autres fonctions CRUD pour posts, topics, etc.
?>
