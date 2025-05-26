<?php
include('../config.php');
include(ROOT_PATH . '/includes/admin_functions.php');
include(ROOT_PATH . '/admin/post_functions.php');

session_start(); // au cas où il n'est pas déjà lancé

// Vérifie si un post doit être supprimé via l'URL
echo '<pre>'; 
print_r($_GET); 
echo '</pre>';
if (isset($_GET['id']) && isset($_GET['role'])) {
    $post_id = intval($_GET['id']);
    $role = $_GET['role'];
    // Optionnel : Vérifier que l'utilisateur est admin
    if (!isset($_SESSION['user_id'])&& $role === "Admin") {
        $_SESSION['message'] = "Action non autorisée.";
        header('Location: posts.php');
        exit();
    }

    // Supprimer le post
    $success = deletePosteById($post_id);

    if ($success) {
        $_SESSION['message'] = "Le post a été supprimé avec succès.";
    } else {
        $_SESSION['message'] = "Erreur lors de la suppression du post.";
    }

    header('Location: posts.php');
    exit();
}
?>
