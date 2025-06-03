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
	<?php include(ROOT_PATH . '/includes/admin/header.php'); ?>


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
