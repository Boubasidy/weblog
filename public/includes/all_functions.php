<?php
// DCKR SESSION sécurisée (Docker-compatible)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// DCKR Connexion DB via config Docker
include_once(__DIR__ . '/../config.php');


// Affichage tableau de posts avec colonne "Afficher"
function displayPostsTable($posts)
{
    if (empty($posts)) {
        echo "<p>Aucun article trouvé.</p>";
        return;
    }

    $showDisplayColumn = isset($_SESSION['role']) && !empty($_SESSION['role']);

    echo '<table class="styled-table">';
    echo '<thead>
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Auteur</th>
                <th>Topic</th>
                <th>Publié</th>
                <th>Date</th>';

    if ($showDisplayColumn) {
        echo '<th>Afficher</th>';
    }

    echo '</tr></thead>';
    echo '<tbody>';

    foreach ($posts as $post) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($post['id']) . '</td>';
        echo '<td>' . htmlspecialchars($post['title']) . '</td>';
        echo '<td>' . htmlspecialchars($post['author']) . '</td>';
        echo '<td>' . htmlspecialchars($post['topic']) . '</td>';
        echo '<td class="' . ($post['published'] ? 'status-yes' : 'status-no') . '">'
            . ($post['published'] ? 'Oui' : 'Non') . '</td>';
        echo '<td>' . htmlspecialchars($post['created_at']) . '</td>';

        if ($showDisplayColumn) {
            echo '<td><a class="btn-display" href="?';
            if (isset($_GET['topic'])) {
                echo 'topic=' . urlencode($_GET['topic']) . '&';
            }
            echo 'post_id=' . urlencode($post['id']) . '">Afficher</a></td>';
        }

        echo '</tr>';
    }

    echo '</tbody></table>';
}

//  Affichage contenu complet d’un post
function displayPostContent($post)
{
    if (!$post) {
        echo '<p>Article introuvable.</p>';
        return;
    }

    echo '<div class="post-content">';
    echo '<h2>' . htmlspecialchars($post['title']) . '</h2>';
    echo '<p><strong>Auteur :</strong> ' . htmlspecialchars($post['author']) . '</p>';
    echo '<p><strong>Topic :</strong> ' . (isset($post['topic']) ? htmlspecialchars($post['topic']) : 'Non spécifié') . '</p>';
    echo '<p><strong>Date :</strong> ' . htmlspecialchars($post['created_at']) . '</p>';
    echo '<p><strong>Mis à jour le :</strong> ' . htmlspecialchars($post['updated_at']) . '</p>';

    if (!empty($post['image'])) {
        $image_filename = basename($post['image']);
        $image_server_path = ROOT_PATH . "/static/images/" . $image_filename;
        $image_web_path = "/static/images/" . rawurlencode($image_filename);

        if (file_exists($image_server_path)) {
            @chmod(ROOT_PATH . "/static", 0777);
            @chmod(ROOT_PATH . "/static/images", 0777);
            @chmod($image_server_path, 0777);

            echo '<img src="' . $image_web_path . '" alt="' . htmlspecialchars($post['title']) . '" style="max-width:100%;height:auto;margin-bottom:20px;">';
        } else {
            echo '<p style="color:red;">Image introuvable : ' . htmlspecialchars($image_filename) . '</p>';
        }
    }

    echo '<div>' . nl2br(htmlspecialchars($post['body'])) . '</div>';
    echo '</div>';
}
