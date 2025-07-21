<?php
// update_quantity.php

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
$option = $data->option;
$newQuantity = $data->quantity;

// Update the quantity in the database (modify this based on your database schema)
$sql = "UPDATE cart SET quantity = $newQuantity WHERE product_id = $product_id AND option = '$option'";

$response = ["success" => false, "error" => ""]; // Default response

if ($conn->query($sql) === TRUE) {
    // Return a success response as JSON
    $response["success"] = true;
} else {
    // Return an error response as JSON
    $response["error"] = "Error updating quantity: " . $conn->error;
}

// Close the database connection
$conn->close();

// Set the content type to JSON
header('Content-Type: application/json');

// Output the JSON response
echo json_encode($response);
?>