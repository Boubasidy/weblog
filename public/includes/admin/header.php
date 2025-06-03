<div class="header">
    <div class="logo">
        <a href="<?php echo BASE_URL . 'admin/dashboard.php'; ?>">
            <h1>MyWebSite - Admin</h1>
        </a>
    </div>

    <?php if (isset($_SESSION['user']) && isset($_SESSION['user']['username'])) : ?>
        <div class="user-info">
            <span><?php echo htmlspecialchars($_SESSION['user']['username']); ?></span> &nbsp; &nbsp;
            <a href="<?php echo BASE_URL . 'logout.php'; ?>" style="
                display: inline-block;
                padding: 8px 16px;
                background-color: #dc3545;
                color: #fff;
                text-decoration: none;
                border-radius: 5px;
                font-family: 'Segoe UI Emoji', 'Segoe UI Symbol', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                font-size: 14px;
                transition: background-color 0.3s ease;
            " onmouseover="this.style.backgroundColor='#c82333'" onmouseout="this.style.backgroundColor='#dc3545'">
                🔒 Logout
            </a>
        </div>
    <?php endif; ?>
</div>
