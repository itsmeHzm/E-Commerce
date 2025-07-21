<?php

session_start();
// Assuming you have a database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "moon";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
if (isset($_SESSION["id"])) {
    $userId = $_SESSION["id"];

    // Fetch transaction history for the user with product details
    $sql = "SELECT p.product_id, p.product_name, p.price, pa.quantity, pa.payment_method, pa.date 
            FROM payments pa
            JOIN products p ON pa.product_id = p.product_id
            WHERE pa.id = '$userId'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Display transaction history in HTML
        while ($row = $result->fetch_assoc()) {
            echo "<p>Date: " . $row["date"] . "<br>";
            echo "Payment Method: " . $row["payment_method"] . "<br>";
            echo "Product ID: " . $row["product_id"] . "<br>";
            echo "Product Name: " . $row["product_name"] . "<br>";
            echo "Quantity: " . $row["quantity"] . "<br>";
            echo "Price: " . $row["price"] . "</p>";
            echo "<hr>";
        }
    } else {
        echo "No transactions found.";
    }
} else {
    // Handle the case when the user is not logged in
    echo "<p>Please log in to view your transaction history.</p>";
}

// Close the database connection
$conn->close();
?>
