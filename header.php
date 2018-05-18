<?php include 'common.php' ?>
<!DOCTYPE html>
<html lang="en">
<body>
<header>
    <div>
        <a href="<?php echo url('index.php') ?>">Home</a>
        <a href="<?php echo url('cart.php') ?>">Cart</a>
        <a href="<?php echo url('login.php') ?>">Login</a>
		<?php if (isset($_SESSION['user'])): ?>
            <a href="<?php echo url('product.php') ?>">Create</a>
            <a href="<?php echo url('products.php') ?>">Products</a>
		<?php endif; ?>
    </div>
</header>
