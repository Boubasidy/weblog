<?php
// DCKR : Vérification de l’état de session avant de l’ouvrir
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // DCKR : Démarrage de session sécurisé
}
include('../config.php');

// Récupère tous les sujets depuis la BDD
function getAllTopics()
{
    $conn = getDBConnection();
    $topics = [];

    $sql = "SELECT id, name, slug FROM topics ORDER BY id DESC";
    $result = $conn->query($sql);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $topics[] = $row;
        }
    }

    $conn->close();
    return $topics;
}

// Crée un nouveau post
function createPost($user_id, $title, $slug, $image, $body, $published, $topic_id)
{
    $conn = getDBConnection();

    // Étape 1 : Insertion dans la table `posts`
    $sql = "INSERT INTO posts (user_id, title, slug, views, image, body, published, created_at, updated_at)
            VALUES (?, ?, ?, 0, ?, ?, ?, NOW(), NOW())";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Erreur de préparation de la requête : " . $conn->error); // DCKR
    }

    $stmt->bind_param("issssi", $user_id, $title, $slug, $image, $body, $published);
    $success = $stmt->execute();

    if (!$success) {
        echo "Erreur lors de l'insertion : " . $stmt->error;
        $stmt->close();
        $conn->close();
        return false;
    }

    $post_id = $stmt->insert_id;
    $stmt->close();

    // Étape 2 : Association avec un topic dans `post_topic`
    $sql2 = "INSERT INTO post_topic (post_id, topic_id) VALUES (?, ?)";
    $stmt2 = $conn->prepare($sql2);
    if (!$stmt2) {
        die("Erreur préparation (post_topic) : " . $conn->error);
    }

    $stmt2->bind_param("ii", $post_id, $topic_id);
    $success_topic = $stmt2->execute();

    if (!$success_topic) {
        echo "Erreur lors de l'association post/topic : " . $stmt2->error;
    }

    $stmt2->close();
    $conn->close();

    return $success_topic ? $post_id : false;
}

// Supprime un post par son ID
function deletePosteById($id)
{
    $conn = getDBConnection();
    if (!$conn) {
        die("Erreur de connexion à la base de données."); // DCKR
    }

    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
    if (!$stmt) {
        die("Erreur de préparation de la requête : " . $conn->error); // DCKR
    }

    $stmt->bind_param("i", $id);
    $success = $stmt->execute();

    if (!$success) {
        echo "Erreur d'exécution : " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    return $success;
}

// Met à jour un post existant
function updatePost($id, $title, $slug, $views, $image, $body, $published, $topic_id)
{
    $conn = getDBConnection();

    $sql = "UPDATE posts 
            SET title = ?, slug = ?, views = ?, image = ?, body = ?, published = ?, updated_at = NOW() 
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Erreur de préparation de la requête : " . $conn->error); // DCKR
    }

    $stmt->bind_param("ssisiii", $title, $slug, $views, $image, $body, $published, $id);
    $success = $stmt->execute();
    $stmt->close();

    if (!$success) {
        echo "Erreur lors de la mise à jour : " . $conn->error;
        $conn->close();
        return false;
    }

    // Met à jour ou insère le topic dans `post_topic`
    $sql2 = "REPLACE INTO post_topic (post_id, topic_id) VALUES (?, ?)";
    $stmt2 = $conn->prepare($sql2);
    if (!$stmt2) {
        die("Erreur de préparation (post_topic) : " . $conn->error);
    }

    $stmt2->bind_param("ii", $id, $topic_id);
    $success2 = $stmt2->execute();

    if (!$success2) {
        echo "Erreur post_topic : " . $stmt2->error;
    }

    $stmt2->close();
    $conn->close();

    return $success2;
}

// Récupère un post par son ID
function getPostById($id)
{
    $conn = getDBConnection();

    $stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
    if (!$stmt) {
        die("Erreur de préparation : " . $conn->error); // DCKR
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();
    $post = $result->fetch_assoc();

    $stmt->close();
    $conn->close();

    return $post ?: null;
}

// Récupère tous les posts
function getAllPosts()
{
    $conn = getDBConnection();
    $posts = [];

    $sql = "SELECT p.id, p.title, p.slug, p.image, p.body, p.published, p.created_at, p.updated_at,
                   u.username AS author, t.name AS topic
            FROM posts p
            LEFT JOIN users u ON p.user_id = u.id
            LEFT JOIN post_topic pt ON p.id = pt.post_id
            LEFT JOIN topics t ON pt.topic_id = t.id
            ORDER BY p.created_at DESC";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
    }

    $conn->close();
    return $posts;
}
?>
