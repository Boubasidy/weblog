<?php
include('config.php');
include(ROOT_PATH . '/includes/admin_functions.php');
include(ROOT_PATH . '/includes/all_functions.php');
include(ROOT_PATH . '/admin/post_functions.php');
// DCKR SESSION sécurisée (Docker-compatible)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <?php include('includes/public/head_section.php'); ?>
    <title>MyWebSite | Home</title>

    <style>
        .styled-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 0.95em;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .styled-table thead tr {
            background-color: #009879;
            color: #ffffff;
            text-align: left;
        }

        .styled-table th,
        .styled-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #dddddd;
        }

        .styled-table tbody tr {
            border-bottom: 1px solid #dddddd;
        }

        .styled-table tbody tr:nth-of-type(even) {
            background-color: #f3f3f3;
        }

        .styled-table tbody tr:last-of-type {
            border-bottom: 2px solid #009879;
        }

        .status-yes {
            color: green;
            font-weight: bold;
        }

        .status-no {
            color: red;
            font-weight: bold;
        }

        .topic-list {
            margin: 20px 0;
        }

        .topic-list a {
            margin-right: 15px;
            padding: 8px 12px;
            background-color: #009879;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }

        .topic-list a:hover {
            background-color: #007f63;
        }

        .post-content {
            margin-top: 30px;
            padding: 20px;
            border: 1px solid #009879;
            border-radius: 8px;
            background: #f9f9f9;
        }

        .post-content img {
            max-width: 100%;
            height: auto;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .btn-display {
            padding: 6px 12px;
            background-color: #009879;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
        }

        .btn-display:hover {
            background-color: #007f63;

        }

        .table-div {
            margin-top: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table th, .table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        .table th {
            background-color: #f2f2f2;
        }

        .table tr:nth-child(even) {
            background-color: #f9f9f9;
        }


    </style>
</head>

<body>
    <div class="container">

        <!-- Navbar -->
        <?php include(ROOT_PATH . '/includes/public/navbar.php'); ?>
        <!-- // Navbar -->

        <!-- Banner -->
        <?php include(ROOT_PATH . '/includes/public/banner.php'); ?>
        <!-- // Banner -->

        <!-- Messages -->
        <!-- (zone prévue si besoin d'afficher des messages ou alertes) -->
        <!-- // Messages -->

        <!-- content -->
        <div class="content">


            <?php
            // Logique d’affichage principale
            $post_to_show = null;

            // Le visiteur non connecté n'a pas de $_SESSION['role']
            if (empty($_SESSION["role"])) {
                // Visiteur non connecté => affiche tous les posts
                $posts = getAllPosts();
                displayPostsTable($posts);
            } elseif ($_SESSION["role"] === "Subscriber") {
                // Abonné connecté
                echo '<h2 class="content-title">Recent Articles</h2>';
                echo '<hr>';
                // Récupérer la liste des topics
                $topics = getAllTopics();

                // Récupérer topic sélectionné dans l'URL (ex: ?topic=motivation)
                $selected_topic = $_GET['topic'] ?? null;

                if (!$selected_topic) {
                    // Pas de topic sélectionné => afficher liste des topics pour choix
                    echo '<div class="topic-list">';
                    echo '<p>Choisissez un topic :</p>';
                    foreach ($topics as $topic) {
                        $topic_slug = htmlspecialchars($topic['slug']);
                        $topic_name = ucfirst(htmlspecialchars($topic['name']));
                        echo "<a href=\"?topic=$topic_slug\">" . $topic_name . "</a>";
                    }
                    echo '</div>';
                } else {
                    // Topic sélectionné => afficher les posts liés à ce topic
                    $posts = getPostsByTopic($selected_topic);
                    echo "<h3>Articles du topic : " . ucfirst(htmlspecialchars($selected_topic)) . "</h3>";
                    displayPostsTable($posts);

                    // Lien retour vers liste topics
                    echo '<p><a href="index.php">← Choisir un autre topic</a></p>';
                }

                // Si post_id est passé, récupérer ce post pour affichage
                if (isset($_GET['post_id'])) {
                    $post_id = intval($_GET['post_id']);
                    $post_to_show = getPostById($post_id);
                }
            } 
            //condition : affichage de la pagepour un AUTHOR
            elseif ($_SESSION["role"] === "Author") {
                // Onglet actif (par défaut : 'recent')
                $tab = $_GET['tab'] ?? 'recent';

                // Affichage des boutons
                echo '<div style="margin-bottom: 20px;">';
                echo '<a href="?tab=recent" class="btn-display" style="margin-right: 10px;">Articles récents</a>';
                echo '<a href="?tab=create" class="btn-display" style="margin-right: 10px;">Créer un post</a>';
                echo '<a href="?tab=subscribers" class="btn-display">Abonnés</a>';
                echo '</div>';

                $user_id = $_SESSION['user']['id'];

                // Onglet actif
                //$tab = $_GET['tab'] ?? 'recent';

                // Afficher le contenu selon l'onglet
                if ($tab === 'recent') {
                    $posts = getPostsByAuthorId($user_id);
                    displayPostsTable($posts);

                    if (isset($_GET['post_id'])) {
                        $post_id = intval($_GET['post_id']);
                        $post_to_show = getPostById($post_id);
                    }
                } elseif ($tab === 'create') {
                    include(ROOT_PATH . '/admin/author_create_post.php');
                } elseif ($tab === 'subscribers') {
                    $subscribers = getSubscribers();

                    echo '<div class="table-div">';
                    echo '<h2>Abonnés</h2>';
                    if (empty($subscribers)) {
                        echo '<h3>Aucun abonné trouvé.</h3>';
                    } else {
                        echo '<table class="table">';
                        echo '<thead><tr><th>N°</th><th>Nom d’utilisateur</th><th>Email</th></tr></thead>';
                        echo '<tbody>';
                        foreach ($subscribers as $index => $subscriber) {
                            echo '<tr>';
                            echo '<td>' . ($index + 1) . '</td>';
                            echo '<td>' . htmlspecialchars($subscriber['username']) . '</td>';
                            echo '<td>' . htmlspecialchars($subscriber['email']) . '</td>';
                            echo '</tr>';
                        }
                        echo '</tbody></table>';
                    }
                    echo '</div>';

                }

            }

            

            // Afficher le contenu du post sélectionné en dessous du tableau, s’il y en a un
            if ($post_to_show) {
                displayPostContent($post_to_show);
            }
            ?>

        </div>

        <!-- Scroll Sections -->

        <!-- NEWS -->
        <section id="news" style="padding: 50px 20px; background-color: #f0f0f0;">
            <h2>News</h2>
            <p>L'intelligence artificielle progresse rapidement. Le nouveau modèle Google Veo 3 permet de générer des vidéos de haute qualité à partir d’un simple texte. Les usages éducatifs, artistiques et professionnels sont en pleine expansion.</p>
        </section>

        <!-- CONTACT -->
        <section id="contact" style="padding: 50px 20px; background-color: #e8f5e9;">
            <h2>Contact</h2>
            <p>Vous pouvez nous contacter à l'adresse suivante : <strong>contact@insa-cvl.fr</strong></p>
        </section>

        <!-- ABOUT -->
        <section id="about" style="padding: 50px 20px; background-color: #e3f2fd;">
            <h2>À propos</h2>
            <p>Le projet <strong>Weblog</strong> était initialement un simple squelette fourni sur le GitHub du professeur A.Adell. Les étudiants <strong>Y.Mazroui</strong> et <strong>BS.Boubacar</strong> ont relevé le défi de le transformer en un site dynamique et fonctionnel, avec des améliorations techniques, une interface modernisée, et une vraie logique de publication de contenu.</p>
        </section>

    </div>
    <!-- // container -->

    <!-- Footer -->
    <?php include(ROOT_PATH . '/includes/public/footer.php'); ?>
    <!-- // Footer -->

</body>

</html>

<?php
ob_end_flush(); // vide le buffer et envoie tout au navigateur proprement
?>
