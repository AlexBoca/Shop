<?php
include 'config.php';
session_start();


if (!isset($_SESSION['cart'])) {
	$_SESSION['cart'] = [];
}
//unset($_SESSION['cart']);
if (isset($_GET['logout'])) {
	logout();
}
if (isset($_GET['send-email'])) {
	sendEmail();
}
if (isset($_SESSION['user'])) {
	if (isset($_GET["edit-product"])) {
		editProduct();
	}
	if (isset($_GET["delete-product"])) {
		deleteProduct();
	}
}

if (isset($_GET["add-cart"])) {
	addCart();
}
if (isset($_GET["remove-cart"])) {
	removeCart();
}
if (isset($_GET["remove-all-cart"])) {
	removeAllCart();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (isset($_GET['login'])) {
		login();
	}
	if (isset($_SESSION['user'])) {
		if (isset($_GET['create-product'])) {
			createProduct();
		}
		if (isset($_GET['update-product'])) {
			updateProduct();
		}
	}
}

function sendEmail() {
	ini_set('sendmail_from', "myself@my.com");
	ini_set('SMTP', "mail.bigpond.com");
	ini_set('smtp_port', 25);

	if (empty($_SESSION['cart'])) {
		redirect($_SERVER['HTTP_REFERER']);
		return;
	}

	if (isset($_POST['email'])) {
		$to = MANAGER_EMAIL;
		$subject = "Shop";
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$headers .= 'From: <' . filter_var($_POST['email']) . '>' . "\r\n";

		$productList = '';
		$products = $_SESSION['cart'];
		foreach ($products as $product) {

			$productList .= "
				<tr>
            	<td>  $product->title </td>
            	<td>  $product->price </td>
       			</tr>";
		};

		$message = "<html>
			<head>
    		<title>Cart list from ClotheShop</title>
			</head>
			<body>
			<table>
    		<thead>
    		<tr>
        	<th>Title</th>
        	<th>Price</th>
    		</tr>
    		</thead>
    		<tbody>
    		$productList
    		</tbody>
			</table>
			</body>
			</html>
			";
		$mail = [$to, $subject, $message, $headers];
		dd($mail);
		mail($to, $subject, $message, $headers);

	}
	return;

}


function uploadImage() {
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
			echo "The file " . basename($_FILES["image"]["name"]) . " has been uploaded.";
			return $imgName;
		} else {
			echo "Sorry, there was an error uploading your file.";
		}
	}
	return null;
}

function getProduct($id) {
	$query = "SELECT * FROM  products WHERE id=?";
	$item = conn($query, $id, true);
	return $item;
}


function removeAllCart() {
	$_SESSION['cart'] = [];
	redirect(url('index.php'));
}

function removeCart() {
	$product_id = $_GET['remove-cart'];
	if (empty($product_id)) {
		redirect($_SERVER['HTTP_REFERER']);
		return;
	}
	$query = "SELECT * FROM products WHERE id=?";
	conn($query, array_values(['id' => $product_id]));
	foreach ($_SESSION['cart'] as $cart) {
		if ($product_id == $cart->id) {
			$index = array_search($cart, $_SESSION["cart"]);
			unset($_SESSION["cart"][$index]);
			redirect(url('index.php'));
			return;
		}
	}
	return;

}

function addCart() {

	$product_id = $_GET['add-cart'];
	if (empty($product_id)) {
		redirect($_SERVER['HTTP_REFERER']);
		return;
	}
	$query = "SELECT * FROM products WHERE id=?";
	$result = conn($query, array_values(['id' => $product_id]), true);
	foreach ($_SESSION['cart'] as $cart) {
		if ($product_id == $cart->id) {
			redirect(url('index.php'));
			return;
		}
	}
	array_push($_SESSION['cart'], $result);
	redirect(url('cart.php'));
	return;
}


function deleteProduct() {
	$arrayVal = ['id' => $_GET['delete-product']];
	$query = "DELETE FROM products WHERE id=?";
	$product = getProduct(array_values($arrayVal));
	conn($query, array_values($arrayVal));
	unlink($product->image);

	redirect(url('products.php'));
}


