<?php
include ('../admin/db.php');
include ('../admin/include/userid.php');

$user_id = $_SESSION['user_id'];

function getOrders($link, $user_id, $page, $ordersPerPage)
{
    $offset = ($page - 1) * $ordersPerPage;
    $orders = array();
    $sql = "SELECT * FROM orders WHERE user_id=$user_id LIMIT $offset, $ordersPerPage";
    $result = $link->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
    }
    return $orders;
}

function getTotalOrders($link, $user_id)
{
    $sql = "SELECT COUNT(*) as totalOrders FROM orders WHERE user_id=$user_id";
    $result = $link->query($sql);
    $row = $result->fetch_assoc();
    return $row['totalOrders'];
}

function updateOrderStatus($link, $orderId, $status)
{
    $stmt = $link->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("ss", $status, $orderId);
    $stmt->execute();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id']) && isset($_POST['status'])) {
    $orderId = $_POST['order_id'];
    $status = $_POST['status'];

    updateOrderStatus($link, $orderId, $status);
}

function getProductNames($link, $user_id)
{
    $productNames = array();
    $sql = "SELECT DISTINCT product_name FROM orders WHERE user_id=$user_id";
    $result = $link->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $productNames[] = $row['product_name'];
        }
    }
    return $productNames;
}

function getFilteredOrders($link, $user_id, $startDate, $endDate, $productName)
{
    $orders = array();
    $sql = "SELECT * FROM orders WHERE user_id=$user_id AND order_date >= '$startDate' AND order_date <= '$endDate' AND product_name='$productName'";
    $result = $link->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
    }
    return $orders;
}

function downloadOrders($orders) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="orders.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, array('ID', 'User ID', 'Product Name', 'Order Date', 'Status'));

    foreach ($orders as $order) {
        fputcsv($output, $order);
    }

    fclose($output);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['download_orders'])) {
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    $productName = $_POST['product_name'];

    $filteredOrders = getFilteredOrders($link, $user_id, $startDate, $endDate, $productName);
    downloadOrders($filteredOrders);
}

$productNames = getProductNames($link, $user_id);
$ordersPerPage = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$totalOrders = getTotalOrders($link, $user_id);
$totalPages = ceil($totalOrders / $ordersPerPage);

$orders = getOrders($link, $user_id, $page, $ordersPerPage);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>DarkPan - Bootstrap 5 Admin Template</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">
    <?php include('../admin/include/header.php')?>
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
        <?php include('../admin/include/sidebar.php') ?>
        <!-- Sidebar End -->


        <!-- Content Start -->
        <div class="content w-100 align-items-center ">
            <!-- Navbar Start -->
            <?php include('../admin/include/navbar.php')?>
            <!-- Navbar End -->
            <div class="  pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-12 col-xl-12">
                        <div class="products     p-4">
                            <h2>My orders</h2>


                        </div>
                    </div>

                </div>
            </div>
            <div class="  pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-12 col-xl-12">
                        <div class="products bg-white  rounded   p-4">
                            <form method="post" action="">
                                <label for="start_date">Start Date:</label>
                                <input type="date" name="start_date" id="start_date" required>
                                <label for="end_date">End Date:</label>
                                <input type="date" name="end_date" id="end_date" required>
                                <label for="product_name">Product Name:</label>
                                <select name="product_name" id="product_name" required>
                                    <?php foreach ($productNames as $productName): ?>
                                    <option value="<?php echo htmlspecialchars($productName); ?>">
                                        <?php echo htmlspecialchars($productName); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="submit" class="btn btn-primary" name="download_orders"
                                    value="Download Orders">
                            </form>


                        </div>
                    </div>

                </div>
            </div>
            <!-- Sale & Revenue End -->
            <div class="card-with pt-4 px-4">
                <div class="row b-3 g-4">
                    <div class="col-sm-12 col-xl-12 p-0 m-0">
                        <div class="chartone text-center rounded p-4 mt-4">
                            <div class="card-add  mb-4">
                                <h6 class="mb-0  ">All Orders</h6>

                                <form class=" searchproduct d-flex" method="POST" action="order_details.php">
                                    <input class=" form-control me-2" type="text" name="search" id="search"
                                        placeholder="Search by order...." aria-label="Search">
                                    <i class="bi bi-search"></i>
                                </form>



                            </div>
                            <table class=" tabel styled-table ">
                                <thead>
                                    <tr>
                                        <th scope="col">Order ID</th>
                                        <th scope="col">item</th>
                                        <th scope="col">Buyer</th>
                                        <th scope="col">Data</th>
                                        <th scope="col">Status</th>
                                        <th class="Amount" scope="col">Amount</th>
                                    </tr>
                                </thead>
                                <?php
                                    $orders = getOrders($link, $user_id, $page, $ordersPerPage);
                                    foreach ($orders as $order) {
                                ?>
                                <tr>
                                    <td>
                                        <a
                                            href="http://localhost/dark/frontend/order_details.php?id=<?php echo htmlspecialchars($order['id'], ENT_QUOTES); ?>">
                                            <?php echo htmlspecialchars($order['order_id'], ENT_QUOTES); ?>
                                        </a>
                                    </td>

                                    <td><?php echo htmlspecialchars($order['product_name'], ENT_QUOTES); ?></td>
                                    <td><?php echo htmlspecialchars($order['name'], ENT_QUOTES); ?></td>
                                    <td><?php echo htmlspecialchars($order['order_date'], ENT_QUOTES); ?></td>
                                    <td>
                                        <form method="post" action="">
                                            <input type="hidden" name="order_id"
                                                value="<?php echo htmlspecialchars($order['id'], ENT_QUOTES); ?>">
                                            <select name="status" onchange="this.form.submit()"
                                                id="status-<?php echo htmlspecialchars($order['order_id'], ENT_QUOTES); ?>">
                                                <option value="new"
                                                    id="default-<?php echo htmlspecialchars($order['order_id'], ENT_QUOTES); ?>"
                                                    <?php echo $order['status'] == 'new' ? 'selected' : ''; ?>>New
                                                </option>
                                                <option value="processing"
                                                    <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>
                                                    Processing
                                                </option>
                                                <option value="completed"
                                                    <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>
                                                    Completed
                                                </option>
                                                <option value="canceled"
                                                    <?php echo $order['status'] == 'canceled' ? 'selected' : ''; ?>>
                                                    Canceled
                                                </option>
                                            </select>
                                        </form>
                                    </td>
                                    <td><?php echo htmlspecialchars($order['price']. ' ' . $order["currency"], ENT_QUOTES); ?>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            </table>

                            <!-- Pagination -->
                            <nav class="page" aria-label="Page navigation example">
                                <ul class="pagination">
                                    <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>"><a
                                            class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>

                        </div>

                    </div>

                </div>

            </div>
            <!-- Sales Chart End -->







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

    <!-- JavaScript Libraries -->
    <?php


include('../admin/include/javascript.php')

     
     ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const orderIds = <?php echo json_encode(array_column($orders, 'select')); ?>;
        orderIds.forEach(function(orderId) {
            const selectElement = document.getElementById('status-' + orderId);
            const defaultOption = document.getElementById('default-' + orderId);
            if (selectElement.value !== 'new') {
                defaultOption.style.display = 'none';
            }
            selectElement.addEventListener('change', function() {
                if (this.value !== 'new') {
                    defaultOption.style.display = 'none';
                } else {
                    defaultOption.style.display = 'block';
                }
            });
        });
    });
    </script>

    <script>
    document.getElementById('download-csv').addEventListener('click', function() {
        window.location.href = 'export_orders.php?format=csv';
    });
    </script>
</body>

</html>