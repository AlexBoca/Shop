<?php include 'header.php' ?>
<div class="container" style="margin: 5% 35% 5% 35%">
	<?php if (!isset($_GET['id'])) :
		$action = 'common.php?create-product';
		$title = $description = $price = $image = '';
	else:
		$title = $_GET['title'];
		$description = $_GET['description'];
		$price = $_GET['price'];
		$image = $_GET['image'];
		$action = url('common.php', ['update-product' => $_GET['id']]);
	endif;
	?>
    <form method="post" action="<?php echo $action ?>" enctype="multipart/form-data">
        <div style="margin: 5px">
            <input type="text" name="title" placeholder="Title" value="<?php echo $title ?>">
        </div>
        <div style="margin: 5px">
            <textarea name="description" placeholder="Description"><?php echo $description ?></textarea>
        </div>
        <div style="margin: 5px">
            <input type="text" name="price" placeholder="Price" value="<?php echo $price ?>">
        </div>
		<?php if ($image): ?>
            <div>
                <img style="width: 80px; height: 80px;" src="<?php echo $image ?>">
            </div>
		<?php endif; ?>
        <div style="margin: 5px">

            <input type="hidden" name="size" value="1000000">
            <input type="file" name="image" id="image" value="<?php echo $image ?>">
        </div>
        <button type="submit">Save</button>
    </form>
    <a href="<?php echo url('products.php') ?>">Products</a>
</div>
<?php include 'footer.php' ?>
<script>

</script>

