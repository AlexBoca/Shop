<?php include 'common.php' ?>
<!DOCTYPE html>
<html lang="en">
<body>
<header>
    <div class="menu">
        <a HREF="<?php echo url('index.php') ?>">Home</a>
        <a HREF="<?php echo url('cart.php') ?>">Cart</a>
        <a HREF="<?php echo url('login.php') ?>">Login</a>
		<?php if (isset($_SESSION['user'])): ?>

            <a HREF="<?php echo url('product.php') ?>">Create</a>
            <a HREF="<?php echo url('products.php') ?>">Products</a>
		<?php endif; ?>
    </div>
</header>
