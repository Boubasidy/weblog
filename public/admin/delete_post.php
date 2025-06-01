<?php
include('../config.php'); 
include(ROOT_PATH . '/includes/admin_functions.php'); 
include(ROOT_PATH . '/admin/post_functions.php'); 

//DCKR : démarre la session uniquement si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start(); //DCKR : gestion de session
}

//DCKR : debug éventuel (à retirer en production)
// echo '<pre>'; print_r($_GET); echo '</pre>';

//DCKR : sécurisation de la suppression via GET
if (isset($_GET['id']) && isset($_GET['role'])) {
    $post_id = intval($_GET['id']); //DCKR : conversion explicite en entier
    $role = htmlspecialchars($_GET['role']); //DCKR : protection contre les injections

    //DCKR : contrôle d'autorisation
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== "Admin") {
        $_SESSION['message'] = "Action non autorisée.";
        header('Location: ' . BASE_URL . 'admin/posts.php'); //DCKR : redirection sûre
        exit();
    }

    //DCKR : suppression du post via fonction dédiée
    $success = deletePosteById($post_id);

    if ($success) {
        $_SESSION['message'] = "Le post a été supprimé avec succès.";
    } else {
        $_SESSION['message'] = "Erreur lors de la suppression du post.";
    }

    header('Location: ' . BASE_URL . 'admin/posts.php'); //DCKR : redirection sûre
    exit();
} else {
    $_SESSION['message'] = "Requête invalide.";
    header('Location: ' . BASE_URL . 'admin/posts.php'); //DCKR : fallback sécurisé
    exit();
}
?>
