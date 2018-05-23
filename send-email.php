<?php

if ($_SESSION['cart']) {
    $placeholders = implode(',', array_fill(0, count($_SESSION['cart']), '?'));
    $query = "SELECT * FROM  products WHERE id IN ($placeholders)";
    $cartList = conn($query, $_SESSION['cart'], true);
}

$to = MANAGER_EMAIL;
$subject = "Shop";

$headers[] = "MIME-Version: 1.0";
$headers[] = "Content-type:text/html;charset=UTF-8";
$headers[] = 'From: <' . filter_var($_POST['email']) . '>';

?>
<html>
<body>
<title><?= __('Shop') ?></title>
<div>
    <h4><?= __('Email') ?>: <?= filter_var($_POST['email']); ?></h4>
</div>
<div>
    <h4><?= __('Comment') ?>: <?= filter_var($_POST['comments']); ?></h4>
</div>
<div>
    <?php foreach ($cartList as $product) : ?>
        <table style="width: 70%; border: solid 1px;">
            <tr>
                <td><img style="width: 100px; height: 100px;" src="<?= BASE_URL . '/' . $product->image ?>"></td>
                <td><?= strip_tags($product->title) ?></td>
                <td><?= strip_tags($product->description) ?></td>
                <td><?= strip_tags($product->price) ?>$</td>
            </tr>
        </table>
    <?php endforeach; ?>
</div>
</body>
</html>



