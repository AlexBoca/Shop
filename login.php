<?php
include 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["username"];
    $password = $_POST["password"];
    if (ADMIN_USERNAME == $name || ADMIN_PASSWORD == $password) {
        $_SESSION['user'] = true;
        redirect(url('products.php'));
    }
    redirect($_SERVER['HTTP_REFERER']);
}

if (isset($_GET['logout'])) {
    if (isset($_SESSION['user'])) {
        unset($_SESSION['user']);
    }
    redirect(url('index.php'));
}
?>
<div id="content" style="margin: 5% 35% 5% 35%;">
    <?php if (isset($_SESSION['user'])) :echo 'you are logged in!'; ?>
        <a href="<?= url('login.php?logout') ?>"><?= __('Logout') ?></a>
    <?php else: ?>
        <form method="post" action="<?= url('login.php') ?>">
            <div style="margin: 5px;">
                <label><?= __('Username') ?></label>
                <input type="text" name="username" value="">
            </div>
            <div style="margin: 5px;">
                <label><?= __('Password') ?></label>
                <input type="password" name="password" value="">
            </div>
            <button type="submit"><?= __('Login') ?></button>
        </form>
    <?php endif; ?>
</div>
<?php include 'footer.php';


