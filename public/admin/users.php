<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once(__DIR__ . '/../config.php');
include(ROOT_PATH . '/includes/admin_functions.php');

// Suppression d’un utilisateur (admin ou non)
if (isset($_GET['delete-admin'])) {
    $admin_id = intval($_GET['delete-admin']);
    $admin_role = isset($_GET['delete-role']) ? $_GET['delete-role'] : "";

    if (deleteUserById($admin_id)) {
        $_SESSION['message'] = ($admin_role === "Admin") ?
            "Admin supprimé avec succès." :
            "Utilisateur supprimé avec succès.";
        $_SESSION['type'] = "success";
    } else {
        $_SESSION['message'] = ($admin_role === "Admin") ?
            "Erreur lors de la suppression de l’admin." :
            "Erreur lors de la suppression de l’utilisateur.";
        $_SESSION['type'] = "error";
    }

    header('Location: users.php');
    exit;
}

// Initialisation
$isEditingUser = false;
$username = "";
$email = "";
$admin_id = 0;
$errors = [];
$role_id = "";

// Récupération des données
$roles = getAdminRoles();
$admins = getAdminUsers();
$others = getOthers();

// Création d’un utilisateur
if (isset($_POST['create_admin'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $passwordConfirmation = $_POST['passwordConfirmation'];
    $role_input = trim($_POST['role_id']);

    // Validation
    if (empty($username)) $errors[] = "Username requis";
    if (empty($email)) $errors[] = "Email requis";
    if (empty($password)) $errors[] = "Mot de passe requis";
    if ($password !== $passwordConfirmation) $errors[] = "Les mots de passe ne correspondent pas";
    if (empty($role_input)) $errors[] = "Rôle requis";

    if (count($errors) === 0) {
        $conn = getDBConnection();
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Recherche rôle
        if (is_numeric($role_input)) {
            $role_id = (int)$role_input;
        } else {
            $stmt = $conn->prepare("SELECT id FROM roles WHERE name = ?");
            $stmt->bind_param("s", $role_input);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $role_id = $row['id'];
            } else {
                $errors[] = "Rôle introuvable";
            }
            $stmt->close();
        }

        if (count($errors) === 0) {
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $passwordHash);
            $stmt->execute();
            $user_id = $stmt->insert_id;
            $stmt->close();

            $stmt = $conn->prepare("INSERT INTO role_user (user_id, role_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $user_id, $role_id);
            $stmt->execute();
            $stmt->close();

            $conn->close();

            $_SESSION['message'] = "Nouvel utilisateur créé avec succès";
            $_SESSION['type'] = "success";
            header("Location: users.php");
            exit;
        } else {
            $conn->close();
        }
    }
}
?>

<?php include(ROOT_PATH . '/includes/admin/head_section.php'); ?>
<title>Admin | Gérer les utilisateurs</title>
</head>
<body>
<?php include(ROOT_PATH . '/includes/admin/header.php'); ?>
<div class="container content">
    <?php include(ROOT_PATH . '/includes/admin/menu.php'); ?>

    <div class="action">
        <h1 class="page-title">Créer / Modifier un utilisateur</h1>
        <form method="post" action="users.php">
            <?php include(ROOT_PATH . '/includes/public/errors.php'); ?>

            <?php if ($isEditingUser) : ?>
                <input type="hidden" name="admin_id" value="<?php echo htmlspecialchars($admin_id); ?>">
            <?php endif ?>

            <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" placeholder="Nom d’utilisateur">
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="Email">
            <input type="password" name="password" placeholder="Mot de passe">
            <input type="password" name="passwordConfirmation" placeholder="Confirmation du mot de passe">
            <input type="text" name="role_id" value="<?php echo htmlspecialchars($role_id); ?>" placeholder="Rôle (Admin, Author...)">

            <?php if ($isEditingUser) : ?>
                <button type="submit" class="btn" name="update_admin">Modifier</button>
            <?php else : ?>
                <button type="submit" class="btn" name="create_admin">Créer</button>
            <?php endif ?>
        </form>
    </div>

    <div class="table-div">
        <?php include(ROOT_PATH . '/includes/public/messages.php'); ?>
        <h2>Administrateurs</h2>
        <?php if (empty($admins)) : ?>
            <h3>Aucun administrateur trouvé.</h3>
        <?php else : ?>
            <table class="table">
                <thead>
                    <th>N°</th><th>Admin</th><th>Rôle</th><th colspan="2">Action</th>
                </thead>
                <tbody>
                    <?php foreach ($admins as $key => $admin) : ?>
                        <tr>
                            <td><?php echo $key + 1; ?></td>
                            <td><?php echo $admin['username']; ?>, <?php echo $admin['email']; ?></td>
                            <td><?php echo $admin['role']; ?></td>
                            <td><a class="fa fa-trash btn delete" href="users.php?delete-admin=<?php echo $admin['id']; ?>&delete-role=<?php echo $admin['role']; ?>" onclick="return confirm('Confirmer la suppression ?');"></a></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        <?php endif ?>
    </div>

    <div class="table-div">
        <h2>Utilisateurs</h2>
        <?php include(ROOT_PATH . '/includes/public/messages.php'); ?>
        <?php if (empty($others)) : ?>
            <h3>Aucun utilisateur trouvé.</h3>
        <?php else : ?>
            <table class="table">
                <thead>
                    <th>N°</th><th>Utilisateur</th><th>Rôle</th><th colspan="2">Action</th>
                </thead>
                <tbody>
                    <?php foreach ($others as $key => $user) : ?>
                        <tr>
                            <td><?php echo $key + 1; ?></td>
                            <td><?php echo $user['username']; ?>, <?php echo $user['email']; ?></td>
                            <td><?php echo $user['role']; ?></td>
                            <td><a class="fa fa-pencil btn edit" href="users_updater.php?edit-admin=<?php echo $user['id']; ?>"></a></td>
                            <td><a class="fa fa-trash btn delete" href="users.php?delete-admin=<?php echo $user['id']; ?>&delete-role=<?php echo $user['role']; ?>" onclick="return confirm('Confirmer la suppression ?');"></a></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        <?php endif ?>
    </div>
</div>
</body>
</html>
