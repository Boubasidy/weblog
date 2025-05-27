<?php include('config.php'); ?>
<?php include('includes/public/head_section.php'); 
include(ROOT_PATH . '/includes/admin_functions.php');
include(ROOT_PATH . '/admin/post_functions.php');?>

<title>MyWebSite | Home </title>

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
		
		<!-- // Messages -->

		<!-- content -->
		<div class="content">
    <h2 class="content-title">Recent Articles</h2>
    <hr>

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

        .styled-table th, .styled-table td {
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
    </style>

    <?php
    $posts = getAllPosts();

    if (empty($posts)) {
        echo "<p>Aucun article trouvé.</p>";
    } else {
        echo '<table class="styled-table">';
        echo '<thead>
                <tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Auteur</th>
                    <th>Topic</th>
                    <th>Publié</th>
                    <th>Date</th>
                </tr>
              </thead>';
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
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    }
    ?>
</div>

			



		</div>
		<!-- // content -->


	</div>
	<!-- // container -->


	<!-- Footer -->
	<?php include(ROOT_PATH . '/includes/public/footer.php'); ?>
	<!-- // Footer -->