<?php
// remove_quantity.php

// Assuming you're working with a database (replace this with your database connection)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "moon";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve data from the request
$data = json_decode(file_get_contents("php://input"));

// Extract data
$product_id = $data->product_id;
$option = $data->option; // Assuming the 'option' key is used instead of 'size'

// Remove the item from the cart in the database using prepared statements
$sql = "DELETE FROM cart WHERE product_id = ? AND option = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $product_id, $option);

$response = ["success" => false]; // Default response

if ($stmt->execute()) {
    // Return a success response as JSON
    $response = ["success" => true];
} else {
    // Return an error response as JSON
    $response = ["error" => "Error removing item: " . $stmt->error];
}

// Close the prepared statement
$stmt->close();

// Close the database connection
$conn->close();

// Set the content type to JSON
header('Content-Type: application/json');

// Output the JSON response
echo json_encode($response);
