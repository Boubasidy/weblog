<?php
// inserer un topic 
function insertTopic($name, $slug) {
    $conn = getDBConnection();

    // 1. Récupérer l'id max
    $result = $conn->query("SELECT MAX(id) as max_id FROM topics");
    if (!$result) {
        die("Erreur lors de la récupération de l'id max : " . $conn->error);
    }
    $row = $result->fetch_assoc();
    $new_id = $row['max_id'] ? $row['max_id'] + 1 : 1;  // si pas de topic, on commence à 1

    // 2. Préparer la requête avec l'id fourni
    $sql = "INSERT INTO topics (id, name, slug) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Erreur préparation requête : " . $conn->error);
    }

    $stmt->bind_param("iss", $new_id, $name, $slug);
    $success = $stmt->execute();

    if ($success) {
        $_SESSION['message'] = "Topic ajouté avec succès !";
        header("Location: topics.php");
        exit;
    } else {
        // Ici, pour propager l'erreur, tu peux gérer selon ton besoin
        die("Erreur lors de l'insertion : " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
}


?>