<?php include 'header.php' ?>
    <div class="container" style="margin: 5% 35% 5% 35%">
        <div class="products-list">
			<?php
			foreach (getProducts() as $product): ?>
                <table style="width: 70%;">
                    <tr>
                        <td><img style="width: 100px; height: 100px;" src="<?php echo $product->image ?>"></td>
                        <td><h3><?php echo $product->title ?></h3>
                            <p><?php echo $product->description ?></p>
                            <p><?php echo $product->price . '$' ?></p>
                        </td>
                        <td>
                            <a href="<?php echo url('common.php', ['add-cart' => $product->id]) ?>">Add</a>
                        </td>

                    </tr>
                </table>

			<?php endforeach; ?>
            <div style="margin: 5px">
                <a type="submit"  href="cart.php">Go to cart</a>
            </div>

        </div>
    </div>
<?php include 'footer.php' ?>