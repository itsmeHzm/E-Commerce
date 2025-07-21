<?php

session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "moon";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process Add Product Form
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["addProduct"])) {
    $productName = $_POST["productName"];
    $productPrice = $_POST["productPrice"];

    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["productImage"]["name"]);

    if (move_uploaded_file($_FILES["productImage"]["tmp_name"], $target_file)) {
        $sql = "INSERT INTO products (product_name, price, image_path, stock_quantity) 
                VALUES ('$productName', $productPrice, '$target_file', 1)";

        if ($conn->query($sql) === TRUE) {
            echo "Product added successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Sorry, there was an error uploading your file.";
        echo "Debugging info: " . print_r($_FILES, true);
    }
}

// Process Add/Remove Stock Form
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["manageStock"])) {
    $productNameToUpdate = $_POST["productNameToUpdate"];
    $quantityToAdd = $_POST["quantityToAdd"];

    // Check if the product exists
    $checkProduct = $conn->prepare("SELECT * FROM products WHERE product_name = ? AND stock_quantity > 0");
    $checkProduct->bind_param("s", $productNameToUpdate);
    $checkProduct->execute();
    $checkProductResult = $checkProduct->get_result();

    if ($checkProductResult->num_rows > 0) {
        // Product exists and has positive stock quantity, update stock
        $sql = "UPDATE products SET stock_quantity = stock_quantity + $quantityToAdd WHERE product_name = '$productNameToUpdate'";

        if ($conn->query($sql) === TRUE) {
            // Display success alert and redirect using JavaScript
            echo '<script>';
            echo 'alert("Stock updated successfully for product: ' . $productNameToUpdate . '");';
            echo 'window.location.href = "add_product.html";';
            echo '</script>';
        } else {
            // Display error alert using JavaScript
            echo '<script>alert("Error updating stock: ' . $conn->error . '");</script>';
        }
    } else {
        // Product does not exist or has zero stock
        // Display alert and redirect using JavaScript
        echo '<script>';
        echo 'alert("Product not found or out of stock: ' . $productNameToUpdate . '");';
        echo 'window.location.href = "add_product.html";';
        echo '</script>';
    }

    $checkProduct->close();
}

// Fetch all products with positive stock quantity or filtered products based on product_category
$sql = "SELECT * FROM products WHERE stock_quantity > 0";
if (isset($_GET['productCategory'])) {
    $category = $_GET['productCategory'];
    $sql .= " AND product_category = '$category'";
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $products = array();
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    // Close the database connection
    $conn->close();

    // Return product data as JSON
    header('Content-Type: application/json');
    echo json_encode($products);
} else {
    // Close the database connection
    $conn->close();

    // Return an empty array as JSON
    header('Content-Type: application/json');
    echo json_encode(array());
}
?>
