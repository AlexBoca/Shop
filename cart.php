<?php include_once 'header.php';
$items = $_SESSION['cart'];

?>

    <div id="content" style="margin: 5% 35% 5% 35%">
        <div class="products-list">
			<?php foreach ($items as $item) :
				?>
                <div style="margin: 5px">
                    <img style="width: 150px; height: 150px;" src="<?php echo $item->image ?>">
                    <h1><?php echo $item->title ?></h1>
                    <p><?php echo $item->description ?></p>
                    <span><?php echo $item->price ?></span>
                    <a type="submit" name="add" href="<?php echo url('common.php', ['remove-cart' => $item->id]) ?>">Remove</a>
                </div>
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
                <textarea name="contact-details" placeholder="Contact details"></textarea>
            </div>
            <div style="margin: 5px">
                <textarea name="comments" placeholder="Comments"></textarea>
            </div>

            <button type="submit">Checkout</button>
        </form>


    </div>

<?php include 'footer.php';