function updateProduct() {
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$bindVars = ['title' => $_POST["title"], 'description' => $_POST["description"], 'price' => $_POST["price"]];
		$condition = ['id' => $_GET['update-product']];
	}

	if ($_FILES["image"]["name"]) {
		$product = getProduct(array_values($condition));
		unlink($product->image);
		$image = uploadImage();
		$bindVars['image'] = $image;
	}

	$query = sprintf('UPDATE products SET %s WHERE id=%s', implode(', ', constructValues($bindVars, true)), constructValues($condition));
	conn($query, array_values(array_merge($bindVars, $condition)));

	redirect(url('products.php'));
}

function createProduct() {
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$params = [$_POST["title"], $_POST["description"], $_POST["price"]];
	}
	$image = uploadImage();
	array_push($params, $image);

	$query = "INSERT INTO products (title,description,price,image) VALUES (?,?,?,?)";
	$arrayVal = array_values($params);
	conn($query, $arrayVal);
	redirect(url('products.php'));
}

function getProducts() {
	$query = "SELECT * FROM products";
	$items = conn($query);
	return $items;
}

function editProduct() {
	$query = "SELECT * FROM  products WHERE id=?";
	$arrayVal = array_values(['id' => $_GET['edit-product']]);
	$items = conn($query, $arrayVal, true);


	redirect(url('product.php', $items));
}

function conn($query, $bindVars = [], $is_list = false) {
	$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$stmt = $conn->prepare($query);
	if (!empty($bindVars)) {
		DynamicBindVariables($stmt, $bindVars);
	}
	$stmt->execute();

	$result = $stmt->get_result();

	$stmt->close();
	$items = [];
	if ($result) {
		if (!$is_list) {
			while ($obj = $result->fetch_object()) {
				array_push($items, $obj);
			}
		} else {
			$items = $result->fetch_object();
		}
		$result->close();
	}

	return $items;
}


function login() {
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$name = $_POST["username"];
		$password = $_POST["password"];
	}
	$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$query = "SELECT id, username, password FROM users";
	$result = $conn->query($query);
	$result = $result->fetch_object();

	if ($result->username == $name && $result->password == $password) {
		$_SESSION['user'] = true;
	}
	$conn->close();
	if (ADMIN_USERNAME == $name || ADMIN_PASSWORD == $password) {
		redirect(url('products.php'));
	}
	redirect('index.php');
}

function logout() {
	if (isset($_SESSION['user'])) {
		unset($_SESSION['user']);
	}
	redirect(url('index.php'));
}

function constructValues($params, $is_set = false) {
	if ($is_set === true) {
		$setFormat = [];
		foreach ($params as $key => $value) {
			$setFormat[] = $key . '=' . '?';
		}
		return $setFormat;
	}
	$values = [];
	for ($i = 0; $i < count($params); $i++) {
		$values[] = '?';
	}
	return implode(',', $values);
}

function DynamicBindVariables($stmt, $params) {

	if ($params != null) {
		// Generate the Type String (eg: 'issisd')
		$types = '';
		foreach ($params as $param) {
			if (is_int($param)) {
				// Integer
				$types .= 'i';
			} elseif (is_float($param)) {
				// Double
				$types .= 'd';
			} elseif (is_string($param)) {
				// String
				$types .= 's';
			} else {
				// Blob and Unknown
				$types .= 'b';
			}
		}
		// Add the Type String as the first Parameter
		$bind_names[] = $types;
		// Loop thru the given Parameters
		for ($i = 0; $i < count($params); $i++) {
			// Create a variable Name
			$bind_name = 'bind' . $i;
			// Add the Parameter to the variable Variable
			$$bind_name = $params[$i];

			// Associate the Variable as an Element in the Array
			$bind_names[] = &$$bind_name;
		}
		//dd($bind_names);

		// Call the Function bind_param with dynamic Parameters
		call_user_func_array(array($stmt, 'bind_param'), $bind_names);
	}
	return $stmt;
}


function redirect($location) {
	header('Location: ' . $location);
}

function url($location, $params = []) {
	$url = BASE_URL . str_replace('//', '/', '/' . $location);
	if (!empty($params)) {
		$url .= '?' . http_build_query($params);
	}
	return $url;
}

function dd($name) {
	var_dump($name);
	die();
}
