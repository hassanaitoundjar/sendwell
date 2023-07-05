<?php
include ('./admin/db.php');
include ('./admin/include/userid.php');
// Check if the user is not logged in, redirect to the login page with an error message
include('./admin/include/checker-uesr.php');


$currencies = array("USD", "EUR", "GBP", "JPY");

$nameErr = $priceErr = $currencyErr = $descriptionErr = $nameExistsErr = "";
$name = $price = $currency = $description = "";
$checkoutUrl = "";
$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
    } else {
        $name = test_input($_POST["name"]);
    }

    if (empty($_POST["price"])) {
        $priceErr = "Price is required";
    } else {
        $price = test_input($_POST["price"]);
    }

    if (empty($_POST["currency"])) {
        $currencyErr = "Currency is required";
    } else {
        $currency = test_input($_POST["currency"]);
    }

    if (empty($_POST["description"])) {
        $descriptionErr = "Description is required";
    } else {
        $description = test_input($_POST["description"]);
    }

    if ($name && $price && $currency && $description) {
        if (!checkProductNameExists($name, $link)) {
            $price = sprintf("%.2f", $price);

            $sql = "INSERT INTO products (name, price, description, currency, checkout_url, created_at, user_id) VALUES (?, ?, ?, ?, ?, NOW(), ?)";

            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "sssssi", $name, $price, $description, $currency, $checkoutUrl, $user_id);

                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['status'] = "Data inserted successfully";
                    $_SESSION['status_type'] = "success";
                } else {
                    $_SESSION['status'] = "Error inserting data: " . mysqli_error($link);
                    $_SESSION['status_type'] = "error";
                }

                mysqli_stmt_close($stmt);
            }

            mysqli_close($link);
        } else {
            $nameExistsErr = "Product name already exists.";
            $_SESSION['status'] = $nameExistsErr;
            $_SESSION['status_type'] = "error";
        }
    }
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function checkProductNameExists($name, $link) {
    $sql = "SELECT id FROM products WHERE name = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "s", $name);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    return $row !== null;
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


include('../admin/include/sidebar.php')

     
     ?>
        <!-- Sidebar End -->


        <!-- Content Start -->
        <div class="content w-100 align-items-center ">
            <!-- Navbar Start -->
            <?php


include('../admin/include/navbar.php')

     
     ?>

            <!-- Navbar End -->


            <!-- Sale & Revenue Start -->
            <div class="  pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-12 col-xl-12">
                        <div class="products bg-white rounded   p-4">
                            <div class="section-header-back">
                                <a href="products.php" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
                            </div>
                            <h2>Add Product</h2>

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
                                <h6 class="mb-0  ">New Product</h6>



                            </div>
                            <form class="update" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
                                method="POST">
                                <?php if (isset($_SESSION['status'])): ?>
                                <div class="alert <?php echo ($_SESSION['status_type'] == 'error') ? 'alert-danger' : 'alert-success'; ?> alert-dismissible fade show"
                                    role="alert">
                                    <?php echo $_SESSION['status']; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                                <?php
                                            unset($_SESSION['status']);
                                            unset($_SESSION['status_type']);
                                        endif;
                                    ?>

                                <div class="d-block mb-3  form-row">
                                    <label for="exampleInputEmail1" class="form-label">Product Name <span
                                            class="required-icon"> *</span></label>
                                    <input type="text" class="form-control" id="name" placeholder="Enter product name"
                                        name="name" aria-describedby="emailHelp" required>
                                    <span class="error"><?php echo $nameErr; ?></span>

                                </div>

                                <div class="d-block mb-3  form-row">
                                    <label for="exampleInputEmail1" class="form-label">Product Description <span
                                            class="required-icon"> *</span></label>
                                    <textarea class="form-control" id="description"
                                        placeholder="Enter product description" name="description"
                                        id="floatingTextarea2" required></textarea>
                                    <span class="error"><?php echo $descriptionErr; ?></span>
                                </div>
                                <div class="d-block mb-3  form-row">
                                    <label for="exampleInputEmail1" class="form-label">Currency <span
                                            class="required-icon"> *</span></label>
                                    <select name="currency" class="form-select" aria-label=".form-select-sm example"
                                        required>

                                        <?php
                                            foreach ($currencies as $currency) {
                                                echo "<option value='$currency'>$currency</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="d-block mb-3  form-row">
                                    <label for="exampleInputEmail1" class="form-label">Price <span
                                            class="required-icon"> *</span></label>
                                    <input type="text" class="form-control" type="number" step="0.01"
                                        class="form-control" id="price" placeholder="Enter product price" name="price"
                                        aria-describedby="emailHelp" required>
                                    <span class="error"><?php echo $descriptionErr; ?></span>

                                </div>



                                <div class="account">

                                    <button type="submit" type="button" class="btn btn-primary">Submit </button>
                                </div>



                            </form>
                        </div>

                    </div>

                </div>
            </div>
            <!-- Sales Chart End -->







            <!-- Footer Start -->
            <?php


include('../admin/include/header.php')

     
     ?>
            <!-- Footer End -->
        </div>
        <!-- Content End -->


        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <?php


include('../admin/include/javascript.php')

     
     ?>
</body>

</html>