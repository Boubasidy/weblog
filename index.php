<?php
include('config.php');
include(ROOT_PATH . '/includes/admin_functions.php');
include(ROOT_PATH . '/includes/all_functions.php');
include(ROOT_PATH . '/admin/post_functions.php');
session_start();
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

        <!-- content -->
        <div class="content">
            <h2 class="content-title">Recent Articles</h2>
            <hr>

            <?php
            // Logique d’affichage principale
            $post_to_show = null;

            if (empty($_SESSION["role"])) {
                // Visiteur non connecté => affiche tous les posts
                $posts = getAllPosts();
                displayPostsTable($posts);
            } else {
                if ($_SESSION["role"] === "Subscriber") {
                    // Abonné connecté

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
                } else {
                    // Autres types d'utilisateur (ex: author) : afficher tous les posts
                    $posts = getAllPosts();
                    displayPostsTable($posts);

                    if (isset($_GET['post_id'])) {
                        $post_id = intval($_GET['post_id']);
                        $post_to_show = getPostById($post_id);
                    }
                }
            }

            // Afficher le contenu du post sélectionné en dessous du tableau, s’il y en a un
            if ($post_to_show) {
                displayPostContent($post_to_show);
            }
            ?>

        </div>
        <!-- // content -->

    </div>
    <!-- // container -->

    <!-- Footer -->
    <?php include(ROOT_PATH . '/includes/public/footer.php'); ?>
    <!-- // Footer -->

</body>

</html>