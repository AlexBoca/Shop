<?php include 'header.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_GET["add-cart"])) {
    $product_id = $_GET['add-cart'];
    if (empty($product_id)) {
        redirect($_SERVER['HTTP_REFERER']);
        return;
    }
    foreach ($_SESSION['cart'] as $cart) {
        if ($product_id == $cart->id) {
            redirect(url('index.php'));
            return;
        }
    }
    array_push($_SESSION['cart'], $product_id);
    redirect(url('index.php'));
}

function indexProducts()
{
    if ($_SESSION['cart']) {
        $values = [];
        for ($i = 0; $i < count($_SESSION['cart']); $i++) {
            $values[] .= "?";
        }
        $values = implode(',', $values);

        $query = "SELECT * FROM products WHERE id NOT IN ($values)";
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
                <td><h3><?= strip_tags($product->title) ?></h3>
                    <p><?= strip_tags($product->description) ?></p>
                    <p><?= strip_tags($product->price) ?>$</p>
                </td>
                <td>
                    <a href="<?= url('index.php', ['add-cart' => $product->id]) ?>"><?= __('Add') ?></a>
                </td>
            </tr>
        </table>
    <?php endforeach; ?>
    <div style="margin: 5px;">
        <a href="cart.php"><?= __('Go to cart') ?></a>
    </div>
</div>

<?php include 'footer.php' ?>