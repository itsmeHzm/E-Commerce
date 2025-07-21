<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "moon";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $adminUsername = $_POST["admin_username"];
    $adminPassword = $_POST["admin_password"];

    // SQL query to check admin credentials
    $sql = "SELECT * FROM admin WHERE admin_username = '$adminUsername' AND admin_password = '$adminPassword'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Admin authentication successful
        echo "<script>alert('Admin login successful!'); window.location.href = 'add_product.html';</script>";
        // Alternatively, you can use header("Location: add_product.html"); for server-side redirection
    } else {
        // Admin authentication failed
        echo "<script>alert('Invalid admin credentials'); window.location.href = 'admin_login.html';</script>";
        // Alternatively, you can use header("Location: login.html"); for server-side redirection
    }
}

// Close the connection
$conn->close();
?>
