<?php

// Check if the ID parameter is set
if (isset($_GET['id'])) {
    $productId = $_GET['id'];

  

    // Your database connection code goes here
    // Replace the following with your actual database connection logic
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "moon";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // SQL query to retrieve product details by ID
    $sql = "SELECT * FROM products WHERE product_id = $productId";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch the data as an associative array
        $row = $result->fetch_assoc();

        // Return the data as JSON
        header('Content-Type: application/json');
        echo json_encode([$row]);

    } else {
        // No product found with the given ID
        echo json_encode(['error' => 'Product not found']);
    }

    // Close the database connection
    $conn->close();
} else {
    // No ID parameter provided
    echo json_encode(['error' => 'No product ID provided']);
}

?>
