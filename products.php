<?php
include 'header.php';

if (!isset($_SESSION['user'])) {
    redirect(url('login.php'));
}
if (isset($_GET["id"])) {
    $query = "SELECT * FROM  products WHERE id=?";
    $product = conn($query, $_GET['id']);
    unlink($product->image);
    $query = "DELETE FROM products WHERE id=?";
    conn($query, $_GET['id']);
}
function getProducts()
{
    $query = "SELECT * FROM products";
    $items = conn($query, [], true);
    return $items;
}

?>
<div style="margin: 5% 35% 5% 35%;">
    <div>
        <?php foreach (getProducts() as $product): ?>
            <table style="width: 70%; border: solid 1px;">
                <tr>
                    <td><img style="width: 100px; height: 100px;" src="<?= $product->image ?>"></td>
                    <td>
                        <h3><?= strip_tags($product->title) ?></h3>
                        <p><?= strip_tags($product->description) ?></p>
                        <p><?= strip_tags($product->price) ?>$</p>
                    </td>
                    <td>
                        <a href="<?= url('product.php', ['id' => $product->id]) ?>"><?= __('Edit') ?></a>
                        <a href="<?= url('products.php', ['id' => $product->id]) ?>"><?= __('Delete') ?></a>
                    </td>
                </tr>
            </table>
        <?php endforeach; ?>
        <div style="margin: 5px;">
            <a name="add" href="<?= url('product.php') ?>"><?= __('Add') ?></a>
            <a name="log-out" href="<?= url('login.php?logout') ?>"><?= __('Logout') ?></a>
        </div>
    </div>
</div>
<?php include 'footer.php' ?>