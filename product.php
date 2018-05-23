<?php
include 'header.php';

if (!isset($_SESSION['user'])) {
    redirect(url('login.php'));
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET["id"])) {
    $query = "SELECT * FROM  products WHERE id=?";
    $product = conn($query, $_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && (!isset($_GET["id"]))) {
    $params = ['title' => $_POST["title"], 'description' => $_POST["description"], 'price' => $_POST["price"]];
    $image = uploadImage();
    if ($image) {
        $params['image'] = $image;
        $paramKeys = implode(',', array_keys($params));
        $placeholders = implode(',', array_fill(0, count($params), '?'));
        $query = "INSERT INTO products ($paramKeys) VALUES ($placeholders)";
        conn($query, array_values($params));
    }
    redirect(url('products.php'));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && (isset($_GET["id"]))) {
    $params = ['title' => $_POST["title"], 'description' => $_POST["description"], 'price' => $_POST["price"]];

    if ($_FILES["image"]["name"]) {
        $query = "SELECT * FROM  products WHERE id=?";
        $product = conn($query, $_GET['id']);
        unlink($product->image);
        $image = uploadImage();
        $params['image'] = $image;
    }
    $format = [];
    foreach ($params as $key => $value) {
        $format[] = $key . '=' . "'$value'";
    }
    $format = implode(', ', $format);
    $query = "UPDATE products SET $format WHERE id=?";
    conn($query, $_GET['id']);

    redirect(url('products.php'));
}


function uploadImage()
{
    $target_dir = "images/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $imgName = $target_dir . md5(date('Y-m-d H:i:s')) . '.' . $imageFileType;

    $acceptedTypes = ["jpg", "png", "jpeg", "gif"];

    if (!isset($_POST["submit"]) && file_exists($imgName) && !array_search($imageFileType, $acceptedTypes)) {
        redirect(url('product.php'));
    } elseif (!move_uploaded_file($_FILES["image"]["tmp_name"], $imgName)) {
        redirect(url('product.php'));
    }
    move_uploaded_file($_FILES["image"]["tmp_name"], $imgName);
    return $imgName;
}

if (!isset($product)) {
    $action = 'product.php';
} else {
    $action = url('product.php', ['id' => $_GET['id']]);
}
?>
<div style="margin: 5% 35% 5% 35%">
    <form method="post" action="<?= $action ?>" enctype="multipart/form-data">
        <div style="margin: 5px;">
            <input type="text" name="title" placeholder="<?= __('Title') ?>"
                   value="<?= (isset($product) ? strip_tags($product->title) : ''); ?>">
        </div>
        <div style="margin: 5px;">
            <textarea name="description"
                      placeholder="<?= __('Description') ?>"><?= (isset($product) ? strip_tags($product->description) : ''); ?></textarea>
        </div>
        <div style="margin: 5px;">
            <input type="text" name="price" placeholder="<?= __('Price') ?>"
                   value="<?= (isset($product) ? strip_tags($product->price) : ''); ?>">
        </div>
        <?php if (isset($product->image)): ?>
            <div>
                <img style="width: 80px; height: 80px;" src="<?= $product->image ?>">
            </div>
        <?php endif; ?>
        <div style="margin: 5px;">
            <input type="hidden" name="size" value="1000000">
            <input type="file" name="image" id="image"
                   value="<?= (isset($product) ? strip_tags($product->title) : ''); ?>">
        </div>
        <button type="submit"><?= __('Save') ?></button>
    </form>
    <a href="<?= url('products.php') ?>"><?= __('Products') ?></a>
</div>
<?php include 'footer.php' ?>

