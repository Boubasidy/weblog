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

?>


