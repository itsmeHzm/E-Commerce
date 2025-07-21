<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "moon";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch product sales data
$salesSql = "SELECT product_name, SUM(quantity) AS total_quantity
        FROM payments
        GROUP BY product_name
        ORDER BY total_quantity DESC
        LIMIT 10"; // Change the limit based on the number of top products you want to include

$salesResult = $conn->query($salesSql);

// Initialize an array to store product sales data
$salesData = [];

if ($salesResult->num_rows > 0) {
    while ($row = $salesResult->fetch_assoc()) {
        $salesData[$row['product_name']] = $row['total_quantity'];
    }
}

// Fetch average rating data
$ratingSql = "SELECT product_name, AVG(rating) AS avg_rating
              FROM products
              LEFT JOIN ratings ON products.product_id = ratings.product_id
              GROUP BY product_name
              ORDER BY avg_rating DESC
              LIMIT 10"; // Change the limit based on the number of top products you want to include

$ratingResult = $conn->query($ratingSql);

// Initialize an array to store rating data
$ratingData = [];

if ($ratingResult->num_rows > 0) {
    while ($row = $ratingResult->fetch_assoc()) {
        $ratingData[$row['product_name']] = $row['avg_rating'];
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Sales and Rating Report - MoonStore</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Bootstrap CSS-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Bootstrap icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="css/styles.css" rel="stylesheet" />
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <!-- Navigation-->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container px-4 px-lg-5">
            <a class="navbar-brand" href="index.html">MoonStore ˚☾˚｡⋆ Admin Panel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" id="navbarDropdown" href="index.html" role="button" data-bs-toggle="dropdown" aria-expanded="false">Shop</a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="index.html">Merchandise</a></li>
                            <li><a class="dropdown-item" href="Credit.html">Game Credit</a></li>

                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" aria-current="page" href="report.php">Report</a></li>
                    <li class="nav-item"><a class="nav-link" href="add_product.html">Add Product</a></li>
                </ul>
                <form class="d-flex">
                    <!-- Logout button -->
                    <a href="logout_admin.php" class="btn btn-danger ms-2 mt-2">Logout</a>
                </form>
            </div>
        </div>
    </nav>


    <!-- Sales Report Section -->
    <section class="container mt-5 mb-5">
        <h2>Product Sales Report</h2>
        <canvas id="salesPieChart" width="300" height="300"></canvas>
    </section>

    <!-- Rating Report Section -->
    <section class="container mt-5 mb-5">
        <h2>Product Rating Report</h2>
        <canvas id="ratingPieChart" width="300" height="300"></canvas>
    </section>

    <!-- Footer-->
    <footer class="py-5 bg-dark">
        <!-- ... (unchanged) ... -->
    </footer>

    <!-- JavaScript for Chart.js -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get the canvas element and create a 2D context
            const salesCanvas = document.getElementById('salesPieChart');
            const salesCtx = salesCanvas.getContext('2d');

            const ratingCanvas = document.getElementById('ratingPieChart');
            const ratingCtx = ratingCanvas.getContext('2d');

            // Create an array of colors for each product
            const colors = ['#FF0000', '#FF8C00', '#FFD700', '#ADFF2F', '#32CD32', '#800080', '#0000FF', '#4682B4', '#008080', '#008000'];

            // Parse the JSON-encoded data for sales
            const salesData = <?php echo json_encode(array_values($salesData)); ?>;
            const salesLabels = <?php echo json_encode(array_keys($salesData)); ?>;

            // Log data and labels to the console for debugging
            console.log(salesData);
            console.log(salesLabels);

            // Create a pie chart for sales
            const salesPieChart = new Chart(salesCtx, {
                type: 'pie',
                data: {
                    labels: salesLabels,
                    datasets: [{
                        data: salesData,
                        backgroundColor: colors
                    }]
                }
            });

            // Parse the JSON-encoded data for ratings
            const ratingData = <?php echo json_encode(array_values($ratingData)); ?>;
            const ratingLabels = <?php echo json_encode(array_keys($ratingData)); ?>;

            // Log data and labels to the console for debugging
            console.log(ratingData);
            console.log(ratingLabels);

            // Create a pie chart for ratings
            const ratingPieChart = new Chart(ratingCtx, {
                type: 'pie',
                data: {
                    labels: ratingLabels,
                    datasets: [{
                        data: ratingData,
                        backgroundColor: colors
                    }]
                }
            });
        });
    </script>

    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Core theme JS-->
    <script src="js/scripts.js"></script>
</body>

</html>