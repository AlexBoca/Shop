<?php require_once 'common.php' ?>
<!DOCTYPE html>
<html lang="en">
<body>
<header>
    <div>
        <a href="<?= url('index.php') ?>"><?= __('Home') ?></a>
        <a href="<?= url('cart.php') ?>"><?= __('Cart') ?></a>
        <a href="<?= url('login.php') ?>"><?= __('Login') ?></a>
        <?php if (isset($_SESSION['user'])): ?>
            <a href="<?= url('product.php') ?>"><?= __('Create') ?></a>
            <a href="<?= url('products.php') ?>"><?= __('Products') ?></a>
        <?php endif; ?>
    </div>
</header>
