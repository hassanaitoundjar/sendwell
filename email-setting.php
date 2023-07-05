<?php
include ('./admin/db.php');
include ('./admin/include/userid.php');
// Check if the user is not logged in, redirect to the login page with an error message
include('./admin/include/checker-uesr.php');


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Load the config file
    $config = include '../admin/phpmailer.php';
    $email_config = $config['email'];

    // Update the email configuration
    $email_config['host'] = $_POST['host'];
    $email_config['username'] = $_POST['username'];
    $email_config['password'] = $_POST['password'];
    $email_config['port'] = (int)$_POST['port'];
    $email_config['admin_email'] = $_POST['admin_email'];
    $email_config['admin_name'] = $_POST['admin_name'];

    try {
        // Save the updated configuration
        $config_data = "<?php\n\nreturn " . var_export(['email' => $email_config], true) . ";\n";
        file_put_contents('../admin/phpmailer.php', $config_data);

        // Redirect with success parameter
        header("Location: email-setting.php?success=1");
    } catch (Exception $e) {
        // Redirect with error parameter
        header("Location: email-setting.php?error=1");
    }
} else {
    $config = include '../admin/phpmailer.php';
    $email_config = $config['email'];
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


include('/admin/include/header.php')

     
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
            <div class=" pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-12 col-xl-12">
                        <div class=" d-flex align-items-center justify-content-between p-4">
                            <!-- <i class="fa fa-chart-line fa-3x text-primary"></i> -->
                            <!-- <div class="ms-3">
                                <p class="mb-2">Today Sale</p>
                                <h6 class="mb-0">$1234</h6>
                            </div> -->
                        </div>
                    </div>

                </div>
            </div>
            <!-- Sale & Revenue End -->


            <!-- Sales Chart Start -->
            <div class=" pt-4 px-4">

                <div class="row g-4">
                    <div class="profile ">
                        <div class="col-sm-12 col-xl-12">

                            <h2 class="text-muted">Configuration</h2>
                        </div>

                    </div>
                </div>
            </div>


            <div class="card-with pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-12 col-xl-12">
                        <div class="chartone text-center rounded p-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <h6 class="mb-0  ">Change Email Configuration</h6>

                            </div>
                            <?php if (isset($_GET['success'])): ?>
                            <div class="alert alert-success">
                                <strong>Success!</strong> Email configuration updated successfully.
                            </div>
                            <?php endif; ?>
                            <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger">
                                <strong>Error!</strong> There was an error updating the email configuration.
                            </div>

                            <?php endif; ?>

                            <form class="update mb-3" id="formAuthentication"
                                action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" accept-charset="UTF-8"
                                method="post">
                                <div class="mb-3   form-row">
                                    <label for="host">Host</label>
                                    <input type="text" class="form-control" id="host" name="host"
                                        value="<?php echo htmlspecialchars($email_config['host'], ENT_QUOTES); ?>"
                                        required>

                                </div>
                                <hr>
                                <div class="mb-3  form-row">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" id="username" name="username"
                                        value="<?php echo htmlspecialchars($email_config['username'], ENT_QUOTES); ?>"
                                        required>

                                </div>

                                <hr>
                                <div class="mb-3  form-row">
                                    <label for="password">Password:</label>
                                    <input type="password" class="form-control" id="password" name="password"
                                        value="<?php echo htmlspecialchars($email_config['password'], ENT_QUOTES); ?>"
                                        required>

                                </div>
                                <hr>
                                <div class="mb-3  form-row">
                                    <label for="port">Port</label>
                                    <input type="number" class="form-control" id="port" name="port"
                                        value="<?php echo htmlspecialchars($email_config['port'], ENT_QUOTES); ?>"
                                        required>

                                </div>
                                <hr>
                                <div class="mb-3  form-row">
                                    <label for="admin_email">Admin Email</label>
                                    <input type="email" class="form-control" id="admin_email" name="admin_email"
                                        value="<?php echo htmlspecialchars($email_config['admin_email'], ENT_QUOTES); ?>"
                                        required>

                                </div>
                                <hr>
                                <div class="mb-3  form-row">
                                    <label for="admin_name">Admin Name</label>
                                    <input type="text" class="form-control" id="admin_name" name="admin_name"
                                        value="<?php echo htmlspecialchars($email_config['admin_name'], ENT_QUOTES); ?>"
                                        required>

                                </div>
                                <hr>
                                <button type="submit"
                                    class="d-flex justify-content-start btn btn-primary">Update</button>

                                <!-- <button type="submit" name="update_data"
                                    class=" d-flex justify-content-start align-items-center btn btn-primary">Update
                                </button> -->





                            </form>
                        </div>
                    </div>

                </div>
            </div>


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
</body>

</html>