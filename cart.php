<?php include_once 'header.php';
$products = $_SESSION['cart'];

?>

    <div id="content" style="margin: 5% 35% 5% 35%">
        <div class="products-list">
           <?php foreach ($products as $product): ?>
            <table style="width: 70%;">
                <tr>
                    <td><img style="width: 100px; height: 100px;" src="<?php echo $product->image ?>"></td>
                    <td><h3><?php echo $product->title ?></h3>
                        <p><?php echo $product->description ?></p>
                        <p><?php echo $product->price . '$' ?></p>
                    </td>
                    <td>
                        <a type="submit" name="add" href="<?php echo url('common.php', ['remove-cart' => $product   ->id]) ?>">Remove</a>
                    </td>

                </tr>
            </table>
            <?php endforeach; ?>

        </div>
        <div style="margin: 5px">
            <a type="submit" name="add" href="<?php echo url('index.php') ?>">Go to index</a>
            <a type="submit" name="log-out" href="<?php echo url('common.php?remove-all-cart') ?>">Remove all</a>
        </div>
        <form method="post" action="<?php echo url('common.php?send-email') ?>" enctype="multipart/form-data">
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

