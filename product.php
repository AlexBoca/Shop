<?php include 'header.php';

if (!$_SERVER['REQUEST_METHOD'] == 'POST' || !isset($_SESSION['user'])) {
    redirect($_SERVER['HTTP_REFERER']);
}

if (isset($_GET['create-product'])) {
    $params = ['title' => $_POST["title"], 'descrition' => $_POST["description"], 'price' => $_POST["price"]];
    $image = uploadImage();

    $params = array_values($params);
    array_push($params, $image);

    $query = "INSERT INTO products (title,description,price,image) VALUES (?,?,?,?)";
    conn($query, $params);
    redirect(url('products.php'));
}

if (isset($_GET['update-product'])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $params = ['title' => $_POST["title"], 'description' => $_POST["description"], 'price' => $_POST["price"]];
    }
    if ($_FILES["image"]["name"]) {
        $query = "SELECT * FROM  products WHERE id=?";
        $product = conn($query, $_GET['update-product']);
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
    conn($query, $_GET['update-product']);

    redirect(url('products.php'));
}

function uploadImage()
{
    $target_dir = "images/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $imgName = $target_dir . md5(date('Y-m-d H:i:s')) . '.' . $imageFileType;

    if (isset($_POST["submit"])) {
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
    }
    // Check if file already exists
    if (file_exists($imgName)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }
    if ($_FILES["image"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $imgName)) {
            return $imgName;
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
    return null;
}

if (!isset($_GET['id'])) {
    $action = 'product.php?create-product';
    $title = $description = $price = $image = '';
} else {
    $title = $_GET['title'];
    $description = $_GET['description'];
    $price = $_GET['price'];
    $image = $_GET['image'];
    $action = url('product.php', ['update-product' => $_GET['id']]);
}
?>
<div style="margin: 5% 35% 5% 35%">
    <form method="post" action="<?= $action ?>" enctype="multipart/form-data">
        <div style="margin: 5px;">
            <input type="text" name="title" placeholder="<?= __('Title') ?>" value="<?= strip_tags($title) ?>">
        </div>
        <div style="margin: 5px;">
            <textarea name="description"
                      placeholder="<?= __('Description') ?>"><?= strip_tags($description) ?></textarea>
        </div>
        <div style="margin: 5px;">
            <input type="text" name="price" placeholder="<?= __('Price') ?>" value="<?= strip_tags($price) ?>">
        </div>
        <?php if ($image): ?>
            <div>
                <img style="width: 80px; height: 80px;" src="<?= $image ?>">
            </div>
        <?php endif; ?>
        <div style="margin: 5px;">
            <input type="hidden" name="size" value="1000000">
            <input type="file" name="image" id="image" value="<?= $image ?>">
        </div>
        <button type="submit"><?= __('Save') ?></button>
    </form>
    <a href="<?= url('products.php') ?>"><?= __('Products') ?></a>
</div>
<?php include 'footer.php' ?>

