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

$stmt = mysqli_prepare($link, "SELECT username, admin FROM users WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $username = $row['username'];
    $isAdmin = $row['admin'];
} else {
    $_SESSION['status'] = "Access denied. Please log in to continue.";
    header("Location: auth-login.php");
    exit(0);
}

// Check if the user has the right role to access the page
if ($isAdmin != 1) {
    header("location: auth-login.php");
    exit(0);
}

// Set the user role based on the value of $isAdmin
$userRole = $isAdmin == 1 ? "Admin" : "User";

// Function to get the total number of orders by user_id
function getTotalOrders($link, $user_id) {
    // Prepare the SQL query to count the orders
    $sql = "SELECT COUNT(*) as totalOrders FROM orders WHERE user_id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = $result->fetch_assoc();
    return $row['totalOrders'];
}

// Function to get the total price by user_id
function getTotalPrice($link, $user_id) {
    // Prepare the SQL query to sum the price column
    $sql = "SELECT SUM(price) as totalPrice FROM orders WHERE user_id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = $result->fetch_assoc();
    return $row['totalPrice'];
}

// Get the total number of orders and total price by user_id
$totalOrders = getTotalOrders($link, $user_id);
$totalPrice = getTotalPrice($link, $user_id);
if (empty($totalPrice)) {
    $totalPrice = 0;
}

// Fetch product sales data by user_id
$query = "SELECT DATE(order_date) AS date, currency, SUM(price) AS total_price FROM orders WHERE user_id = ? GROUP BY DATE(order_date), currency";
$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$sales_data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $sales_data[] = $row;
}

// Function to fill missing days in sales data
function fillMissingDays($sales_data) {
    if (empty($sales_data)) {
        return [];
    }
    $filled_data = [];
    $start_date = new DateTime(min(array_column($sales_data, 'date')));
    $end_date = new DateTime(date('Y-m-d'));
    $interval = new DateInterval('P1D');
    $date_range = new DatePeriod($start_date, $interval, $end_date);

    foreach ($date_range as $date) {
        $formatted_date = $date->format('Y-m-d');
        $found = false;
        foreach ($sales_data as $sale) {
            if ($formatted_date == $sale['date']) {
                $found = true;
                $filled_data[] = $sale;
                break;
            }
        }
        if (!$found) {
            $filled_data[] = [
                'date' => $formatted_date,
                'currency' => '',
                'total_price' => 0
            ];
        }
    }

    return $filled_data;
}

$sales_data = fillMissingDays($sales_data);

// Calculate total income for each day
$daily_income = [];
foreach ($sales_data as $sale) {
    $date = $sale['date'];
    if (!isset($daily_income[$date])) {
        $daily_income[$date] = 0;
    }
    $daily_income[$date] += $sale['total_price'];
}

// Get total daily sales by user_id
$sql = "SELECT SUM(price) as total_daily_sales FROM orders WHERE user_id = ? AND DATE(order_date) = CURDATE()";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = $result->fetch_assoc();
$totalDailySales = isset($row['total_daily_sales']) ? $row['total_daily_sales'] : 0;

// Get total monthly sales by user_id
$sql = "SELECT SUM(price) as total_monthly_sales FROM orders WHERE user_id = ? AND YEAR(order_date) = YEAR(CURDATE()) AND MONTH(order_date) = MONTH(CURDATE())";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = $result->fetch_assoc();
$totalMonthlySales = isset($row['total_monthly_sales']) ? $row['total_monthly_sales'] : 0;

// Calculate total income for the month
$month_income = 0;
$curr_month = date('m');
foreach ($sales_data as $sale) {
    $sale_month = date('m', strtotime($sale['date']));
    if ($sale_month == $curr_month) {
        $month_income += $sale['total_price'];
    }
}

// Encode data as JSON for use in JavaScript
$json_data = json_encode($sales_data);

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>DarkPan - Bootstrap 5 Admin Template</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php include('/admin/include/header.php')?>
</head>

