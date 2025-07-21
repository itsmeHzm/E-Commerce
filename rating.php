<?php
// Start the session
session_start();

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

// Process the rating data received from the AJAX request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the user is logged in
    if (isset($_SESSION['id'])) {
        $userId = $_SESSION['id'];
        $productName = $_POST["productName"];
        $rating = $_POST["rating"];

        // Validate the data (add more validation if needed)
        if (empty($productName) || empty($rating)) {
            echo "Error: Product name and rating are required.";
            exit();
        }

        // Get the product ID from the database based on the product name
        $sqlProductId = "SELECT product_id FROM products WHERE product_name = '$productName'";
        $resultProductId = $conn->query($sqlProductId);

        if ($resultProductId->num_rows > 0) {
            $row = $resultProductId->fetch_assoc();
            $productId = $row["product_id"];

            // Insert the rating into the database (replace with your actual table name)
            $sql = "INSERT INTO ratings (user_id, product_id, rating) VALUES ('$userId', '$productId', '$rating')";

            if ($conn->query($sql) === TRUE) {
                echo "Rating submitted successfully!";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "Error: Product not found.";
        }
    } else {
        // User is not logged in
        echo "Error: User not logged in.";
    }
}

// Close the database connection
$conn->close();
?>
