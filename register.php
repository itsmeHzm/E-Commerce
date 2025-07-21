<?php
$username = $_POST['username'];
$email = $_POST['email'];
$name = $_POST['name'];
$password = $_POST['password'];
$password1 = $_POST['confirm_password'];

// Check for empty fields
if (empty($username) || empty($email) || empty($name) || empty($password) || empty($password1)) {
    // Display alert for empty fields and redirect back to register.html
    echo '<script>
            alert("Please fill in all fields.");
            setTimeout(function(){
                window.location.href = "register.html";
            }, 1000);
          </script>';
    exit(); // Ensure that no other code is executed after the redirect
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'moon');
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
} else {
    // Check if the username already exists
    $check_username_stmt = $conn->prepare("SELECT * FROM registration WHERE username = ?");
    $check_username_stmt->bind_param("s", $username);
    $check_username_stmt->execute();
    $check_username_result = $check_username_stmt->get_result();
    $check_username_stmt->close();

    if ($check_username_result->num_rows > 0) {
        // Display username taken message using JavaScript
        echo '<script>
                alert("Username is already taken. Please choose another username.");
                setTimeout(function(){
                    window.location.href = "register.html";
                }, 1000);
              </script>';
        exit(); // Ensure that no other code is executed after the redirect
    }

    if ($password === $password1) {
        // Insert new user if username is unique
        $insert_stmt = $conn->prepare("INSERT INTO registration (username, name, email, password) VALUES (?, ?, ?, ?)");
        $insert_stmt->bind_param("ssss", $username, $name, $email, $password);

        if ($insert_stmt->execute()) {
            // Close the database connection
            $insert_stmt->close();
            $conn->close();

            // Display success message using JavaScript
            echo '<script>
                    alert("Register Successfully...");
                    setTimeout(function(){
                        window.location.href = "login.html";
                    }, 1000);
                  </script>';
            exit(); // Ensure that no other code is executed after the redirect
        } else {
            echo 'Error during registration: ' . $insert_stmt->error;
        }
    } else {
        // Display password mismatch message using JavaScript
        echo '<script>
                alert("Password does not match");
                setTimeout(function(){
                    window.location.href = "register.html";
                }, 1000);
              </script>';
        exit(); // Ensure that no other code is executed after the redirect
    }

    $conn->close();
}
?>
