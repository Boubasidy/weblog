<?php 
    session_start();
    include('../config.php');
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
    function createPost($user_id, $title, $slug, $image, $body, $published)
    {
        $conn = getDBConnection();

        $sql = "INSERT INTO posts (user_id, title, slug, views, image, body, published, created_at, updated_at)
                VALUES (?, ?, ?, 0, ?, ?, ?, NOW(), NOW())";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Erreur préparation de la requête : " . $conn->error);
        }

        $stmt->bind_param("issssi", $user_id, $title, $slug, $image, $body, $published);
        $success = $stmt->execute();

        if (!$success) {
            echo "Erreur lors de l'insertion : " . $stmt->error;
        }

        $stmt->close();
        $conn->close();

        return $success;
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
    function updatePost($id, $title, $slug, $views, $image, $body, $published, $topic_id) {
        $conn = getDBConnection();

        $sql = "UPDATE posts SET title = ?, slug = ?, views = ?, image = ?, body = ?, published = ?, topic_id = ?, updated_at = NOW() WHERE id = ?";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Erreur préparation requête : " . $conn->error);
        }

        $stmt->bind_param("ssisiiii", $title, $slug, $views, $image, $body, $published, $topic_id, $id);

        $success = $stmt->execute();

        if (!$success) {
            echo "Erreur lors de la mise à jour : " . $stmt->error;
        }

        $stmt->close();
        $conn->close();

        return $success;
    }

?>


