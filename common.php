<?php
include 'config.php';
session_start();


if (!isset($_SESSION['cart'])) {
	$_SESSION['cart'] = [];
}


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

	if (empty($_SESSION['cart'])) {
		redirect($_SERVER['HTTP_REFERER']);
		return;
	}

	if (isset($_POST['email'])) {
		$to = $subject = $headers = '';

		ob_start();
		include 'send-email.php';
		$message = ob_get_contents();
		ob_end_clean();

		$mail = mail($to, $subject, $message, implode("\r\n", $headers));

	}
	redirect(url('cart.php', ['mail' => $mail]));

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
			return $imgName;
		} else {
			echo "Sorry, there was an error uploading your file.";
		}
	}
	return null;
}

function getProduct($id) {
	$query = "SELECT * FROM  products WHERE id=?";
	$item = conn($query, $id);
	return $item;
}

function removeAllCart() {
	$_SESSION['cart'] = [];
	redirect(url('index.php'));
}


function removeCart() {
	if (empty($product_id)) {
		redirect($_SERVER['HTTP_REFERER']);
		return;
	}
	$query = "SELECT * FROM products WHERE id=?";
	conn($query, $_GET['remove-cart']);
	foreach ($_SESSION['cart'] as $cart) {
		if ($product_id == $cart->id) {
			$index = array_search($cart, $_SESSION["cart"]);
			unset($_SESSION["cart"][$index]);
			redirect(url('cart.php'));
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
	foreach ($_SESSION['cart'] as $cart) {
		if ($product_id == $cart->id) {
			redirect(url('index.php'));
			return;
		}
	}
	array_push($_SESSION['cart'], getProduct($product_id));
	redirect(url('index.php'));
	return;
}


function deleteProduct() {
	$query = "DELETE FROM products WHERE id=?";
	$product = getProduct($_GET['delete-product']);
	conn($query, $_GET['delete-product']);
	unlink($product->image);

	redirect(url('products.php'));
}


function updateProduct() {
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$params = ['title' => $_POST["title"], 'description' => $_POST["description"], 'price' => $_POST["price"]];
	}

	if ($_FILES["image"]["name"]) {
		$product = getProduct(array_values($_GET['update-product']));
		unlink($product->image);
		$image = uploadImage();
		$bindVars['image'] = $image;
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


function createProduct() {
	$params = ['title' => $_POST["title"], 'descrition' => $_POST["description"], 'price' => $_POST["price"]];
	$image = uploadImage();

	$params = array_values($params);
	array_push($params, $image);

	array_values($params);
	$query = "INSERT INTO products (title,description,price,image) VALUES (?,?,?,?)";
	conn($query, $params);
	//dd($params);
	redirect(url('products.php'));
}


function getProducts() {
	$query = "SELECT * FROM products";
	$items = conn($query, [], true);
	return $items;
}


function editProduct() {
	$query = "SELECT * FROM  products WHERE id=?";
	$items = conn($query, $_GET['edit-product']);

	redirect(url('product.php', $items));
}


function login() {
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$name = $_POST["username"];
		$password = $_POST["password"];
	}
	$query = "SELECT * FROM users";
	$result = conn($query);
	if ($result->username == $name && $result->password == $password) {
		$_SESSION['user'] = true;
	}
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


function conn($query, $params = [], $get_list = false) {
	$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$stmt = $conn->prepare($query);
	if (!empty($params)) {
		DynamicBindVariables($stmt, $params);
	}
	$stmt->execute();

	$result = $stmt->get_result();

	$stmt->close();
	$items = [];
	if ($result) {
		if ($get_list) {
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

function bindTypes($param) {
	$types = '';

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

	return $types;
}

function DynamicBindVariables($stmt, $params) {

	if ($params != null) {
		// Generate the Type String (eg: 'issisd')
		$bind_names = [];
		if (is_array($params)) {
			$types = '';
			foreach ($params as $param) {
				$types .= bindTypes($param);
			}
			$bind_names[] = $types;
			for ($i = 0; $i < count($params); $i++) {
				// Create a variable Name
				$bind_name = 'bind' . $i;
				// Add the Parameter to the variable Variable
				$$bind_name = $params[$i];

				// Associate the Variable as an Element in the Array
				$bind_names[] = &$$bind_name;
			}
		} else {
			$param = $params;
			$type = '';
			$type .= bindTypes($params);
			$stmt->bind_param($type, $param);
		}

		call_user_func_array(array($stmt, 'bind_param'), $bind_names);
	}

	return $stmt;
}


function redirect($location) {
	header('Location: ' . $location);
	exit();
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
