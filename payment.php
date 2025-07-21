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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the paymentMethod is set in the POST request
    if (isset($_POST["paymentMethod"])) {
        // Get payment information from the form
        $paymentMethod = $_POST["paymentMethod"];

        // Check if the session variable 'id' is set
        if (isset($_SESSION['id'])) {
            // Get the user ID from the session variable
            $userId = $_SESSION['id'];

            // Get the current date
            $date = date("Y-m-d");

            // Fetch product_id, quantity, price, and product_name values from the cart for the user
            $fetchCartItemsSQL = "SELECT c.product_id, c.quantity, c.price, p.product_name, p.stock_quantity 
                                  FROM cart c
                                  JOIN products p ON c.product_id = p.product_id
                                  WHERE c.id = '$userId'";
            $result = $conn->query($fetchCartItemsSQL);

            if ($result) {
                // Insert into the "payments" table with user ID, payment method, product_id, quantity, price, product_name, and date
                while ($row = $result->fetch_assoc()) {
                    $productId = $row['product_id'];
                    $quantity = $row['quantity'];
                    $price = $row['price'];
                    $productName = $row['product_name'];
                    $stockQuantity = $row['stock_quantity'];

                    // Update stock_quantity in products table
                    $newStockQuantity = $stockQuantity - $quantity;
                    $updateStockSQL = "UPDATE products SET stock_quantity = '$newStockQuantity' WHERE product_id = '$productId'";
                    if ($conn->query($updateStockSQL) !== TRUE) {
                        echo "<script>alert('Error updating stock quantity: " . $conn->error . "');</script>";
                        exit();
                    }

                    // Insert into the "payments" table
                    $sql = "INSERT INTO payments (id, product_id, quantity, price, product_name, payment_method, date) 
                            VALUES ('$userId', '$productId', '$quantity', '$price', '$productName', '$paymentMethod', '$date')";

                    if ($conn->query($sql) !== TRUE) {
                        echo "<script>alert('Error: " . $sql . "\\n" . $conn->error . "');</script>";
                        exit();
                    }
                }

                // Remove items from the cart after successful payment
                $removeCartItemsSQL = "DELETE FROM cart WHERE id = '$userId'";
                if ($conn->query($removeCartItemsSQL) !== TRUE) {
                    echo "<script>alert('Error removing items from the cart: " . $conn->error . "');</script>";
                    exit();
                }

                echo "<script>alert('Thank you for buying with us!'); window.location.href = 'index.html';</script>";
                exit();
            } else {
                echo "<script>alert('Error fetching cart items: " . $conn->error . "');</script>";
            }
        } else {
            echo "<script>alert('User ID not set. Please log in before making a purchase.'); window.location.href = 'login.html';</script>";
        }
    } else {
        echo "<script>alert('Please choose a payment method before proceeding to checkout.'); window.location.href = 'payment.html';</script>";
    }
}

// Close the database connection
$conn->close();
?>