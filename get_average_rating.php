<?php
// Connect to your MySQL database (replace these values with your actual database credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "moon";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process the product ID received from the AJAX request
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $productId = $_GET["product_id"];

    // Fetch the average rating from the database (replace with your actual table names)
    $sql = "SELECT AVG(rating) AS average_rating FROM ratings WHERE product_id = '$productId'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $averageRating = $row["average_rating"];

        // Return the average rating as JSON
        echo json_encode(['average_rating' => round($averageRating, 2)]);
    } else {
        // No ratings found for the product
        echo json_encode(['average_rating' => 0]);
    }
}

// Close the database connection
$conn->close();
?>
