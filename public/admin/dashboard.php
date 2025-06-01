<?php include('../config.php'); ?>
<?php include(ROOT_PATH . '/includes/admin_functions.php'); ?>
<?php include(ROOT_PATH . '/includes/admin/head_section.php'); ?>

<?php 
// DCKR - Démarrage sécurisé de la session si non déjà lancé
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<title>Admin | Dashboard</title>
</head>

<body>
	<div class="header">
		<div class="logo">
			<a href="<?php echo BASE_URL . 'admin/dashboard.php'; ?>">
				<h1>MyWebSite - Admin</h1>
			</a>
		</div>

		<?php if (isset($_SESSION['user'])) : ?>
			<div class="user-info">
				<span><?php echo htmlspecialchars($_SESSION['user']['username']); ?></span> &nbsp; &nbsp;
				<a href="<?php echo BASE_URL . 'logout.php'; ?>" class="logout-btn">logout</a>
			</div>
		<?php endif; ?>
	</div>

	<div class="container dashboard">
		<h1>Welcome</h1>
		<div class="stats">
			<a href="<?php echo BASE_URL . 'admin/users.php'; ?>" class="first">
				<span>43</span> <br>
				<span>Newly registered users</span>
			</a>
			<a href="<?php echo BASE_URL . 'admin/posts.php'; ?>">
				<span>43</span> <br>
				<span>Published posts</span>
			</a>
			<a href="#">
				<span>43</span> <br>
				<span>Published comments</span>
			</a>
		</div>

		<br><br><br>

		<div class="buttons">
			<a href="<?php echo BASE_URL . 'admin/users.php'; ?>">Add Users</a>
			<a href="<?php echo BASE_URL . 'admin/posts.php'; ?>">Add Posts</a>
		</div>
	</div>
</body>

</html>
