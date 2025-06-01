<?php if (isset($_SESSION['user']['username'])): ?>
    <div class="logged_in_info">
        <span style="
		display: inline-block;
		padding: 10px 20px;
		background-color: #f0f8ff;
		border-left: 5px solid #009879;
		border-radius: 8px;
		font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
		color: #333;
		font-size: 16px;
		box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);">
		👋 Welcome, <strong style="color: #009879;"><?= htmlspecialchars($_SESSION['user']['username']) ?></strong>
		</span>

        |
        <span><a href="logout.php">Logout</a></span>
    </div>
<?php else: ?>
    
    <div class="banner">
        <div class="welcome_msg">
            <h1>Today's Inspiration</h1>
            <p>
                Je ne suis rien, je le sais, <br>
                mais je compose mon rien <br>
                avec un petit morceau de tout<br>
                <span>Victor Hugo</span>
            </p>
            <a href="register.php" class="btn">Register now!</a>
        </div>

        <div class="login_div">
            <form action="login.php" method="post">
                <h2>Login</h2>
                <div style="width: 60%; margin: 0px auto;">
                    <?php include(ROOT_PATH . '/includes/public/errors.php'); ?>
                </div>
                <input type="text" name="username" value="" placeholder="Username">
                <input type="password" name="password" placeholder="Password">
                <button class="btn" type="submit" name="login_btn">Sign in</button>
            </form>
        </div>
    </div>
<?php endif; ?>