<?php
include('../admin/db.php');
include('../admin/include/userid.php');

$order_id = isset($_GET['id']) ? $_GET['id'] : null;
$search = isset($_POST['search']) ? $_POST['search'] : null;

// Fetch the order details from the database using the provided search parameter
function getOrderDetails($link, $search)
{
    $query = "SELECT * FROM orders WHERE id = ? OR email = ? OR name = ? OR order_id = ?";

    $stmt = $link->prepare($query);

    $stmt->bind_param("ssss", $search, $search, $search,$search);

    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    // Check if a result was found, otherwise return a message
    if ($data) {
        return $data;
    } else {
        return [];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order = getOrderDetails($link, $search);
}

// Fetch the order details from the database using the provided order ID
function getOrderDetailsById($link, $order_id)
{
    $stmt = $link->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

if ($order_id) {
    $order = getOrderDetailsById($link, $order_id);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>DarkPan - Bootstrap 5 Admin Template</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">
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
        <!-- Content Start -->
        <div class="content w-100 align-items-center ">
            <!-- Navbar Start -->
            <?php include('../admin/include/navbar.php')?>
            <!-- Navbar End -->
            <!-- Sale & Revenue Start -->
            <div class="  pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-12 col-xl-12 p-0 m-0">
                        <div class="products   p-4">
                            <div class="section-header-back">
                                <a href="products.html" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
                            </div>
                            <h2>Orders</h2>

                        </div>
                    </div>

                </div>
            </div>
            <!-- Sale & Revenue End -->
            <div class="manage card-with pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-12 col-xl-12">
                        <div class="chartone text-center rounded p-4">
                            <div class="card-add d-flex   mb-4">
                                <h6 class="text-start mb-0   "> Order
                                    <span><?php if (!empty($order)) : ?><?php echo htmlspecialchars($order['order_id'], ENT_QUOTES); ?></span>
                                </h6>

                                <span
                                    class="status"><?php echo htmlspecialchars($order['status'], ENT_QUOTES); ?></span>

                            </div>
                            <?php else: ?>

                            <?php endif; ?>
                            <hr>
                            <?php if (!empty($order)) : ?>
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>Order ID</th>
                                        <td><?php echo htmlspecialchars($order['order_id'], ENT_QUOTES); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Name</th>
                                        <td><?php echo htmlspecialchars($order['name'], ENT_QUOTES); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><?php echo htmlspecialchars($order['email'], ENT_QUOTES); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Product</th>
                                        <td><?php echo htmlspecialchars($order['product_name'], ENT_QUOTES); ?></td>
                                    </tr>

                                    <tr>
                                        <th>transaction_id</th>
                                        <td><?php echo htmlspecialchars($order['transaction_id'], ENT_QUOTES); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Channels</th>
                                        <td><?php echo htmlspecialchars($order['channels'], ENT_QUOTES); ?></td>
                                    </tr>
                                    <tr>
                                        <th>adult</th>
                                        <td><?php echo htmlspecialchars($order['adult'], ENT_QUOTES); ?></td>
                                    </tr>
                                    <th>Status</th>
                                    <td><?php echo htmlspecialchars($order['status'], ENT_QUOTES); ?></td>
                                    </tr>

                                    <tr>
                                        <th>Amount</th>
                                        <td><?php echo htmlspecialchars($order['price'] . ' ' . $order["currency"], ENT_QUOTES); ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>PayPal Fees</th>
                                        <td><?php echo htmlspecialchars($order['payment_fee'], ENT_QUOTES); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Profit</th>
                                        <td><?php echo htmlspecialchars($order['profit'], ENT_QUOTES); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Created At</th>
                                        <td><?php echo htmlspecialchars($order['order_date'], ENT_QUOTES); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <?php else: ?>
                            <p class="text-center">No order found.</p>
                            <?php endif; ?>
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
    <?php include('../admin/include/javascript.php')

     
     ?>
</body>

</html>