<body>
    <div class="page-content   ">
        <!-- Spinner Start -->
        <div id="spinner"
            class="show bg-dark position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->
        <!-- Sidebar Start -->
        <?php include('../admin/include/sidebar.php')?>
        <!-- Sidebar End -->
        <!-- Content Start -->
        <div class="content ">
            <!-- Navbar Start -->
            <?php include('../admin/include/navbar.php')?>
            <!-- Navbar End -->
            <!-- Sale & Revenue Start -->
            <div class=" pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-white rounded d-flex align-items-center justify-content-between p-4">
                            <i class="fa fa-chart-line fa-3x text-primary"></i>
                            <div class="ms-3">
                                <p class="mb-2">Today Sale</p>
                                <h6 class="mb-0">$<?php echo $totalPrice; ?></h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-white rounded d-flex align-items-center justify-content-between p-4">
                            <i class="fa fa-chart-bar fa-3x text-primary"></i>
                            <div class="ms-3">
                                <p class="mb-2">Total Orders</p>
                                <h6 class="mb-0"><?php echo $totalOrders; ?></h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-white rounded d-flex align-items-center justify-content-between p-4">
                            <i class="fa fa-chart-area fa-3x text-primary"></i>
                            <div class="ms-3">
                                <p class="mb-2">Today Daily</p>
                                <h6 class="mb-0" id="totalDailySales">$ <?php echo $totalDailySales; ?></h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-white rounded d-flex align-items-center justify-content-between p-4">
                            <i class="fa fa-chart-pie fa-3x text-primary"></i>
                            <div class="ms-3">
                                <p class="mb-2">Total Month</p>
                                <h6 class="mb-0" id="totalMonthlySales">$<?php echo $totalMonthlySales; ?></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Sale & Revenue End -->


            <!-- Sales Chart Start -->
            <div class=" pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-12 col-xl-12">
                        <div class="chartone text-center rounded p-4">
                            <div class=" d-flex align-items-center justify-content-between mb-4">
                                <h6 class="mb-0  ">Income summary</h6>
                                <div class="">
                                    <h2 class="m-0 fs-5 fw-normal text-dark">
                                        <?php echo "$" . number_format($month_income, 2); ?>
                                    </h2>
                                    <span class="mb-0 text-muted  ">Total income</span>
                                </div>


                            </div>
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>

                </div>
            </div>






            <!-- Widgets Start -->
            <div class=" pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-12 col-md-6 col-xl-4">
                        <div class="h-100  rounded p-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="mb-0">Messages</h6>
                                <a href="">Show All</a>
                            </div>
                            <div class="d-flex align-items-center border-bottom py-3">
                                <img class="rounded-circle flex-shrink-0" src="img/user.jpg" alt=""
                                    style="width: 40px; height: 40px;">
                                <div class="w-100 ms-3">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-0">Jhon Doe</h6>
                                        <small>15 minutes ago</small>
                                    </div>
                                    <span>Short message goes here...</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center border-bottom py-3">
                                <img class="rounded-circle flex-shrink-0" src="img/user.jpg" alt=""
                                    style="width: 40px; height: 40px;">
                                <div class="w-100 ms-3">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-0">Jhon Doe</h6>
                                        <small>15 minutes ago</small>
                                    </div>
                                    <span>Short message goes here...</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center border-bottom py-3">
                                <img class="rounded-circle flex-shrink-0" src="img/user.jpg" alt=""
                                    style="width: 40px; height: 40px;">
                                <div class="w-100 ms-3">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-0">Jhon Doe</h6>
                                        <small>15 minutes ago</small>
                                    </div>
                                    <span>Short message goes here...</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center pt-3">
                                <img class="rounded-circle flex-shrink-0" src="img/user.jpg" alt=""
                                    style="width: 40px; height: 40px;">
                                <div class="w-100 ms-3">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-0">Jhon Doe</h6>
                                        <small>15 minutes ago</small>
                                    </div>
                                    <span>Short message goes here...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-xl-4">
                        <div class="h-100  rounded p-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <h6 class="mb-0">Calender</h6>
                                <a href="">Show All</a>
                            </div>
                            <div id="calender"></div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-xl-4">
                        <div class="h-100  rounded p-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <h6 class="mb-0">To Do List</h6>
                                <a href="">Show All</a>
                            </div>
                            <div class="d-flex mb-2">
                                <input class="form-control bg-dark border-0" type="text" placeholder="Enter task">
                                <button type="button" class="btn btn-primary ms-2">Add</button>
                            </div>
                            <div class="d-flex align-items-center border-bottom py-2">
                                <input class="form-check-input m-0" type="checkbox">
                                <div class="w-100 ms-3">
                                    <div class="d-flex w-100 align-items-center justify-content-between">
                                        <span>Short task goes here...</span>
                                        <button class="btn btn-sm"><i class="fa fa-times"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center border-bottom py-2">
                                <input class="form-check-input m-0" type="checkbox">
                                <div class="w-100 ms-3">
                                    <div class="d-flex w-100 align-items-center justify-content-between">
                                        <span>Short task goes here...</span>
                                        <button class="btn btn-sm"><i class="fa fa-times"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center border-bottom py-2">
                                <input class="form-check-input m-0" type="checkbox" checked>
                                <div class="w-100 ms-3">
                                    <div class="d-flex w-100 align-items-center justify-content-between">
                                        <span><del>Short task goes here...</del></span>
                                        <button class="btn btn-sm text-primary"><i class="fa fa-times"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center border-bottom py-2">
                                <input class="form-check-input m-0" type="checkbox">
                                <div class="w-100 ms-3">
                                    <div class="d-flex w-100 align-items-center justify-content-between">
                                        <span>Short task goes here...</span>
                                        <button class="btn btn-sm"><i class="fa fa-times"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center pt-2">
                                <input class="form-check-input m-0" type="checkbox">
                                <div class="w-100 ms-3">
                                    <div class="d-flex w-100 align-items-center justify-content-between">
                                        <span>Short task goes here...</span>
                                        <button class="btn btn-sm"><i class="fa fa-times"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Widgets End -->


            <!-- Footer Start -->
            <div class=" pt-4 px-4">
                <div class=" rounded-top p-4">
                    <div class="row">
                        <div class="col-12 col-sm-6 text-center text-sm-start">
                            &copy; <a href="#">Your Site Name</a>, All Right Reserved.
                        </div>
                        <div class="col-12 col-sm-6 text-center text-sm-end">
                            <!--/*** This template is free as long as you keep the footer author’s credit link/attribution link/backlink. If you'd like to use the template without the footer author’s credit link/attribution link/backlink, you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". Thank you for your support. ***/-->
                            Designed By <a href="https://htmlcodex.com">HTML Codex</a>
                            <br>Distributed By: <a href="https://themewagon.com" target="_blank">ThemeWagon</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer End -->
        </div>
        <!-- Content End -->


        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <?php include('../admin/include/javascript.php')?>
    <script>
    const salesData = <?php echo $json_data; ?>;
    const labels = salesData.map(p => p.date);
    const sales = salesData.map(p => ({
        x: p.date,
        y: p.total_price,
        currency: p.currency
    }));

    const ctx = document.getElementById('salesChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Income',
                data: sales,
                fill: true, // Fill the area under the line
                backgroundColor: 'rgba(75, 192, 192, 0.2)', // Set the background color with 20% opacity
                // backgroundColor: '#ffff',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2
            }]
        },
        options: {
            scales: {
                x: {
                    grid: {
                        display: false // Remove vertical grid lines
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value, index, values) {
                            const currency = sales[index] && sales[index].currency ? sales[index].currency :
                                'USD';
                            return currency + value.toLocaleString(); // add currency symbol to the left
                        },
                        padding: 10, // add padding to the left of the tick label
                    },
                    grid: {
                        display: true, // Show horizontal grid lines
                        borderColor: 'rgba(0, 0, 0, 0)', // Set the border color to transparent
                        color: 'rgba(0, 0, 0, 0.1)', // Set the grid line color with 10% opacity
                    },
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const currency = context.raw.currency ? context.raw.currency : 'USD';
                            return 'Income: ' + currency + context.raw.y.toLocaleString();
                        }
                    }
                }
            }
        }
    });
    </script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>

</html>