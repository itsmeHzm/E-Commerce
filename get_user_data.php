<?php

session_start();

if (isset($_SESSION["id"])) {
    $userId = $_SESSION["id"];

    $conn = new mysqli('localhost', 'root', '', 'moon');
    if ($conn->connect_error) {
        die('Connection Failed: ' . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT name, streetAddress, city, postcode, state, phoneNum FROM registration WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    // Error handling
    if ($stmt->errno) {
        echo "Error executing query: " . $stmt->error;
    } else {
        $result = $stmt->get_result();
        $userData = $result->fetch_assoc();
        $stmt->close();
        $conn->close();

        // Check if required address information is present
        if ($userData["name"] && $userData["streetAddress"] && $userData["city"] && $userData["postcode"] && $userData["state"] && $userData["phoneNum"]) {
            // Send user data as JSON response
            echo json_encode($userData);
        } else {
            // User has not submitted address information
            echo 'Please insert your address in the user';
        }
    }
} else {
    // User is not logged in
    echo 'User authentication failed. Please Log in or Register';
}
?>