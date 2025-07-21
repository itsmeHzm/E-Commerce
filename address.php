<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $streetAddress = $_POST['streetAddress'];
    $city = $_POST['city'];
    $postcode = $_POST['postcode'];
    $state = $_POST['state'];
    $phoneNum = $_POST['phoneNumber'];

    // Function to check if the string contains only numeric characters
    function isNumeric($str)
    {
        return preg_match('/^[0-9]+$/', $str);
    }

    // Function to check if a string is empty
    function isEmpty($str)
    {
        return empty(trim($str));
    }

    // Check for empty values
    if (isEmpty($streetAddress) || isEmpty($city) || isEmpty($postcode) || isEmpty($state) || isEmpty($phoneNum)) {
        // Display alert for empty fields and redirect back to the account.html
        echo '<script>
                alert("Please fill in all fields.");
                setTimeout(function(){
                    window.location.href = "account.html";
                }, 1000);
              </script>';
        exit(); // Ensure that no other code is executed after the redirect
    }

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'moon');
    if ($conn->connect_error) {
        die('Connection Failed: ' . $conn->connect_error);
    } else {
        // Check if the phone number contains only numeric characters
        if (isNumeric($phoneNum)) {
            $stmtAddress = $conn->prepare("UPDATE registration SET streetAddress = ?, city = ?, postcode = ?, state = ?, phoneNum = ? WHERE id = ?");
            $stmtAddress->bind_param("sssssi", $streetAddress, $city, $postcode, $state, $phoneNum, $_SESSION["id"]);
            $stmtAddress->execute();
            $stmtAddress->close();

            // Display the echo message using JavaScript alert
            echo '<script>
                    alert("Data update Successfully...");
                    setTimeout(function(){
                        window.location.href = "index.html";
                    }, 1000);
                  </script>';
            exit(); // Ensure that no other code is executed after the redirect
        } else {
            // Display phone number format error using JavaScript alert
            echo '<script>
                    alert("Invalid phone number format. Please enter numeric characters only.");
                    setTimeout(function(){
                        window.location.href = "account.html";
                    }, 1000);
                  </script>';
            exit(); // Ensure that no other code is executed after the redirect
        }
    }
}
