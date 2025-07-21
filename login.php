<?php
session_start();
// Check if the user is already logged in
$isLoggedIn = isset($_SESSION['username']) && !empty($_SESSION['username']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get user input
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'moon');
    if ($conn->connect_error) {
        die('Connection Failed: ' . $conn->connect_error);
    }

    // SQL query with prepared statement
    $sql = "SELECT * FROM registration WHERE username = ? AND password = ?";

    // Create a prepared statement
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bind_param('ss', $username, $password);

    // Execute the statement
    $stmt->execute();

    // Get result set
    $result = $stmt->get_result();

    // Check if a row is returned
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id = $row["id"];
        // Authentication successful
        $_SESSION['id'] = $id;
        echo 'Login successful! Welcome ' . $username;
        // Add further actions if needed
        header('Location: index.html');
    } else {
        // Authentication failed
        echo '<script>
                alert("Login failed. Invalid username or password.");
                window.location.href = "login.html";
              </script>';
    }
    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>
