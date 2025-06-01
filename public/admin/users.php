<?php
//DCKR : Vérifie que la session n’est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start(); //DCKR : session_start sécurisé
}

include('../config.php');
include(ROOT_PATH . '/includes/admin_functions.php');
//include(ROOT_PATH . '/includes/public/functions.php');

// Suppression d’un utilisateur
if (isset($_GET['delete-admin'])) {
    $admin_id = intval($_GET['delete-admin']); //DCKR : Sécurisation ID
    $admin_role = isset($_GET['delete-role']) ? $_GET['delete-role'] : "";

    if (deleteUserById($admin_id)) {
        $_SESSION['message'] = ($admin_role === "Admin")
            ? "Admin supprimé avec succès."
            : "Utilisateur supprimé avec succès.";
        $_SESSION['type'] = "success";
    } else {
        $_SESSION['message'] = ($admin_role === "Admin")
            ? "Erreur lors de la suppression de l’admin."
            : "Erreur lors de la suppression de l’utilisateur.";
        $_SESSION['type'] = "error";
    }

    header('Location: users.php'); //DCKR : Redirection après suppression
    exit;
}

// Initialisation
$isEditingUser = false;
$username = "";
$email = "";
$admin_id = 0;
$errors = [];
$role_id = "";

// Rôles et utilisateurs
$roles = getAdminRoles(); //DCKR : potentiellement inutile ici
$admins = getAdminUsers();
$others = getOthers();

// Création d’un utilisateur admin
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

        // Recherche du rôle par nom ou id
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
            // Insertion utilisateur
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $passwordHash);
            $stmt->execute();
            $user_id = $stmt->insert_id;
            $stmt->close();

            // Liaison rôle
            $stmt = $conn->prepare("INSERT INTO role_user (user_id, role_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $user_id, $role_id);
            $stmt->execute();
            $stmt->close();

            $conn->close();

            $_SESSION['message'] = "Nouvel utilisateur créé avec succès";
            $_SESSION['type'] = "success"; //DCKR : Ajout du type
            header("Location: users.php"); //DCKR : Redirection propre
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
<?php include(ROOT_PATH . '/includes/admin/header.php') ?>

<div class="container content">
    <?php include(ROOT_PATH . '/includes/admin/menu.php') ?>

    <div class="action">
        <h1 class="page-title">Créer / Modifier un utilisateur</h1>

        <form method="post" action="users.php">
            <?php include(ROOT_PATH . '/includes/public/errors.php') ?>

            <?php if ($isEditingUser === true) : ?>
                <input type="hidden" name="admin_id" value="<?php echo htmlspecialchars($admin_id); ?>">
            <?php endif ?>

            <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" placeholder="Nom d’utilisateur">
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="Email">
            <input type="password" name="password" placeholder="Mot de passe">
            <input type="password" name="passwordConfirmation" placeholder="Confirmation du mot de passe">
            <input type="text" name="role_id" value="<?php echo htmlspecialchars($role_id); ?>" placeholder="Rôle (Admin, Author...)">

            <?php if ($isEditingUser === true) : ?>
                <button type="submit" class="btn" name="update_admin">Modifier</button>
            <?php else : ?>
                <button type="submit" class="btn" name="create_admin">Créer</button>
            <?php endif ?>
        </form>
    </div>

    <div class="table-div">
        <?php include(ROOT_PATH . '/includes/public/messages.php') ?>
        <?php if (empty($admins)) : ?>
            <h1>Aucun administrateur trouvé.</h1>
        <?php else : ?>
            <table class="table">
                <thead>
                    <th>N°</th><th>Admin</th><th>Rôle</th><th colspan="2">Action</th>
                </thead>
                <tbody>
                    <?php foreach ($admins as $key => $admin) : ?>
                        <tr>
                            <td><?php echo $key + 1; ?></td>
                            <td><?php echo htmlspecialchars($admin['username']) . ", " . htmlspecialchars($admin['email']); ?></td>
                            <td><?php echo htmlspecialchars($admin['role']); ?></td>
                            <td>
                                <a class="fa fa-trash btn delete"
                                   href="users.php?delete-admin=<?php echo $admin['id']; ?>&delete-role=<?php echo urlencode($admin['role']); ?>"
                                   onclick="return confirm('Supprimer cet utilisateur ?');">
                                </a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        <?php endif ?>
    </div>

    <div class="table-div">
        <?php include(ROOT_PATH . '/includes/public/messages.php') ?>
        <?php if (empty($others)) : ?>
            <h1>Aucun utilisateur trouvé.</h1>
        <?php else : ?>
            <table class="table">
                <thead>
                    <th>N°</th><th>Utilisateur</th><th>Rôle</th><th colspan="2">Action</th>
                </thead>
                <tbody>
                    <?php foreach ($others as $key => $admin) : ?>
                        <tr>
                            <td><?php echo $key + 1; ?></td>
                            <td><?php echo htmlspecialchars($admin['username']) . ", " . htmlspecialchars($admin['email']); ?></td>
                            <td><?php echo htmlspecialchars($admin['role']); ?></td>
                            <td>
                                <a class="fa fa-pencil btn edit" href="users_updater.php?edit-admin=<?php echo $admin['id']; ?>"></a>
                            </td>
                            <td>
                                <a class="fa fa-trash btn delete"
                                   href="users.php?delete-admin=<?php echo $admin['id']; ?>&delete-role=<?php echo urlencode($admin['role']); ?>"
                                   onclick="return confirm('Supprimer cet utilisateur ?');">
                                </a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        <?php endif ?>
    </div>
</div>
</body>
</html>
