<?php
include './admin/db.php';
include ('./admin/include/userid.php');
// Check if the user is not logged in, redirect to the login page with an error message
include('./admin/include/checker-uesr.php');

$user_id = $_SESSION['user_id'];

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit"])) {
    $id = test_input($_POST["id"]);
    $name = test_input($_POST["name"]);
    $price = test_input($_POST["price"]);
    $currency = test_input($_POST["currency"]);

    $sql = "UPDATE products SET name='$name', price='$price', currency='$currency' WHERE id=$id AND user_id=$user_id";

    if (mysqli_query($link, $sql)) {
        echo "Data updated successfully";
    } else {
        echo "Error updating data: " . mysqli_error($link);
    }
}

if (isset($_GET["delete"])) {
    $id = test_input($_GET["delete"]);

    $sql = "DELETE FROM products WHERE id=$id AND user_id=$user_id";

    if (mysqli_query($link, $sql)) {
    } else {
        echo "Error deleting data: " . mysqli_error($link);
    }
}

$sql = "SELECT * FROM products WHERE user_id=$user_id";
$result = mysqli_query($link, $sql);

if (mysqli_num_rows($result) == 0) {
    header("Location: add_product");
    exit();
}

if (isset($_GET["search"])) {
    $search = test_input($_GET["search"]);

    if (strlen($search) < 3 || strlen($search) > 50) {
    } else {
        $sql = "SELECT * FROM products WHERE name LIKE '%$search%' AND user_id=$user_id";
        $result = mysqli_query($link, $sql);

    }
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
    <?php


include('./admin/include/header.php')

     
     ?>
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
        <?php


include('./admin/include/sidebar.php')

     
     ?>
        <!-- Sidebar End -->


        <!-- Content Start -->
        <div class="content w-100 align-items-center ">
            <!-- Navbar Start -->
            <?php


include('./admin/include/navbar.php')

     
     ?>
            <!-- Navbar End -->


            <!-- Sale & Revenue Start -->
            <div class="  pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-12 col-xl-12">
                        <div class="products bg-white rounded   p-4">
                            <h2>Products</h2>
                            <a href="add_product.php" class="btn btn-primary">Add New</a>

                        </div>
                    </div>

                </div>
            </div>
            <!-- Sale & Revenue End -->
            <div class="card-with pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-12 col-xl-12">
                        <div class="chartone text-center rounded p-4">
                            <div class="card-add  mb-4">
                                <h6 class="mb-0  ">All Products</h6>

                                <form class=" searchproduct d-flex">
                                    <input class=" form-control me-2" name="search" id="search" type="search"
                                        placeholder="Search" aria-label="Search">
                                    <i class="bi bi-search"></i>
                                </form>
                            </div>
                            <?php if (isset($_GET["search"]) && (strlen($search) < 3 || strlen($search) > 50)): ?>
                            <div class="alert alert-danger" role="alert">
                                Search must be between 3 and 50 characters long
                            </div>
                            <?php endif; ?>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                            <table class=" tabel styled-table ">
                                <thead>
                                    <tr>
                                        <th scope="col">Name Product</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Create at</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>


                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td>
                                            <a
                                                href="https://iptvsmartersproo.com/checkout/product.php?id=<?php echo $row['id']; ?>&name=<?php echo urlencode(strtolower(str_replace(' ', '-', $row['name']))); ?>">
                                                <?php echo $row["name"]; ?>
                                            </a>
                                        </td>

                                        <td><?php echo $row["price"] . ' ' . $row["currency"]; ?></td>
                                        <td><?php echo $row["created_at"]; ?></td>
                                        <td>
                                            <div class="action-links">
                                                <a href="#" data-bs-toggle="modal"
                                                    data-bs-target="#editModal<?php echo $row['id']; ?>"
                                                    class="edit">Edit</a>

                                                <a href="#" class="delete" data-id="<?php echo $row['id']; ?>"
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal">Delete</a>


                                            </div>
                                        </td>

                                    </tr>
                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1"
                                        aria-labelledby="editModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title"
                                                        id="editModalLabel<?php echo $row['id']; ?>">Edit Product</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>"
                                                    method="post">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="id"
                                                            value="<?php echo $row['id']; ?>">
                                                        <div class="mb-3">
                                                            <label for="name<?php echo $row['id']; ?>"
                                                                class="form-label">Product Name</label>
                                                            <input type="text" class="form-control"
                                                                id="name<?php echo $row['id']; ?>" name="name"
                                                                value="<?php echo $row['name']; ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="price<?php echo $row['id']; ?>"
                                                                class="form-label">Product Price</label>
                                                            <input type="number" step="0.01" class="form-control"
                                                                id="price<?php echo $row['id']; ?>" name="price"
                                                                value="<?php echo $row['price']; ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="currency<?php echo $row['id']; ?>"
                                                                class="form-label">Currency</label>
                                                            <input type="text" class="form-control"
                                                                id="currency<?php echo $row['id']; ?>" name="currency"
                                                                value="<?php echo $row['currency']; ?>" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" name="edit" class="btn btn-primary">Save
                                                            changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal" tabindex="-1"
                                        aria-labelledby="deleteModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel">Delete Product</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete this product?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cancel</button>
                                                    <a href="#" class="btn btn-danger" id="confirmDelete">Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endwhile; ?>
                                </tbody>

                            </table>
                            <?php else: ?>
                            <div class="alert alert-warning" role="alert">
                                No products found for search query "<?php echo $search ?>"
                            </div>
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
    <?php


include('./admin/include/javascript.php')

     
     ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var deleteModal = document.getElementById('deleteModal');
        var confirmDelete = document.getElementById('confirmDelete');

        deleteModal.addEventListener('show.bs.modal', function(event) {
            var deleteLink = event.relatedTarget;
            var productId = deleteLink.getAttribute('data-id');
            confirmDelete.href = 'products.php?delete=' + productId;
        });
    });
    </script>



</body>




</body>

</html>