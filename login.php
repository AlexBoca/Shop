<?php include 'header.php'; ?>
    <div id="content" style="margin: 5% 35% 5% 35%">
        <?php if (isset($_SESSION['user'])) :echo 'you are logged in!'; ?>
            <a HREF="<?php echo url('common.php?logout') ?>">Logout</a>
        <?php else: ?>
                <form method="post" action="<?= url('common.php?login') ?>">
                <div style="margin: 5px">
                    <label>Username</label>
                    <input type="text" name="username" value="">
                </div>
                <div style="margin: 5px">
                    <label>Password</label>
                    <input type="password" name="password" value="">
                </div>
                <button type="submit">Login</button>
            </form>
        <?php endif; ?>
    </div>
<?php include 'footer.php';


