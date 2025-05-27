<?php 
    session_start();
    include('../config.php');
    // retrieve all topics 
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
    // create a new post
function createPost($user_id, $title, $slug, $image, $body, $published, $topic_id)
{
    $conn = getDBConnection();

    // 1. Insérer le post dans la table `posts`
    $sql = "INSERT INTO posts (user_id, title, slug, views, image, body, published, created_at, updated_at)
            VALUES (?, ?, ?, 0, ?, ?, ?, NOW(), NOW())";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Erreur préparation : " . $conn->error);
    }

    $stmt->bind_param("issssi", $user_id, $title, $slug, $image, $body, $published);
    $success = $stmt->execute();

    if (!$success) {
        echo "Erreur lors de l'insertion du post : " . $stmt->error;
        $stmt->close();
        $conn->close();
        return false;
    }

    $post_id = $stmt->insert_id;
    $stmt->close();

    // 2. Associer le post à un topic dans la table `post_topic`
    $sql2 = "INSERT INTO post_topic (post_id, topic_id) VALUES (?, ?)";
    $stmt2 = $conn->prepare($sql2);
    if (!$stmt2) {
        die("Erreur préparation (post_topic) : " . $conn->error);
    }

    $stmt2->bind_param("ii", $post_id, $topic_id);
    $success_topic = $stmt2->execute();

    if (!$success_topic) {
        echo "Erreur lors de l'association post/topic : " . $stmt2->error;
        $stmt2->close();
        $conn->close();
        return false;
    }

    $stmt2->close();
    $conn->close();

    return $post_id;
}

   // Exemple : supprimer un poste par ID
    function deletePosteById($id)
    {
        $conn = getDBConnection();
        if (!$conn) {
            die("Erreur de connexion à la base de données.");
        }

        $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
        if (!$stmt) {
            die("Erreur préparation requête : " . $conn->error);
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
// pour editer un post
function updatePost($id, $title, $slug, $views, $image, $body, $published) {
    $conn = getDBConnection();

    $sql = "UPDATE posts 
            SET title = ?, slug = ?, views = ?, image = ?, body = ?, published = ?, updated_at = NOW() 
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Erreur préparation requête : " . $conn->error);
    }

    // Correction ici : ajout de $id à la fin
    $stmt->bind_param("ssisiii", $title, $slug, $views, $image, $body, $published, $id);

    $success = $stmt->execute();

    if (!$success) {
        echo "Erreur lors de la mise à jour : " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    return $success;
}

    // recuperer un poste par son id
    function getPostById($id)
    {
        $conn = getDBConnection();

        $sql = "SELECT * FROM posts WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die("Erreur de préparation de la requête : " . $conn->error);
        }

        $stmt->bind_param("i", $id); // 'i' = entier
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            return $result->fetch_assoc(); // retourne le post sous forme de tableau associatif
        } else {
            return null; // ou false selon ton usage
        }
    }

    // recupere tous les topics 
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


