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
    $is_error = false;
    if (!isset($_POST['title']) || empty($_POST['title'])) {
        $is_error = true;
        flash('title', 'Title is required');
    } else {
        old('title', $_POST['title']);
    }
    if (!isset($_POST['description']) || empty($_POST['description'])) {
        $is_error = true;
        flash('description', 'Description is required');
    } else {
        old('description', $_POST['description']);
    }
    if (!isset($_POST['price']) || empty($_POST['price'])) {
        $is_error = true;
        flash('price', 'Price is required');
    } else {
        old('price', $_POST['price']);
    }
    if ($is_error) {
        redirect($_SERVER['HTTP_REFERER']);
        return;
    }
    $params = ['title' => $_POST["title"], 'description' => $_POST["description"], 'price' => $_POST["price"]];
    $result = uploadImage();
    $params['image'] = $result;
    $paramKeys = implode(',', array_keys($params));
    $placeholders = implode(',', array_fill(0, count($params), '?'));
    $query = "INSERT INTO products ($paramKeys) VALUES ($placeholders)";
    conn($query, array_values($params));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && (isset($_GET["id"]))) {
    $params = ['title' => $_POST["title"], 'description' => $_POST["description"], 'price' => $_POST["price"]];
    if ($_FILES["image"]["name"]) {
        unlink($product->image);
        $image = uploadImage();
        $params['image'] = $image;
    }
    if ($image) {
        $format = [];
        foreach ($params as $key => $value) {
            $format[] = $key . '=' . "?";
        }
        $format = implode(', ', $format);
        $params['id'] = $_GET['id'];
        $query = "UPDATE products SET $format WHERE id=?";
        conn($query, array_values($params));
        redirect(url('products.php'));
    }
}


function uploadImage()
{
    if (!isset($_FILES['image']) || empty($_FILES['image'])) {
        flash('image-upload', 'Need to upload a image!');
        redirect($_SERVER['HTTP_REFERER']);
        return null;
    }
    $target_dir = "images/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $imgName = $target_dir . md5(date('Y-m-d H:i:s')) . '.' . $imageFileType;
    $acceptedTypes = ["image/jpg", "image/png", "image/jpeg", "image/gif"];

    if (!in_array($_FILES["image"]["type"], $acceptedTypes)) {
        flash('image-type', 'This image type cant be stored!');
        redirect($_SERVER['HTTP_REFERER']);
        return null;
    } elseif (!move_uploaded_file($_FILES["image"]["tmp_name"], $imgName)) {
        flash('image-uploaded', 'This image cant be stored!');
        redirect($_SERVER['HTTP_REFERER']);
        return null;
    }
    move_uploaded_file($_FILES["image"]["tmp_name"], $imgName);
    return $imgName;
}

if (isset($_SESSION['old']['title'])) {
    $title = old('title');
} else {
    $title = isset($product) ? strip_tags($product->title) : '';
}
if (isset($_SESSION['old']['description'])) {
    $description = old('description');
} else {
    $description = isset($product) ? strip_tags($product->description) : '';
}
if (isset($_SESSION['old']['price'])) {
    $price = old('price');
} else {
    $price = isset($product) ? strip_tags($product->price) : '';
}

?>
<div style="margin: 5% 35% 5% 35%">
    <?php
    if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])):
        foreach ($_SESSION['errors'] as $name => $value): ?>
            <p><?= flash($name) ?></p>
        <?php endforeach;
    endif;
    ?>
    <form method="post" enctype="multipart/form-data">
        <div style="margin: 5px;">
            <input type="text" name="title" placeholder="<?= __('Title') ?>" value="<?= $title ?>"/>
        </div>
        <div style="margin: 5px;">
            <textarea name="description" placeholder="<?= __('Description') ?>"><?= $title ?></textarea>
        </div>
        <div style="margin: 5px;">
            <input type="text" name="price" placeholder="<?= __('Price') ?>" value="<?= $price ?>"/>
        </div>
        <?php if (isset($product->image)): ?>
            <div>
                <img style="width: 80px; height: 80px;" src="<?= $product->image ?>">
            </div>
        <?php endif; ?>
        <div style="margin: 5px;">
            <input type="hidden" name="size" value="1000000">
            <input type="file" name="image" id="image"
                   value="<?= (isset($product) ? strip_tags($product->image) : ''); ?>"/>
        </div>
        <button name="submit" type="submit"><?= __('Save') ?></button>
    </form>
    <a href="<?= url('products.php') ?>"><?= __('Products') ?></a>
</div>
<?php include 'footer.php' ?>

