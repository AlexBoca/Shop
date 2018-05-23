<?php
include 'header.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_GET["id"])) {

    if (empty($_GET["id"])) {
        redirect($_SERVER['HTTP_REFERER']);
        return;
    }
    if (in_array($_GET["id"], $_SESSION['cart'])) {
        redirect(url('index.php'));
    }
    $_SESSION['cart'][] = $_GET["id"];
}

function indexProducts()
{
    if ($_SESSION['cart']) {
        $placeholders = implode(',', array_fill(0, count($_SESSION['cart']), '?'));
        $query = "SELECT * FROM products WHERE id NOT IN ($placeholders)";
        $items = conn($query, $_SESSION['cart'], true);
    } else {
        $query = "SELECT * FROM products";
        $items = conn($query, [], true);

    }
    return $items;
}

?>
<div style="margin: 5% 35% 5% 35%;">
    <?php foreach (indexProducts() as $product): ?>
        <table style="width: 70%; border: solid 1px;">
            <tr>
                <td><img style="width: 100px; height: 100px;" src="<?= $product->image ?>"></td>
                <td>
                    <h3><?= strip_tags($product->title) ?></h3>
                    <p><?= strip_tags($product->description) ?></p>
                    <p><?= strip_tags($product->price) ?>$</p>
                </td>
                <td>
                    <a href="<?= url('index.php', ['id' => $product->id]) ?>"><?= __('Add') ?></a>
                </td>
            </tr>
        </table>
    <?php endforeach; ?>
    <div style="margin: 5px;">
        <a href="cart.php"><?= __('Go to cart') ?></a>
    </div>
</div>

<?php include 'footer.php' ?>