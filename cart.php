<?php
// Include your database connection file or code here
// For example: include('db_connection.php');

session_start();

// Sample database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "moon";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json_data = file_get_contents('php://input');
    $cart_data = json_decode($json_data, true);

    // Check for user authentication (replace with your authentication logic)
    if (isset($_SESSION['id'])) {
        $id = $_SESSION['id'];

        // Insert cart item into the database
        $product_id = $cart_data['product_id'];
        $option = $cart_data['option'] ?? 'default_option'; // Use 'default_option' or choose an appropriate default
        // Check if the "quantity" key exists in the JSON data
        $quantity = $cart_data['quantity'] ?? 1; // Default quantity or choose an appropriate value
        $price = $cart_data['price'];

        $sql = "INSERT INTO cart (id, product_id, option, quantity, price) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issdd", $id, $product_id, $option, $quantity, $price);


        if ($stmt->execute()) {
            echo 'Item added to the cart successfully!';
        } else {
            echo 'Error adding item to the cart: ' . $stmt->error;
        }

        $stmt->close();
    } else {
        echo 'User authentication failed. Please Log in or Register';
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch cart items for the authenticated user from the database
    // Check for user authentication (replace with your authentication logic)
    if (isset($_SESSION['id'])) {
        $id = $_SESSION['id'];
        $sql = "SELECT c.cart_id, c.product_id, c.option, c.quantity, c.price, p.image_path, p.product_name 
        FROM cart c 
        JOIN products p ON c.product_id = p.product_id 
        WHERE c.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
    }

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $cart_items = $result->fetch_all(MYSQLI_ASSOC);

        // Return cart items as JSON
        header('Content-Type: application/json');
        echo json_encode($cart_items);
        exit(); // Ensure that no HTML is appended after echoing JSON
    } else {
        $error = ["error" => "Error fetching cart items: " . $stmt->error];
        header('Content-Type: application/json');
        echo json_encode($error);
        exit();
    }
} else {
    $error = ["error" => "Invalid request method."];
    header('Content-Type: application/json');
    echo json_encode($error);
    exit();
}

$conn->close();
?>