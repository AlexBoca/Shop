<?php include_once 'header.php';

if (isset($_GET['send-email'])) {
    if (!empty($_SESSION['cart']) && isset($_POST['email'])) {
        $to = $subject = $headers = '';

        ob_start();
        include 'send-email.php';
        $message = ob_get_contents();
        ob_end_clean();

        $mail = mail($to, $subject, $message, implode("\r\n", $headers));
        redirect(url('cart.php', ['mail' => $mail]));

    }
    redirect($_SERVER['HTTP_REFERER']);
}

if (isset($_GET["remove-all-cart"])) {
    $_SESSION['cart'] = [];
    redirect(url('index.php'));
}

if (isset($_GET["remove-cart"])) {
    if (empty($_GET['remove-cart']) || empty($_SESSION['cart'])) {
        redirect($_SERVER['HTTP_REFERER']);
    }
    $productId = $_GET['remove-cart'];
    $index = array_search($productId, $_SESSION["cart"]);
    if ($index !== false) {
        unset($_SESSION["cart"][$index]);
        $_SESSION["cart"] = array_values($_SESSION["cart"]);
    }
    redirect(url('cart.php'));
}

function getCartItems()
{
    $items = [];
    $cart = $_SESSION['cart'];
    if ($cart) {
        $placeholders = implode(',', array_fill(0, count($cart), '?'));
        $query = "SELECT * FROM  products WHERE id IN ($placeholders)";
        $items = conn($query, $cart, true);
    }
    return $items;
}

?>
<div style="margin: 5% 35% 5% 35%;">
    <div>
        <h4>
            <?php echo(isset($_GET['mail']) ? __('Thanks for contacting us.') : ''); ?>
        </h4>
        <?php foreach (getCartItems() as $product): ?>
            <table style="width: 70%; border: solid 1px;">
                <tr>
                    <td><img style="width: 100px; height: 100px;" src="<?= $product->image ?>"></td>
                    <td><h3><?= strip_tags($product->title) ?></h3>
                        <p><?= strip_tags($product->description) ?></p>
                        <p><?= strip_tags($product->price) ?>$</p>
                    </td>
                    <td>
                        <a href="<?= url('cart.php', ['remove-cart' => $product->id]) ?>"><?= __('Remove') ?></a>
                    </td>
                </tr>
            </table>
        <?php endforeach; ?>
    </div>
    <div style="margin: 5px;">
        <a name="add" href="<?= url('index.php') ?>"><?= __('Go to index') ?></a>
        <a name="log-out" href="<?= url('cart.php?remove-all-cart') ?>"><?= __('Remove all') ?></a>
    </div>
    <form method="post" action="<?= url('cart.php?send-email') ?>" enctype="multipart/form-data">
        <div style="margin: 5px;">
            <input type="text" name="email" placeholder="<?= __('Email') ?>" value="">
        </div>
        <div style="margin: 5px;">
            <textarea name="comments" placeholder="<?= __('Comments') ?>"></textarea>
        </div>
        <button type="submit"><?= __('Checkout') ?></button>
    </form>
</div>
<?php include 'footer.php';

