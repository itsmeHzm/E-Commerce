<?php
session_start();

// Establish a database connection (modify these values based on your database setup)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "moon";

$conn = new mysqli($servername, $username, $password, $dbname);

$directoryPath = 'uploads';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productName = $_POST["productName"];
    $productPrice = $_POST["productPrice"];
    $productCategory = $_POST["productCategory"];

    // Check for empty values
    if (empty($productName) || empty($productPrice) || empty($productCategory) || empty($_FILES["productImage"]["name"])) {
        echo "Please fill in all fields.";
        exit();
    }

    // File upload handling
    $target_dir = "uploads/";  // Change this to the directory where you want to store the uploaded images
    $target_file = $target_dir . basename($_FILES["productImage"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if the file is a JPEG or PNG image
    if ($imageFileType != "jpeg" && $imageFileType != "png" && $imageFileType != "jpg") {
        echo '<script>
                alert("Invalid file type. Only JPEG and PNG files are allowed.");
                setTimeout(function(){
                    window.location.href = "add_product.html";
                }, 1000);
              </script>';
        exit();
    }

    // Check if the file was successfully uploaded
    if (move_uploaded_file($_FILES["productImage"]["tmp_name"], $target_file)) {
        // SQL query to insert data into the products table
        $sql = "INSERT INTO products (product_name, price, product_category, image_path, stock_quantity) 
                VALUES ('$productName', $productPrice, '$productCategory', '$target_file', 1)";

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

// Fetch all products or filtered products based on product_category
$sql = "SELECT * FROM products WHERE stock_quantity > 0";
if (isset($_GET['productCategory'])) {
    $category = $_GET['productCategory'];
    $sql = "SELECT * FROM products WHERE product_category = '$category' AND stock_quantity > 0";
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
