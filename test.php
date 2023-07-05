<?php

include ('../admin/db.php');

// Initialize the session
session_start();

// Check if the user is not logged in, redirect to the login page with an error message
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: auth-login.php?error=access_denied");
    exit;
}

// Check if the user has the right role to access the page
if (basename($_SERVER["PHP_SELF"]) === "index.php" && (!isset($_SESSION["admin"]) || $_SESSION["admin"] !== true)) {
    header("location: user_index.php");
    exit;
} elseif (basename($_SERVER["PHP_SELF"]) === "user_index.php" && isset($_SESSION["admin"]) && $_SESSION["admin"] === true) {
    header("location: index.php");
    exit;
}

// Get user information
$user_id = $_SESSION['user_id'];

// Fetch best selling products data by user_id, grouped by month
$query = "SELECT p.name as product_name, COUNT(o.product_id) as total_sold, MONTHNAME(o.order_date) as month, SUM(o.price) as total_price FROM orders o JOIN products p ON o.product_id = p.id WHERE o.user_id = ? AND YEAR(o.order_date) = YEAR(CURDATE()) GROUP BY o.product_id, MONTH(o.order_date) ORDER BY MONTH(o.order_date) ASC, total_sold DESC";
$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$best_selling_data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $best_selling_data[] = $row;
}


$json_best_selling_data = json_encode($best_selling_data);



?>


<!DOCTYPE html>
<html lang="en">

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div>
        <canvas id="doughnutChart"></canvas>
    </div>

    <script>
    const bestSellingData = <?php echo $json_best_selling_data; ?>;

    const doughnutChartLabels = bestSellingData.map(item => `${item.product_name} `);
    const doughnutChartData = bestSellingData.map(item => item.total_price);
    const backgroundColors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#E7E9ED'];

    const doughnutChartConfig = {
        type: 'doughnut',
        data: {
            labels: doughnutChartLabels,
            datasets: [{
                data: doughnutChartData,
                backgroundColor: backgroundColors
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Best selling products'
                },
                legend: {
                    position: 'right',
                }
            }
        }
    };

    const doughnutChartCtx = document.getElementById('doughnutChart').getContext('2d');
    new Chart(doughnutChartCtx, doughnutChartConfig);
    </script>

</body>

</html>