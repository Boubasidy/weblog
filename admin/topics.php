<?php
include('../config.php'); 
include(ROOT_PATH . '/includes/admin_functions.php');
include(ROOT_PATH . '/includes/all_functions.php');
include(ROOT_PATH . '/admin/post_functions.php');

// Variables initiales
$errors = [];
$name = '';
$slug = '';

// Traitement formulaire ajout topic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']);

    if (empty($name)) {
        $errors[] = "Le nom du topic est obligatoire.";
    }
    if (empty($slug)) {
        $errors[] = "Le slug du topic est obligatoire.";
    } else {
        $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $slug));
    }

    if (empty($errors)) {
        insertTopic($name , $slug);
    }
}

// Récupérer tous les topics pour affichage
$topics = getAllTopics();

?>

<?php include(ROOT_PATH . '/includes/admin/head_section.php'); ?>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f9f9f9;
        margin: 0; padding: 20px;
    }
    .container {
        max-width: 900px;
        margin: 40px auto;
        display: flex;
        gap: 40px;
        flex-wrap: wrap;
    }
    .form-container, .table-container {
        background: #fff;
        padding: 30px 40px;
        border-radius: 8px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        flex: 1 1 400px; /* min width 400px, grow */
        box-sizing: border-box;
    }
    h2 {
        margin-top: 0;
        color: #333;
        text-align: center;
        margin-bottom: 25px;
    }
    label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #555;
    }
    input[type="text"] {
        width: 100%;
        padding: 10px 12px;
        margin-bottom: 20px;
        border: 1.8px solid #ccc;
        border-radius: 5px;
        font-size: 1rem;
        transition: border-color 0.3s ease;
    }
    input[type="text"]:focus {
        border-color: #4a90e2;
        outline: none;
    }
    small {
        color: #999;
        font-style: italic;
        margin-top: -15px;
        margin-bottom: 20px;
        display: block;
    }
    button {
        width: 100%;
        padding: 12px;
        background:rgb(112, 226, 74);
        border: none;
        border-radius: 5px;
        color: white;
        font-size: 1.1rem;
        font-weight: 700;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    button:hover {
        background: #357ABD;
    }
    .error {
        background-color: #ffdddd;
        border-left: 5px solid #f44336;
        margin-bottom: 20px;
        padding: 12px 15px;
        color: #a94442;
        border-radius: 4px;
    }
    .error ul {
        margin: 0;
        padding-left: 20px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 1rem;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 10px 15px;
        text-align: left;
    }
    th {
        background-color: #4a90e2;
        color: white;
    }
    tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }
</style>
</head>
<body>

<?php include(ROOT_PATH . '/includes/admin/header.php'); ?>

<div class="container content">	
<?php include(ROOT_PATH . '/includes/admin/menu.php') ?>
    <div class="form-container">
        <h2>Ajouter un nouveau topic</h2>

        <?php if (!empty($errors)): ?>
            <div class="error">
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="topics.php" novalidate>
            <label for="name">Nom du topic :</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>

            <label for="slug">Slug :</label>
            <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($slug) ?>" required>
            <small>Le slug est un identifiant simple sans espaces (ex: motivation, inspiration)</small>

            <button type="submit">Ajouter le topic</button>
        </form>
    </div>

    <div class="table-container">
        <h2>Topics existants</h2>
        <?php if (count($topics) === 0): ?>
            <p>Aucun topic trouvé.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Slug</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topics as $topic): ?>
                        <tr>
                            <td><?= htmlspecialchars($topic['id']) ?></td>
                            <td><?= htmlspecialchars($topic['name']) ?></td>
                            <td><?= htmlspecialchars($topic['slug']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</div>

</body>
</html>
