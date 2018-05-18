<?php include 'header.php' ?>
    <div style="margin: 5% 35% 5% 35%">
        <div><?php
            foreach (getProducts() as $product): ?>
                <table style="width: 70%; border: solid 1px">
                    <tr>
                        <td><img style="width: 100px; height: 100px;" src="<?php echo $product->image ?>"></td>
                        <td><h3><?= $product->title ?></h3>
                            <p><?= $product->description ?></p>
                            <p><?= $product->price . '$' ?></p>
                        </td>
                        <td>
                            <a href="<?= url('common.php', ['edit-product' => $product->id]) ?>">Edit</a>
                            <a href="<?= url('common.php', ['delete-product' => $product->id]) ?>">Delete</a>
                        </td>
                    </tr>
                </table>
            <?php endforeach; ?>
            <div style="margin: 5px">
                <a name="add" href="product.php">Add</a>
                <a name="log-out" href="common.php?logout">LogOut</a>
            </div>
        </div>
    </div>
<?php include 'footer.php' ?>