<?php

session_start();

// Establish a database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "moon";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if product_id is set in the request
if (isset($_GET['product_id'])) {
    $productId = $_GET['product_id'];

    // Fetch product name based on product_id
    $sql = "SELECT product_name FROM products WHERE product_id = $productId";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $productName = $row['product_name'];

        // Return product name as JSON
        header('Content-Type: application/json');
        echo json_encode(array('productName' => $productName));
    } else {
        // Product not found
        header('Content-Type: application/json');
        echo json_encode(array('error' => 'Product not found'));
    }
} else {
    // Product_id parameter not provided
    header('Content-Type: application/json');
    echo json_encode(array('error' => 'Product_id parameter not provided'));
}

$conn->close();
?>
