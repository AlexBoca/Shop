<?php include_once 'header.php';
$products = $_SESSION['cart'];
?>
    <div id="content" style="margin: 5% 35% 5% 35%">
        <div>
            <h4>
                    <?php if (isset($_GET['mail'])) :
                    echo 'Thanks for contacting us.';
                endif; ?>
            </h4>
            <?php foreach ($products as $product): ?>
                <table style="width: 70%; border: solid 1px">
                    <tr>
                        <td><img style="width: 100px; height: 100px;" src="<?= $product->image ?>"></td>
                        <td><h3><?= $product->title ?></h3>
                            <p><?= $product->description ?></p>
                            <p><?= $product->price ?></p>$
                        </td>
                        <td>
                            <a href="<?= url('common.php', ['remove-cart' => $product->id]) ?>">Remove</a>
                        </td>
                    </tr>
                </table>
            <?php endforeach; ?>
        </div>
        <div style="margin: 5px">
            <a name="add" href="<?= url('index.php') ?>">Go to index</a>
            <a name="log-out" href="<?= url('common.php?remove-all-cart') ?>">Remove all</a>
        </div>
        <form method="post" action="<?= url('common.php?send-email') ?>" enctype="multipart/form-data">
            <div style="margin: 5px">
                <input type="text" name="email" placeholder="Email" value="">
            </div>
            <div style="margin: 5px">
                <textarea name="comments" placeholder="Comments"></textarea>
            </div>
            <button type="submit">Checkout</button>
        </form>
    </div>
<?php include 'footer.php';

