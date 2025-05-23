<?php if (isset($_SESSION['user']['username'])) { ?>
	<div class="logged_in_info">
		<span>welcome <?php echo $_SESSION['user']['username'] ?></span>
		|
		<span><a href="logout.php">logout</a></span>
	</div>
<?php } else { ?>
	<div class="banner">
		<div class="welcome_msg">
			<h1>Today's Inspiration</h1>
			<p>
			Je ne suis rien, je le sais, <br>
			mais je compose mon rien <br>
			avec un petit morceau de tout<br>
			<span>Victor Hugo</span>
			</p>
			<a href="register.php" class="btn">register now!</a>
		</div>

		<div class="login_div">
			<form action="<?php echo BASE_URL . 'index.php'; ?>" method="post">
				<h2>Login</h2>
				<div style="width: 60%; margin: 0px auto;">
					<?php include(ROOT_PATH . '/includes/public/errors.php') ?>
				</div>
				<input type="text" name="username" value="" placeholder="Username">
				<input type="password" name="password" placeholder="Password">
				<button class="btn" type="submit" name="login_btn">Sign in</button>
			</form>
		</div>
	</div>
<?php } ?>