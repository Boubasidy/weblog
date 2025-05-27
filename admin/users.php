<?php
session_start();
include('../config.php');
include __DIR__ . '/../includes/admin_functions.php';
include __DIR__ . '/../includes/public/functions.php';


// Suppression d’un utilisateur admin
if (isset($_GET['delete-admin'])) {
    $admin_id = $_GET['delete-admin'];
	$admin_role = $_GET['delete-role'];
    if (deleteUserById($admin_id)) {
		if ($admin_role === "Admin")
		{
        	$_SESSION['message'] = "Admin supprimé avec succès.";
        	$_SESSION['type'] = "success";
		}
		else {
			$_SESSION['message'] = "Utilisateur supprimé avec succès.";
        	$_SESSION['type'] = "success";
		}
    } else {
		if ($admin_role === "Admin")
		{
			$_SESSION['message'] = "Erreur lors de la suppression de l’admin.";
			$_SESSION['type'] = "error";
		}
		else {
			$_SESSION['message'] = "Erreur lors de la suppression de l’utilisateur.";
        	$_SESSION['type'] = "error";
		}
    }
    header('Location: users.php');
    exit;
}


// Initialiser variables
$isEditingUser = false;
$username = "";
$email = "";
$admin_id = 0;
$errors = [];
$role_id = "";

// Récupérer tous les rôles (inutile ici, mais conservé pour compatibilité)
$roles = getAdminRoles();

// Récupérer tous les admins
$admins = getAdminUsers();
$others = getOthers();

// Créer un admin
// Créer un admin
if (isset($_POST['create_admin'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $passwordConfirmation = $_POST['passwordConfirmation'];
    $role_input = trim($_POST['role_id']); // Peut être ID ou nom

    // Validation
    if (empty($username)) $errors[] = "Username requis";
    if (empty($email)) $errors[] = "Email requis";
    if (empty($password)) $errors[] = "Mot de passe requis";
    if ($password !== $passwordConfirmation) $errors[] = "Les mots de passe ne correspondent pas";
    if (empty($role_input)) $errors[] = "Rôle requis";

    if (count($errors) === 0) {
        $conn = getDBConnection();
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Si l'utilisateur entre un nom de rôle (ex: "Admin"), récupérer l'id
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
            // Insérer l'utilisateur
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $passwordHash);
            $stmt->execute();
            $user_id = $stmt->insert_id;
            $stmt->close();

            // Associer le rôle à l'utilisateur
            $stmt = $conn->prepare("INSERT INTO role_user (user_id, role_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $user_id, $role_id);
            $stmt->execute();
            $stmt->close();

            $conn->close();

            $_SESSION['message'] = "Nouvel utilisateur créé avec succès";
            header("Location: users.php");
            exit;
        } else {
            $conn->close();
        }
    }
}
?>

<?php include(ROOT_PATH . '/includes/admin/head_section.php'); ?>
<title>Admin | Manage users</title>
</head>

<body>
<?php include(ROOT_PATH . '/includes/admin/header.php') ?>
<div class="container content">
    <?php include(ROOT_PATH . '/includes/admin/menu.php') ?>

    <div class="action">
        <h1 class="page-title">Create/Edit Admin User</h1>

        <form method="post" action="users.php">
            <?php include(ROOT_PATH . '/includes/public/errors.php') ?>

            <?php if ($isEditingUser === true) : ?>
                <input type="hidden" name="admin_id" value="<?php echo $admin_id; ?>">
            <?php endif ?>

            <input type="text" name="username" value="<?php echo $username; ?>" placeholder="Username">
            <input type="email" name="email" value="<?php echo $email ?>" placeholder="Email">
            <input type="password" name="password" placeholder="Password">
            <input type="password" name="passwordConfirmation" placeholder="Password confirmation">

            <!-- Champ de rôle en texte libre -->
            <input type="text" name="role_id" value="<?php echo htmlspecialchars($role_id); ?>" placeholder="Enter role (e.g., Admin or Author)">

            <?php if ($isEditingUser === true) : ?>
                <button type="submit" class="btn" name="update_admin">UPDATE</button>
            <?php else : ?>
                <button type="submit" class="btn" name="create_admin">Save User</button>
            <?php endif ?>
        </form>
    </div>

    <div class="table-div">
        <?php include(ROOT_PATH . '/includes/public/messages.php') ?>

        <?php if (empty($admins)) : ?>
            <h1>No admins in the database.</h1>
        <?php else : ?>
            <table class="table">
                <thead>
                <th>N</th>
                <th>Admin</th>
                <th>Role</th>
                <th colspan="2">Action</th>
                </thead>
                <tbody>
                <?php foreach ($admins as $key => $admin) : ?>
                    <tr>
                        <td><?php echo $key + 1; ?></td>
                        <td><?php echo $admin['username']; ?>, &nbsp;<?php echo $admin['email']; ?></td>
                        <td><?php echo $admin['role']; ?></td>
                        <td><a class="fa fa-trash btn delete" href="users.php?delete-admin=<?php echo $admin['id']; ?>&delete-role=<?php echo $admin['role']; ?>" onclick="return confirm('Are you sure you want to delete this user?');"></a></td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        <?php endif ?>
    </div>

    <div class="table-div">
        <?php include(ROOT_PATH . '/includes/public/messages.php') ?>

        <?php if (empty($others)) : ?>
            <h1>No users in the database.</h1>
        <?php else : ?>
            <table class="table">
                <thead>
                <th>N</th>
                <th>User</th>
                <th>Role</th>
                <th colspan="2">Action</th>
                </thead>
                <tbody>
                <?php foreach ($others as $key => $admin) : ?>
                    <tr>
                        <td><?php echo $key + 1; ?></td>
                        <td><?php echo $admin['username']; ?>, &nbsp;<?php echo $admin['email']; ?></td>
                        <td><?php echo $admin['role']; ?></td>
                        <td><a class="fa fa-pencil btn edit" href="users_updater.php?edit-admin=<?php echo $admin['id'] ?>"></a></td>
                        <td><a class="fa fa-trash btn delete" href="users.php?delete-admin=<?php echo $admin['id'] ?>&<?php echo $admin['role'] ?>"></a></td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        <?php endif ?>
    </div>
</div>
</body>
</html>
