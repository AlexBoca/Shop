<?php
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_SESSION['cart']) && isset($_POST['email'])) {
        $to = $subject = $headers = '';

        ob_start();
        include 'send-email.php';
        $message = ob_get_contents();
        ob_end_clean();
        $mail = mail($to, $subject, $message, implode("\r\n", $headers));
        redirect(url('cart.php', ['mail' => $mail]));
    }
    redirect(url('index.php'));
}

if (isset($_GET["remove-all"])) {
    $_SESSION['cart'] = [];
    redirect(url('index.php'));
}

if (isset($_GET["id"])) {

    if (empty($_GET['id']) || empty($_SESSION['cart'])) {
        redirect($_SERVER['HTTP_REFERER']);
    }

    $index = array_search($_GET['id'], $_SESSION["cart"]);
    if ($index !== false) {
        unset($_SESSION["cart"][$index]);
        $_SESSION["cart"] = array_values($_SESSION["cart"]);
    }
}

function getCartItems()
{
    $items = [];
    if ($_SESSION['cart']) {
        $placeholders = implode(',', array_fill(0, count($_SESSION['cart']), '?'));
        $query = "SELECT * FROM  products WHERE id IN ($placeholders)";
        $items = conn($query, $_SESSION['cart'], true);
    }
    return $items;
}

?>
    <div style="margin: 5% 35% 5% 35%;">
        <div>
            <?php if (isset($_GET['mail'])) { ?>
                <h4><?= __('Thanks for contacting us.'); ?></h4>
            <?php } ?>
            <?php foreach (getCartItems() as $product): ?>
                <table style="width: 70%; border: solid 1px;">
                    <tr>
                        <td><img style="width: 100px; height: 100px;" src="<?= $product->image ?>"></td>
                        <td>
                            <h3><?= strip_tags($product->title) ?></h3>
                            <p><?= strip_tags($product->description) ?></p>
                            <p><?= strip_tags($product->price) ?>$</p>
                        </td>
                        <td>
                            <a href="<?= url('cart.php', ['id' => $product->id]) ?>"><?= __('Remove') ?></a>
                        </td>
                    </tr>
                </table>
            <?php endforeach; ?>
        </div>
        <div style="margin: 5px;">
            <a name="to-index" href="<?= url('index.php') ?>"><?= __('Go to index') ?></a>
            <a name="remove-all" href="<?= url('cart.php?remove-all') ?>"><?= __('Remove all') ?></a>
        </div>
        <form method="post" action="<?= url('cart.php') ?>" enctype="multipart/form-data">
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

